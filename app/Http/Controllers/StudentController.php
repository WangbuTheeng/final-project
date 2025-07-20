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
        // Check if user has Super Admin, Admin, or Teacher role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Unauthorized access to Students.');
        }

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
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to create Students.');
        }

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
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to create Students.');
        }

        $request->validate([
            // User fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',

            // Nepal-specific user fields
            'citizenship_number' => 'required|string|max:50|unique:users,citizenship_number',
            'permanent_address' => 'required|string',
            'temporary_address' => 'nullable|string',
            'district' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'religion' => 'nullable|string|max:100',
            'caste_ethnicity' => 'nullable|string|max:100',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',

            // Student academic fields
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'mode_of_entry' => 'required|in:entrance_exam,direct_entry,transfer',

            // Academic background
            'previous_school_name' => 'nullable|string|max:255',
            'slc_see_board' => 'nullable|string|max:100',
            'slc_see_year' => 'nullable|integer|min:2000|max:' . date('Y'),
            'slc_see_marks' => 'nullable|string|max:50',
            'plus_two_board' => 'nullable|string|max:100',
            'plus_two_year' => 'nullable|integer|min:2000|max:' . date('Y'),
            'plus_two_marks' => 'nullable|string|max:50',
            'plus_two_stream' => 'nullable|in:Science,Management,Humanities,Technical,Other',
            'entrance_exam_score' => 'nullable|numeric|min:0|max:100',

            // Family information
            'father_name' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'guardian_citizenship_number' => 'nullable|string|max:50',
            'annual_family_income' => 'nullable|numeric|min:0',

            // Additional information
            'scholarship_info' => 'nullable|string',
            'hostel_required' => 'nullable|boolean',
            'medical_info' => 'nullable|string',
            'preferred_subjects' => 'nullable|string',

            // Guardian information (legacy)
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email',
            'guardian_address' => 'nullable|string',
            'guardian_relationship' => 'nullable|string|max:100',

            // File uploads
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $photoPath = null;
            $documentPaths = [];

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('student-photos', 'public');
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $documentPaths[] = $document->store('student-documents', 'public');
                }
            }

            // Create user account
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'password' => Hash::make('password123'), // Default password
                'role' => 'student',
                'status' => 'active',
                // Nepal-specific fields
                'citizenship_number' => $request->citizenship_number,
                'permanent_address' => $request->permanent_address,
                'temporary_address' => $request->temporary_address,
                'district' => $request->district,
                'province' => $request->province,
                'religion' => $request->religion,
                'caste_ethnicity' => $request->caste_ethnicity,
                'blood_group' => $request->blood_group,
                'country' => 'Nepal'
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
                'mode_of_entry' => $request->mode_of_entry,
                'status' => 'active',
                'expected_graduation_date' => $expectedGraduationDate,
                'guardian_info' => $guardianInfo,
                // Academic background
                'previous_school_name' => $request->previous_school_name,
                'slc_see_board' => $request->slc_see_board,
                'slc_see_year' => $request->slc_see_year,
                'slc_see_marks' => $request->slc_see_marks,
                'plus_two_board' => $request->plus_two_board,
                'plus_two_year' => $request->plus_two_year,
                'plus_two_marks' => $request->plus_two_marks,
                'plus_two_stream' => $request->plus_two_stream,
                'entrance_exam_score' => $request->entrance_exam_score,
                // Family information
                'father_name' => $request->father_name,
                'father_occupation' => $request->father_occupation,
                'mother_name' => $request->mother_name,
                'mother_occupation' => $request->mother_occupation,
                'guardian_citizenship_number' => $request->guardian_citizenship_number,
                'annual_family_income' => $request->annual_family_income,
                // Additional information
                'scholarship_info' => $request->scholarship_info,
                'hostel_required' => $request->hostel_required ?? false,
                'medical_info' => $request->medical_info,
                'preferred_subjects' => $request->preferred_subjects,
                // File paths
                'photo_path' => $photoPath,
                'document_paths' => $documentPaths
            ]);

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($student)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'admission_number' => $admissionNumber,
                    'faculty_name' => $student->faculty->name ?? 'N/A',
                    'department_name' => $student->department->name ?? 'N/A'
                ])
                ->log('Student created successfully');

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
        // Check if user has Super Admin, Admin, or Teacher role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Unauthorized access to view Student details.');
        }

        $student->load(['user', 'department.faculty', 'faculty', 'academicYear']);

        // Get current academic year enrollments
        $currentAcademicYear = AcademicYear::current();
        $currentEnrollments = $student->enrollmentsForAcademicYear($currentAcademicYear->id)
            ->with(['class.course', 'class.instructor.user'])
            ->get();

        // Get academic history
        $academicHistory = Enrollment::where('student_id', $student->id)
            ->with(['class.course', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('academic_year_id');

        return view('students.show', compact('student', 'currentEnrollments', 'academicHistory'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(Student $student)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to edit Students.');
        }

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
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to update Students.');
        }

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
            'mode_of_entry' => 'required|in:entrance_exam,direct_entry,transfer',
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
                'mode_of_entry' => $request->mode_of_entry,
                'status' => $request->status,
                'guardian_info' => $guardianInfo
            ]);

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($student)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'admission_number' => $student->admission_number,
                    'faculty_name' => $student->faculty->name ?? 'N/A',
                    'department_name' => $student->department->name ?? 'N/A'
                ])
                ->log('Student updated successfully');

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
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to delete Students.');
        }

        try {
            DB::beginTransaction();

            // Check if student has active enrollments
            $activeEnrollments = $student->enrollments()->where('status', 'enrolled')->count();
            if ($activeEnrollments > 0) {
                return back()->with('error', 'Cannot delete student with active enrollments.');
            }

            // Log activity before deletion
            activity()
                ->causedBy(auth()->user())
                ->performedOn($student)
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'admission_number' => $student->admission_number,
                    'student_name' => $student->user->name,
                    'faculty_name' => $student->faculty->name ?? 'N/A',
                    'department_name' => $student->department->name ?? 'N/A'
                ])
                ->log('Student deleted');

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
