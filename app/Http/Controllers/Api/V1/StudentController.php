<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ActivityService;
use App\Services\DatabaseOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    protected $activityService;
    protected $dbOptimizationService;

    public function __construct(ActivityService $activityService, DatabaseOptimizationService $dbOptimizationService)
    {
        $this->activityService = $activityService;
        $this->dbOptimizationService = $dbOptimizationService;
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
            $filters = $request->only(['status', 'faculty_id', 'department_id', 'search']);

            // Use optimized query
            $students = $this->dbOptimizationService->getOptimizedStudents($perPage, $filters);

            return response()->json([
                'success' => true,
                'data' => [
                    'students' => $students->items(),
                    'pagination' => [
                        'current_page' => $students->currentPage(),
                        'last_page' => $students->lastPage(),
                        'per_page' => $students->perPage(),
                        'total' => $students->total(),
                        'from' => $students->firstItem(),
                        'to' => $students->lastItem(),
                    ],
                    'filters' => $filters,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'admission_date' => 'nullable|date',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user first
            $user = \App\Models\User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'password' => \Hash::make('password123'), // Default password
            ]);

            // Assign student role
            $user->assignRole('student');

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'admission_number' => $this->generateAdmissionNumber(),
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'admission_date' => $request->admission_date ?? now(),
                'status' => 'active',
                'guardian_name' => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'guardian_email' => $request->guardian_email,
            ]);

            // Load relationships
            $student->load(['user', 'faculty', 'department']);

            // Log activity
            $this->activityService->logStudentActivity('create', $student);

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully',
                'data' => [
                    'student' => $this->formatStudent($student)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified student.
     */
    public function show($id)
    {
        try {
            $student = Student::with(['user', 'faculty', 'department', 'enrollments.course'])
                ->findOrFail($id);

            // Log activity
            $this->activityService->logStudentActivity('view', $student);

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => $this->formatStudent($student, true)
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, $id)
    {
        try {
            $student = Student::with(['user'])->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $student->user_id,
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string',
                'faculty_id' => 'sometimes|required|exists:faculties,id',
                'department_id' => 'sometimes|required|exists:departments,id',
                'status' => 'sometimes|required|in:active,inactive,graduated,suspended',
                'guardian_name' => 'nullable|string|max:255',
                'guardian_phone' => 'nullable|string|max:20',
                'guardian_email' => 'nullable|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user data
            $userFields = ['first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender', 'address'];
            $userData = $request->only($userFields);
            if (!empty($userData)) {
                $student->user->update($userData);
            }

            // Update student data
            $studentFields = ['faculty_id', 'department_id', 'status', 'guardian_name', 'guardian_phone', 'guardian_email'];
            $studentData = $request->only($studentFields);
            if (!empty($studentData)) {
                $student->update($studentData);
            }

            // Reload relationships
            $student->load(['user', 'faculty', 'department']);

            // Log activity
            $this->activityService->logStudentActivity('update', $student);

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully',
                'data' => [
                    'student' => $this->formatStudent($student)
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy($id)
    {
        try {
            $student = Student::with(['user'])->findOrFail($id);

            // Log activity before deletion
            $this->activityService->logStudentActivity('delete', $student);

            // Soft delete student
            $student->delete();

            // Optionally deactivate user
            $student->user->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student statistics.
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => Student::count(),
                'active' => Student::where('status', 'active')->count(),
                'graduated' => Student::where('status', 'graduated')->count(),
                'suspended' => Student::where('status', 'suspended')->count(),
                'recent_registrations' => Student::where('created_at', '>=', now()->subDays(30))->count(),
                'by_faculty' => Student::join('faculties', 'students.faculty_id', '=', 'faculties.id')
                    ->select('faculties.name', \DB::raw('count(*) as count'))
                    ->groupBy('faculties.id', 'faculties.name')
                    ->get(),
                'by_status' => Student::select('status', \DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk operations on students.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,graduate,suspend,delete',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $students = Student::whereIn('id', $request->student_ids)->get();
            $action = $request->action;
            $count = 0;

            foreach ($students as $student) {
                switch ($action) {
                    case 'activate':
                        $student->update(['status' => 'active']);
                        break;
                    case 'deactivate':
                        $student->update(['status' => 'inactive']);
                        break;
                    case 'graduate':
                        $student->update(['status' => 'graduated']);
                        break;
                    case 'suspend':
                        $student->update(['status' => 'suspended']);
                        break;
                    case 'delete':
                        $student->delete();
                        break;
                }

                // Log activity
                $this->activityService->logStudentActivity($action, $student);
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk {$action} completed successfully",
                'data' => [
                    'affected_count' => $count,
                    'action' => $action
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search students.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->query;
            $limit = $request->get('limit', 20);

            $students = Student::join('users', 'students.user_id', '=', 'users.id')
                ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
                ->where(function ($q) use ($query) {
                    $q->where('users.first_name', 'like', "%{$query}%")
                      ->orWhere('users.last_name', 'like', "%{$query}%")
                      ->orWhere('users.email', 'like', "%{$query}%")
                      ->orWhere('students.admission_number', 'like', "%{$query}%");
                })
                ->select([
                    'students.id',
                    'students.admission_number',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'faculties.name as faculty_name'
                ])
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'students' => $students,
                    'query' => $query,
                    'count' => $students->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate admission number.
     */
    private function generateAdmissionNumber()
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastStudent ? (int) substr($lastStudent->admission_number, -4) + 1 : 1;
        
        return $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Format student data for API response.
     */
    private function formatStudent($student, $detailed = false)
    {
        $data = [
            'id' => $student->id,
            'admission_number' => $student->admission_number,
            'status' => $student->status,
            'admission_date' => $student->admission_date?->toDateString(),
            'user' => [
                'id' => $student->user->id,
                'first_name' => $student->user->first_name,
                'last_name' => $student->user->last_name,
                'name' => $student->user->name,
                'email' => $student->user->email,
                'phone' => $student->user->phone,
                'date_of_birth' => $student->user->date_of_birth?->toDateString(),
                'gender' => $student->user->gender,
                'address' => $student->user->address,
            ],
            'faculty' => $student->faculty ? [
                'id' => $student->faculty->id,
                'name' => $student->faculty->name,
            ] : null,
            'department' => $student->department ? [
                'id' => $student->department->id,
                'name' => $student->department->name,
            ] : null,
            'created_at' => $student->created_at->toISOString(),
            'updated_at' => $student->updated_at->toISOString(),
        ];

        if ($detailed) {
            $data['guardian'] = [
                'name' => $student->guardian_name,
                'phone' => $student->guardian_phone,
                'email' => $student->guardian_email,
            ];

            if ($student->enrollments) {
                $data['enrollments'] = $student->enrollments->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->id,
                        'course' => [
                            'id' => $enrollment->course->id,
                            'name' => $enrollment->course->name,
                            'code' => $enrollment->course->code,
                        ],
                        'status' => $enrollment->status,
                        'enrolled_at' => $enrollment->created_at->toISOString(),
                    ];
                });
            }
        }

        return $data;
    }
}
