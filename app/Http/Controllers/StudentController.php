<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        $this->authorize('view-students');

        $departments = Department::active()->get();
        $faculties = Faculty::active()->get();
        $courses = Course::active()->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        // Get filters
        $selectedDepartment = $request->get('department_id');
        $selectedFaculty = $request->get('faculty_id');
        $selectedCourse = $request->get('course_id');
        $selectedStatus = $request->get('status');
        $selectedAcademicYear = $request->get('academic_year_id');
        $search = $request->get('search');

        // Build query
        $students = Student::with(['user', 'department.faculty', 'faculty', 'academicYear'])
            ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                return $query->where('department_id', $selectedDepartment);
            })
            ->when($selectedFaculty, function ($query) use ($selectedFaculty) {
                return $query->where('faculty_id', $selectedFaculty);
            })
            ->when($selectedCourse, function ($query) use ($selectedCourse) {
                return $query->whereHas('enrollments.class.course', function ($q) use ($selectedCourse) {
                    $q->where('course_id', $selectedCourse);
                });
            })
            ->when($selectedStatus, function ($query) use ($selectedStatus) {
                return $query->where('status', $selectedStatus);
            })
            ->when($selectedAcademicYear, function ($query) use ($selectedAcademicYear) {
                return $query->where('academic_year_id', $selectedAcademicYear);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('admission_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get statistics
        $stats = $this->getStudentStats($selectedDepartment, $selectedFaculty, $selectedStatus);

        return view('students.index', compact(
            'students',
            'departments',
            'faculties',
            'courses',
            'academicYears',
            'selectedDepartment',
            'selectedFaculty',
            'selectedCourse',
            'selectedStatus',
            'selectedAcademicYear',
            'search',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $this->authorize('create-students');

        $faculties = Faculty::with('departments')->where('is_active', true)->get();
        $departments = Department::with('faculty')->active()->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('students.create', compact('faculties', 'departments', 'academicYears'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $this->authorize('create-students');

        $request->validate([
            // User fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string',

            // Student fields
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'current_level' => 'required|in:100,200,300,400,500',
            'mode_of_entry' => 'required|in:entrance_exam,direct_entry,transfer',
            'study_mode' => 'required|in:full_time,part_time,distance',
            
            // Guardian information
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email',
            'guardian_address' => 'nullable|string',
            'guardian_relationship' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'password' => Hash::make('password123'), // Default password
                'role' => 'student',
                'status' => 'active'
            ]);

            // Prepare guardian info
            $guardianInfo = null;
            if ($request->guardian_name) {
                $guardianInfo = [
                    'name' => $request->guardian_name,
                    'phone' => $request->guardian_phone,
                    'email' => $request->guardian_email,
                    'address' => $request->guardian_address,
                    'relationship' => $request->guardian_relationship,
                ];
            }

            // Generate admission number
            $admissionNumber = Student::generateAdmissionNumber($request->academic_year_id, $request->department_id, $request->faculty_id);

            // Calculate expected graduation date
            $academicYear = AcademicYear::find($request->academic_year_id);
            $expectedGraduationDate = null;

            if ($request->department_id) {
                $department = Department::find($request->department_id);
                $expectedGraduationDate = $academicYear->end_date->addYears($department->duration_years);
            } else {
                // Default to 4 years if no department specified
                $expectedGraduationDate = $academicYear->end_date->addYears(4);
            }

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'admission_number' => $admissionNumber,
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'academic_year_id' => $request->academic_year_id,
                'current_level' => $request->current_level,
                'mode_of_entry' => $request->mode_of_entry,
                'study_mode' => $request->study_mode,
                'status' => 'active',
                'expected_graduation_date' => $expectedGraduationDate,
                'guardian_info' => $guardianInfo
            ]);

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully. Default password is "password123".');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating student: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified student
     */
    public function show(Student $student)
    {
        $this->authorize('view-students');

        $student->load(['user', 'department.faculty', 'faculty', 'academicYear']);

        // Get current academic year enrollments
        $currentAcademicYear = AcademicYear::current();
        $currentEnrollments = $student->enrollmentsForSemester($currentAcademicYear->id, 'first')
            ->with(['class.course', 'class.instructor.user'])
            ->get();

        // Get academic history
        $academicHistory = Enrollment::where('student_id', $student->id)
            ->with(['class.course', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('semester', 'desc')
            ->get()
            ->groupBy(['academic_year_id', 'semester']);

        return view('students.show', compact('student', 'currentEnrollments', 'academicHistory'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(Student $student)
    {
        $this->authorize('edit-students');

        $student->load('user');
        $faculties = Faculty::with('departments')->where('is_active', true)->get();
        $departments = Department::with('faculty')->active()->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('students.edit', compact('student', 'faculties', 'departments', 'academicYears'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student)
    {
        $this->authorize('edit-students');

        $request->validate([
            // User fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($student->user_id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string',
            
            // Student fields
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'current_level' => 'required|in:100,200,300,400,500',
            'mode_of_entry' => 'required|in:entrance_exam,direct_entry,transfer',
            'study_mode' => 'required|in:full_time,part_time,distance',
            'status' => 'required|in:active,graduated,suspended,withdrawn,deferred',
            
            // Guardian information
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email',
            'guardian_address' => 'nullable|string',
            'guardian_relationship' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Update user account
            $student->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
            ]);

            // Prepare guardian info
            $guardianInfo = null;
            if ($request->guardian_name) {
                $guardianInfo = [
                    'name' => $request->guardian_name,
                    'phone' => $request->guardian_phone,
                    'email' => $request->guardian_email,
                    'address' => $request->guardian_address,
                    'relationship' => $request->guardian_relationship,
                ];
            }

            // Update student record
            $student->update([
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'current_level' => $request->current_level,
                'mode_of_entry' => $request->mode_of_entry,
                'study_mode' => $request->study_mode,
                'status' => $request->status,
                'guardian_info' => $guardianInfo
            ]);

            DB::commit();

            return redirect()->route('students.show', $student)
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating student: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete-students');

        try {
            DB::beginTransaction();

            // Check if student has active enrollments
            $activeEnrollments = $student->enrollments()->where('status', 'enrolled')->count();
            if ($activeEnrollments > 0) {
                return back()->with('error', 'Cannot delete student with active enrollments.');
            }

            $student->delete();
            $student->user->delete();

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Get student statistics
     */
    private function getStudentStats($departmentId = null, $facultyId = null, $status = null)
    {
        $query = Student::query();

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($facultyId) {
            $query->where('faculty_id', $facultyId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('status', 'active')->count(),
            'graduated' => $query->where('status', 'graduated')->count(),
            'suspended' => $query->where('status', 'suspended')->count(),
            'withdrawn' => $query->where('status', 'withdrawn')->count(),
        ];
    }

}
