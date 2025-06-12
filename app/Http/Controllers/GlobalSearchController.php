<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Course;
use App\Models\ClassSection;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Global search across all entities
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        $limit = $request->get('limit', 5);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Search Users
        if ($type === 'all' || $type === 'users') {
            $users = $this->searchUsers($query, $limit);
            if (!empty($users)) {
                $results['users'] = [
                    'title' => 'Users',
                    'icon' => 'fas fa-users',
                    'items' => $users,
                    'view_all_url' => route('users.index', ['search' => $query])
                ];
            }
        }

        // Search Students
        if ($type === 'all' || $type === 'students') {
            $students = $this->searchStudents($query, $limit);
            if (!empty($students)) {
                $results['students'] = [
                    'title' => 'Students',
                    'icon' => 'fas fa-user-graduate',
                    'items' => $students,
                    'view_all_url' => route('students.index', ['search' => $query])
                ];
            }
        }

        // Search Faculties
        if ($type === 'all' || $type === 'faculties') {
            $faculties = $this->searchFaculties($query, $limit);
            if (!empty($faculties)) {
                $results['faculties'] = [
                    'title' => 'Faculties',
                    'icon' => 'fas fa-building',
                    'items' => $faculties,
                    'view_all_url' => route('faculties.index', ['search' => $query])
                ];
            }
        }

        // Search Departments
        if ($type === 'all' || $type === 'departments') {
            $departments = $this->searchDepartments($query, $limit);
            if (!empty($departments)) {
                $results['departments'] = [
                    'title' => 'Departments',
                    'icon' => 'fas fa-sitemap',
                    'items' => $departments,
                    'view_all_url' => route('departments.index', ['search' => $query])
                ];
            }
        }

        // Search Courses
        if ($type === 'all' || $type === 'courses') {
            $courses = $this->searchCourses($query, $limit);
            if (!empty($courses)) {
                $results['courses'] = [
                    'title' => 'Courses',
                    'icon' => 'fas fa-book',
                    'items' => $courses,
                    'view_all_url' => route('courses.index', ['search' => $query])
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * Full search results page
     */
    public function results(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        if (strlen($query) < 2) {
            return redirect()->back()->with('error', 'Search query must be at least 2 characters long.');
        }

        $results = [];
        $totalResults = 0;

        // Get comprehensive results for each category
        if ($type === 'all' || $type === 'users') {
            $users = User::with('roles')->search($query)->orderBy('id', 'asc')->paginate(10);
            $results['users'] = $users;
            $totalResults += $users->total();
        }

        if ($type === 'all' || $type === 'students') {
            $students = Student::with(['user', 'department'])
                ->whereHas('user', function ($q) use ($query) {
                    $q->search($query);
                })
                ->orWhere('matric_number', 'LIKE', "%{$query}%")
                ->orderBy('id', 'asc')
                ->paginate(10);
            $results['students'] = $students;
            $totalResults += $students->total();
        }

        return view('search.results', compact('results', 'query', 'type', 'totalResults'));
    }

    private function searchUsers($query, $limit)
    {
        return User::with('roles')
            ->search($query)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'description' => $user->roles->pluck('name')->implode(', '),
                    'url' => route('users.show', $user),
                    'avatar' => strtoupper(substr($user->name, 0, 2)),
                    'type' => 'user'
                ];
            })->toArray();
    }

    private function searchStudents($query, $limit)
    {
        try {
            return Student::with(['user', 'department'])
                ->whereHas('user', function ($q) use ($query) {
                    $q->search($query);
                })
                ->orWhere('matric_number', 'LIKE', "%{$query}%")
                ->orderBy('id', 'asc')
                ->limit($limit)
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'title' => $student->user->name ?? 'Unknown',
                        'subtitle' => $student->matric_number,
                        'description' => $student->department->name ?? 'No Department',
                        'url' => route('students.show', $student),
                        'avatar' => strtoupper(substr($student->user->name ?? 'ST', 0, 2)),
                        'type' => 'student'
                    ];
                })->toArray();
        } catch (\Exception $e) {
            // If students table doesn't exist or has issues, return empty array
            return [];
        }
    }

    private function searchFaculties($query, $limit)
    {
        return Faculty::where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($faculty) {
                return [
                    'id' => $faculty->id,
                    'title' => $faculty->name,
                    'subtitle' => $faculty->code,
                    'description' => $faculty->description,
                    'url' => route('faculties.show', $faculty),
                    'avatar' => strtoupper(substr($faculty->name, 0, 2)),
                    'type' => 'faculty'
                ];
            })->toArray();
    }

    private function searchDepartments($query, $limit)
    {
        return Department::with('faculty')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($department) {
                return [
                    'id' => $department->id,
                    'title' => $department->name,
                    'subtitle' => $department->code,
                    'description' => $department->faculty->name ?? 'No Faculty',
                    'url' => route('departments.show', $department),
                    'avatar' => strtoupper(substr($department->name, 0, 2)),
                    'type' => 'department'
                ];
            })->toArray();
    }

    private function searchCourses($query, $limit)
    {
        return Course::with(['faculty', 'department'])
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'subtitle' => $course->code,
                    'description' => $course->faculty->name ?? 'No Faculty',
                    'url' => route('courses.show', $course),
                    'avatar' => strtoupper(substr($course->title, 0, 2)),
                    'type' => 'course'
                ];
            })->toArray();
    }
}
