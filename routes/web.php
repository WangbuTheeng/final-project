<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ClassSectionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\BulkMarksController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\MarksheetController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\CollegeSettingController;
use App\Http\Controllers\ReportsController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Test route for testing Tailwind and Alpine.js
Route::get('/test', function() {
    return view('test');
});

// Test route for mobile layout
Route::get('/test-mobile', function() {
    return view('test-mobile');
});

// Test route for responsive design
Route::get('/test-responsive', function() {
    return view('test-responsive');
})->middleware('auth');

// Test route for college settings
Route::get('/test-college-settings', function() {
    $settings = \App\Models\CollegeSetting::getSettings();
    return response()->json($settings);
});

// Test route to create/update college settings
Route::get('/test-update-college-settings', function() {
    $settings = \App\Models\CollegeSetting::getSettings();

    $updated = $settings->update([
        'college_name' => 'Bajra International College',
        'college_address' => 'Kathmandu, Nepal',
        'college_website' => 'www.bajracollege.edu.np',
        'college_phone' => '+977-1-4444444',
        'college_email' => 'info@bajracollege.edu.np',
        'marksheet_settings' => [
            'show_logo' => true,
            'show_signatures' => true,
            'show_issue_date' => true,
            'show_grading_scale' => false,
            'show_qr_code' => false,
            'watermark_text' => 'OFFICIAL'
        ]
    ]);

    return response()->json([
        'updated' => $updated,
        'settings' => $settings->fresh()
    ]);
});

// Test form for college settings
Route::get('/test-form-college-settings', function() {
    return view('test-college-settings');
});

Auth::routes();

// Original home route - redirects to dashboard for backward compatibility
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Dashboard routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/modern', function(App\Services\DashboardService $dashboardService) {
    $user = Auth::user();
    $dashboardData = $dashboardService->getDashboardData($user);
    return view('dashboard-modern', compact('dashboardData'));
})->name('dashboard.modern');
Route::get('/api/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->name('recent');
        Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/statistics', [App\Http\Controllers\NotificationController::class, 'getStatistics'])->name('statistics');
        Route::post('/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-multiple-read', [App\Http\Controllers\NotificationController::class, 'markMultipleAsRead'])->name('mark-multiple-read');
        Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/bulk-delete', [App\Http\Controllers\NotificationController::class, 'destroyMultiple'])->name('destroy-multiple');

        // Test route (only in local environment)
        Route::post('/test', [App\Http\Controllers\NotificationController::class, 'test'])->name('test');

        // Placeholder for notification settings (to be implemented)
        Route::get('/settings', function() {
            return view('placeholder', [
                'title' => 'Notification Settings',
                'message' => 'Notification settings feature coming soon'
            ]);
        })->name('settings');
    });

    // Activity routes
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [App\Http\Controllers\ActivityController::class, 'index'])->name('index');
        Route::get('/recent', [App\Http\Controllers\ActivityController::class, 'getRecent'])->name('recent');
        Route::get('/timeline', [App\Http\Controllers\ActivityController::class, 'getTimeline'])->name('timeline');
        Route::get('/statistics', [App\Http\Controllers\ActivityController::class, 'getStatistics'])->name('statistics');
        Route::get('/user/{user?}', [App\Http\Controllers\ActivityController::class, 'getUserActivities'])->name('user');
        Route::get('/user/{user}/statistics', [App\Http\Controllers\ActivityController::class, 'getUserStatistics'])->name('user.statistics');
        Route::get('/export', [App\Http\Controllers\ActivityController::class, 'export'])->name('export');
        Route::post('/cleanup', [App\Http\Controllers\ActivityController::class, 'cleanup'])->name('cleanup')->middleware('can:manage-system');
    });

    // Placeholder routes for quick actions (to be implemented)
    Route::get('/courses', function() { return view('placeholder', ['title' => 'Courses', 'message' => 'Course management coming soon']); })->name('courses.index');
    Route::get('/courses/create', function() { return view('placeholder', ['title' => 'Create Course', 'message' => 'Course creation coming soon']); })->name('courses.create');
    Route::get('/enrollments', function() { return view('placeholder', ['title' => 'Enrollments', 'message' => 'Enrollment management coming soon']); })->name('enrollments.index');
    Route::get('/settings', function() { return view('placeholder', ['title' => 'Settings', 'message' => 'System settings coming soon']); })->name('settings.index');
    Route::get('/import', function() { return view('placeholder', ['title' => 'Bulk Import', 'message' => 'Data import system coming soon']); })->name('import.index');

    // Marks management placeholder
    Route::get('/marks', function() { return view('placeholder', ['title' => 'Marks Management', 'message' => 'Marks management coming soon']); })->name('marks.index');

    // Performance monitoring routes (admin only)
    Route::middleware(['can:manage-system'])->prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [App\Http\Controllers\PerformanceController::class, 'index'])->name('index');
        Route::get('/realtime-metrics', [App\Http\Controllers\PerformanceController::class, 'getRealtimeMetrics'])->name('realtime-metrics');
        Route::get('/cache-stats', [App\Http\Controllers\PerformanceController::class, 'getCacheStats'])->name('cache-stats');
        Route::get('/database-analysis', [App\Http\Controllers\PerformanceController::class, 'analyzeDatabase'])->name('database-analysis');
        Route::get('/report', [App\Http\Controllers\PerformanceController::class, 'generateReport'])->name('report');
        Route::post('/optimize', [App\Http\Controllers\PerformanceController::class, 'optimize'])->name('optimize');
        Route::post('/clear-cache', [App\Http\Controllers\PerformanceController::class, 'clearCache'])->name('clear-cache');
        Route::post('/metrics', [App\Http\Controllers\PerformanceController::class, 'storeMetrics'])->name('store-metrics');
    });

    // Security monitoring routes (admin only)
    Route::middleware(['can:manage-system'])->prefix('security')->name('security.')->group(function () {
        Route::get('/', [App\Http\Controllers\SecurityController::class, 'index'])->name('index');
        Route::post('/scan', [App\Http\Controllers\SecurityController::class, 'runSecurityScan'])->name('scan');
        Route::post('/block-ip', [App\Http\Controllers\SecurityController::class, 'blockIP'])->name('block-ip');
        Route::post('/unblock-ip', [App\Http\Controllers\SecurityController::class, 'unblockIP'])->name('unblock-ip');
        Route::post('/force-logout-all', [App\Http\Controllers\SecurityController::class, 'forceLogoutAll'])->name('force-logout-all');
        Route::get('/report', [App\Http\Controllers\SecurityController::class, 'generateReport'])->name('report');
    });

    // Profile Routes
    Route::get('profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/password', [App\Http\Controllers\ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Global Search Routes
    Route::get('search', [GlobalSearchController::class, 'search'])->name('global.search');
    Route::get('search/results', [GlobalSearchController::class, 'results'])->name('search.results');
    // User Management Routes
    Route::middleware(['role:Super Admin|Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users-search', [UserController::class, 'search'])->name('users.search');
    });
    
    // Role Management Routes
    Route::middleware(['permission:view-roles'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Permission Management Routes
    Route::middleware(['permission:view-permissions'])->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Activity Logs Route (Super Admin only)
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{id}', [App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::get('activity-logs-export', [App\Http\Controllers\ActivityLogController::class, 'export'])->name('activity-logs.export');
    });
    
    // Academic Year Routes
    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::resource('academic-years', AcademicYearController::class);
        Route::put('academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])
            ->name('academic-years.set-current');
        Route::put('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive'])
            ->name('academic-years.set-active');
    });

    // College Settings Routes
    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::get('college-settings', [CollegeSettingController::class, 'index'])->name('college-settings.index');
        Route::put('college-settings', [CollegeSettingController::class, 'update'])->name('college-settings.update');
        Route::post('college-settings/delete-file', [CollegeSettingController::class, 'deleteFile'])->name('college-settings.delete-file');
    });

    // Grading System Routes
    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::resource('grading-systems', App\Http\Controllers\GradingSystemController::class);
        Route::patch('grading-systems/{gradingSystem}/set-default', [App\Http\Controllers\GradingSystemController::class, 'setDefault'])
            ->name('grading-systems.set-default');
        Route::patch('grading-systems/{gradingSystem}/toggle-status', [App\Http\Controllers\GradingSystemController::class, 'toggleStatus'])
            ->name('grading-systems.toggle-status');
    });

    // Faculty Routes
    Route::middleware(['permission:manage-faculties'])->group(function () {
        Route::resource('faculties', FacultyController::class)->except(['index', 'show']);
    });

    // Faculty view routes (accessible by teachers and admins)
    Route::middleware(['permission:view-faculties|manage-faculties'])->group(function () {
        Route::get('faculties', [FacultyController::class, 'index'])->name('faculties.index');
        Route::get('faculties/{faculty}', [FacultyController::class, 'show'])->name('faculties.show');
    });

    // Department Routes
    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::resource('departments', DepartmentController::class);
        Route::get('faculties/{faculty}/departments', [DepartmentController::class, 'getByFaculty'])
            ->name('departments.by-faculty');
    });

    // Course Routes
    Route::middleware(['permission:manage-courses|view-courses'])->group(function () {
        Route::resource('courses', CourseController::class);
    });



    // Class Section Routes
    Route::middleware(['permission:manage-classes'])->group(function () {
        Route::resource('classes', ClassSectionController::class)->except(['index', 'show']);
        Route::post('classes/{class}/assign-instructor', [ClassSectionController::class, 'assignInstructor'])
            ->name('classes.assign-instructor');
    });

    // Class view routes (accessible by teachers and admins)
    Route::middleware(['permission:view-classes|manage-classes'])->group(function () {
        Route::get('classes', [ClassSectionController::class, 'index'])->name('classes.index');
        Route::get('classes/{class}', [ClassSectionController::class, 'show'])->name('classes.show');
    });

    // Subject Routes
    Route::middleware(['permission:manage-courses|view-subjects'])->group(function () {
        Route::resource('subjects', SubjectController::class);
        Route::get('classes/{class}/subjects', [SubjectController::class, 'getByClass'])
            ->name('subjects.by-class');
        Route::get('subjects/next-order-sequence', [SubjectController::class, 'getNextOrderSequenceAjax'])
            ->name('subjects.next-order-sequence');
        Route::get('subjects/generate-code', [SubjectController::class, 'generateSubjectCodeSuggestion'])
            ->name('subjects.generate-code');
    });

    // Student Management Routes
    Route::middleware(['permission:manage-students|view-students'])->group(function () {
        Route::resource('students', StudentController::class);
    });

    // Teacher Management Routes
    Route::middleware(['permission:manage-teachers|view-teachers'])->group(function () {
        Route::resource('teachers', TeacherController::class);
    });

    // AJAX Routes for Students (less restrictive middleware)
    Route::middleware(['auth'])->group(function () {
        Route::get('students/departments-by-faculty/{facultyId}', [StudentController::class, 'getDepartmentsByFaculty'])
            ->name('students.departments-by-faculty');
    });

    // AJAX Routes for Enrollment (hierarchical selection)
    Route::middleware(['auth'])->group(function () {
        Route::get('ajax/faculties', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getFaculties'])
            ->name('ajax.faculties');
        Route::get('ajax/courses/by-faculty', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getCoursesByFaculty'])
            ->name('ajax.courses.by-faculty');
        Route::get('ajax/classes/by-course', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getClassesByCourse'])
            ->name('ajax.classes.by-course');
        Route::get('ajax/students', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getStudents'])
            ->name('ajax.students');
        Route::get('ajax/exams', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getExams'])
            ->name('ajax.exams');

        // Additional enrollment filter routes
        Route::get('ajax/enrollment/courses', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getCoursesByFaculty'])
            ->name('ajax.enrollment.courses');
    });

    // Enrollment Management Routes
    Route::middleware(['permission:manage-enrollments'])->group(function () {
        // Bulk enrollment routes must come before resource routes to avoid conflicts
        Route::get('enrollments/bulk-create', [EnrollmentController::class, 'bulkCreate'])
            ->name('enrollments.bulk-create');
        Route::post('enrollments/bulk-store', [EnrollmentController::class, 'bulkStore'])
            ->name('enrollments.bulk-store');

        // Resource routes
        Route::resource('enrollments', EnrollmentController::class)->except(['edit', 'update']);

        // Additional enrollment routes
        Route::post('enrollments/{enrollment}/drop', [EnrollmentController::class, 'drop'])
            ->name('enrollments.drop');

        // Test route for enrollment creation
        Route::get('test-enrollment', function() {
            return 'Enrollment routes are working';
        });
    });
    
    // Exam Management Routes (Admin/Examiner only)
    Route::middleware(['permission:manage-exams'])->group(function () {
        Route::resource('exams', ExamController::class)->except(['index', 'show']);
        Route::get('exams/{exam}/grades', [ExamController::class, 'grades'])
            ->name('exams.grades');
        Route::post('exams/{exam}/grades', [ExamController::class, 'storeGrades'])
            ->name('exams.grades.store');
        Route::get('exams/subjects/by-class', [ExamController::class, 'getSubjects'])
            ->name('exams.subjects.by-class');
        Route::get('exams/subject-marks', [ExamController::class, 'getSubjectMarks'])
            ->name('exams.subject-marks');
        Route::get('exams/class-marks', [ExamController::class, 'getClassMarks'])
            ->name('exams.class-marks');
    });

    // Exam View Routes (accessible by teachers and admins)
    Route::middleware(['permission:view-exams|manage-exams'])->group(function () {
        Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
    });

    // Bulk Marks Entry Routes
    Route::middleware(['permission:manage-exams'])->group(function () {
        Route::get('bulk-marks', [BulkMarksController::class, 'index'])
            ->name('bulk-marks.index');
        Route::get('bulk-marks/create', [BulkMarksController::class, 'create'])
            ->name('bulk-marks.create');
        Route::post('bulk-marks', [BulkMarksController::class, 'store'])
            ->name('bulk-marks.store');
    });

    // Grade Routes (temporarily without permission middleware for testing)
    Route::resource('grades', GradeController::class)->only(['index', 'show']);
    Route::get('grades/create', [GradeController::class, 'create'])
        ->name('grades.create');
    Route::post('grades', [GradeController::class, 'store'])
        ->name('grades.store');
    Route::get('grades/bulk-entry', [GradeController::class, 'bulkEntry'])
        ->name('grades.bulk-entry');
    Route::post('grades/bulk-store', [GradeController::class, 'storeBulk'])
        ->name('grades.bulk-store');
    Route::get('grades/student/{student}/report', [GradeController::class, 'studentReport'])
        ->name('grades.student-report');
    Route::get('grades/subjects/by-class', [GradeController::class, 'getSubjects'])
        ->name('grades.subjects.by-class');

    // Marks Entry Routes
    Route::middleware(['permission:manage-exams'])->group(function () {
        Route::get('marks', [MarkController::class, 'index'])->name('marks.index');
        Route::post('marks/search', [MarkController::class, 'search'])->name('marks.search');
        // Redirect GET requests to marks/search back to marks index
        Route::get('marks/search', function() {
            return redirect()->route('marks.index')->with('info', 'Please use the search form to find exams for marks entry.');
        });
        Route::post('marks/bulk-store', [MarkController::class, 'storeBulk'])->name('marks.store-bulk');
        Route::get('marks/classes-by-course', [MarkController::class, 'getClassesByCourse'])->name('marks.classes-by-course');
        Route::get('marks/exams-by-class', [MarkController::class, 'getExamsByClass'])->name('marks.exams-by-class');
    });

    // Marksheet Generation Routes
    Route::middleware(['permission:manage-exams'])->group(function () {
        Route::get('marksheets', [MarksheetController::class, 'index'])->name('marksheets.index');
        Route::get('marksheets/exam/{exam}/student/{student}', [MarksheetController::class, 'generate'])->name('marksheets.generate');
        Route::get('marksheets/exam/{exam}/student/{student}/pdf', [MarksheetController::class, 'generatePdf'])->name('marksheets.generate-pdf');
        Route::get('marksheets/exam/{exam}/bulk', [MarksheetController::class, 'generateBulk'])->name('marksheets.bulk');
        Route::get('marksheets/students-by-exam', [MarksheetController::class, 'getStudentsByExam'])->name('marksheets.students-by-exam');
    });

    // Result Management Routes
    Route::middleware(['permission:manage-exams|view-exams|view-grades'])->group(function () {
        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::get('results/exam/{exam}/generate', [ResultController::class, 'generate'])->name('results.generate');
        Route::get('results/exam/{exam}/pdf', [ResultController::class, 'generatePdf'])->name('results.generate-pdf');
        Route::get('results/exam/{exam}/student/{student}/marksheet', [ResultController::class, 'studentMarksheet'])->name('results.student-marksheet');
        Route::get('results/exam/{exam}/student/{student}/marksheet/pdf', [ResultController::class, 'studentMarksheetPdf'])->name('results.student-marksheet-pdf');
        Route::post('results/exam/{exam}/bulk-generate', [ResultController::class, 'bulkGenerate'])->name('results.bulk-generate');
    });
    
    // Finance Routes
    Route::middleware(['role_or_permission:Super Admin|view-finances'])->prefix('finance')->name('finance.')->group(function () {
        // Dashboard
        Route::get('/', [FinanceController::class, 'dashboard'])->name('dashboard');

        // Fee Routes
        Route::get('/fees', [FinanceController::class, 'indexFees'])->name('fees.index');
        Route::get('/fees/create', [FinanceController::class, 'createFee'])->name('fees.create')->middleware('permission:manage-fees');
        Route::post('/fees', [FinanceController::class, 'storeFee'])->name('fees.store')->middleware('permission:manage-fees');
        Route::get('/fees/{fee}', [FinanceController::class, 'showFee'])->name('fees.show');
        Route::get('/fees/{fee}/edit', [FinanceController::class, 'editFee'])->name('fees.edit')->middleware('permission:manage-fees');
        Route::put('/fees/{fee}', [FinanceController::class, 'updateFee'])->name('fees.update')->middleware('permission:manage-fees');
        Route::delete('/fees/{fee}', [FinanceController::class, 'destroyFee'])->name('fees.destroy')->middleware('permission:manage-fees');

        // Invoice Routes
        Route::get('/invoices', [FinanceController::class, 'indexInvoices'])->name('invoices.index');
        Route::get('/invoices/create', [FinanceController::class, 'createInvoice'])->name('invoices.create')->middleware('permission:create-invoices');
        Route::post('/invoices', [FinanceController::class, 'storeInvoice'])->name('invoices.store')->middleware('permission:create-invoices');
        Route::get('/invoices/{invoice}', [FinanceController::class, 'showInvoice'])->name('invoices.show');
        Route::get('/invoices/{invoice}/print', [FinanceController::class, 'printInvoice'])->name('invoices.print');
        Route::post('/invoices/{invoice}/status', [FinanceController::class, 'updateInvoiceStatus'])->name('invoices.update-status')->middleware('permission:manage-invoices');
        Route::get('/get-applicable-fees', [FinanceController::class, 'getApplicableFees'])->name('get-applicable-fees');
        Route::get('/get-student-courses-classes', [FinanceController::class, 'getStudentCoursesAndClasses'])->name('get-student-courses-classes');
        Route::get('/get-student-invoices', [FinanceController::class, 'getStudentInvoices'])->name('get-student-invoices');
        Route::get('/get-student-outstanding-balance', [FinanceController::class, 'getStudentOutstandingBalance'])->name('get-student-outstanding-balance');

        // Payment Routes
        Route::get('/payments', [FinanceController::class, 'indexPayments'])->name('payments.index');
        Route::get('/payments/create', [FinanceController::class, 'createPayment'])->name('payments.create')->middleware('permission:create-payments');
        Route::post('/payments', [FinanceController::class, 'storePayment'])->name('payments.store')->middleware('permission:create-payments');
        Route::get('/payments/{payment}', [FinanceController::class, 'showPayment'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [FinanceController::class, 'verifyPayment'])->name('payments.verify')->middleware('permission:verify-payments');
        Route::post('/payments/{payment}/cancel', [FinanceController::class, 'cancelPayment'])->name('payments.cancel')->middleware('permission:manage-payments');

        // Teacher Routes
        Route::get('/teachers', [FinanceController::class, 'indexTeachers'])->name('teachers.index')->middleware('permission:manage-salaries');
        Route::get('/teachers/create', [FinanceController::class, 'createTeacher'])->name('teachers.create')->middleware('permission:manage-salaries');
        Route::post('/teachers', [FinanceController::class, 'storeTeacher'])->name('teachers.store')->middleware('permission:manage-salaries');
        Route::get('/teachers/{teacher}', [FinanceController::class, 'showTeacher'])->name('teachers.show')->middleware('permission:manage-salaries');
        Route::get('/teachers/{teacher}/edit', [FinanceController::class, 'editTeacher'])->name('teachers.edit')->middleware('permission:manage-salaries');
        Route::put('/teachers/{teacher}', [FinanceController::class, 'updateTeacher'])->name('teachers.update')->middleware('permission:manage-salaries');

        // Salary Payment Routes
        Route::get('/salaries', [FinanceController::class, 'indexSalaryPayments'])->name('salaries.index')->middleware('permission:manage-salaries');
        Route::get('/salaries/create', [FinanceController::class, 'createSalaryPayment'])->name('salaries.create')->middleware('permission:manage-salaries');
        Route::post('/salaries', [FinanceController::class, 'storeSalaryPayment'])->name('salaries.store')->middleware('permission:manage-salaries');
        Route::get('/salaries/bulk-process', [FinanceController::class, 'bulkProcessSalaries'])->name('salaries.bulk-process')->middleware('permission:manage-salaries');
        Route::post('/salaries/bulk', [FinanceController::class, 'bulkSalaryPayment'])->name('salaries.bulk-store')->middleware('permission:manage-salaries');
        Route::get('/salaries/{salaryPayment}', [FinanceController::class, 'showSalaryPayment'])->name('salaries.show')->middleware('permission:manage-salaries');
        Route::post('/salaries/{salaryPayment}/approve', [FinanceController::class, 'approveSalaryPayment'])->name('salaries.approve')->middleware('permission:manage-salaries');
        Route::get('/salaries/export', [FinanceController::class, 'exportSalaries'])->name('salaries.export')->middleware('permission:view-financial-reports');
        Route::get('/salaries/export', [FinanceController::class, 'exportSalaries'])->name('salaries.export')->middleware('permission:view-financial-reports');

        // Expense Routes
        Route::get('/expenses', [FinanceController::class, 'indexExpenses'])->name('expenses.index');
        Route::get('/expenses/create', [FinanceController::class, 'createExpense'])->name('expenses.create')->middleware('permission:manage-expenses');
        Route::post('/expenses', [FinanceController::class, 'storeExpense'])->name('expenses.store')->middleware('permission:manage-expenses');
        Route::get('/expenses/{expense}', [FinanceController::class, 'showExpense'])->name('expenses.show');
        Route::get('/expenses/{expense}/edit', [FinanceController::class, 'editExpense'])->name('expenses.edit')->middleware('permission:manage-expenses');
        Route::put('/expenses/{expense}', [FinanceController::class, 'updateExpense'])->name('expenses.update')->middleware('permission:manage-expenses');
        Route::delete('/expenses/{expense}', [FinanceController::class, 'destroyExpense'])->name('expenses.destroy')->middleware('permission:manage-expenses');
        Route::post('/expenses/{expense}/approve', [FinanceController::class, 'approveExpense'])->name('expenses.approve')->middleware('permission:approve-expenses');
        Route::post('/expenses/{expense}/reject', [FinanceController::class, 'rejectExpense'])->name('expenses.reject')->middleware('permission:approve-expenses');
        Route::get('/expenses/analytics', [FinanceController::class, 'expenseAnalytics'])->name('expenses.analytics')->middleware('permission:view-financial-reports');

        // Financial Reports Routes
        Route::get('/reports', [FinanceController::class, 'indexReports'])->name('reports.index')->middleware('permission:view-financial-reports');
        Route::get('/reports/student-statement', [FinanceController::class, 'studentFeeStatement'])->name('reports.student-statement')->middleware('permission:view-financial-reports');
        Route::get('/reports/payment-report', [FinanceController::class, 'paymentReport'])->name('reports.payment-report')->middleware('permission:view-financial-reports');
        Route::get('/reports/outstanding-dues', [FinanceController::class, 'outstandingDuesReport'])->name('reports.outstanding-dues')->middleware('permission:view-financial-reports');
        Route::get('/reports/salary-report', [FinanceController::class, 'salaryReport'])->name('reports.salary-report')->middleware('permission:view-financial-reports');
        Route::get('/reports/student-statement/export', [FinanceController::class, 'exportStudentStatement'])->name('reports.export-student-statement')->middleware('permission:view-financial-reports');
    });
});

Route::get('/get-course-type', [ClassSectionController::class, 'getCourseType'])->name('getCourseType');

// Reports Routes
Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportsController::class, 'index'])->name('index');
    Route::get('/students', [ReportsController::class, 'studentReports'])->name('students');
    Route::get('/academic', [ReportsController::class, 'academicReports'])->name('academic');
    Route::get('/teachers', [ReportsController::class, 'teacherReports'])->name('teachers');
    Route::get('/enrollments', [ReportsController::class, 'enrollmentReports'])->name('enrollments');
    Route::get('/system', [ReportsController::class, 'systemReports'])->name('system');
    Route::get('/students/export', [ReportsController::class, 'exportStudents'])->name('export-students');
});

// Debug route
Route::get('/debug/api-test', function() {
    return view('debug.api-test');
})->middleware('auth');

// Test route for dashboard chart data
Route::get('/test-dashboard-data', function() {
    $controller = new \App\Http\Controllers\DashboardController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getChartData');
    $method->setAccessible(true);
    
    $chartData = $method->invoke($controller);
    
    return response()->json($chartData);
})->middleware('auth');


// AJAX API routes for enrollment (moved from api.php to avoid CSRF issues)
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/courses', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getCourses']);
    Route::get('/students', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getStudents']);
    Route::get('/classes/by-courses', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getClassesByCourses']);
    Route::get('/classes/available', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getAvailableClasses']);
    Route::get('/enrollment/eligibility', [App\Http\Controllers\Api\EnrollmentApiController::class, 'checkEligibility']);
    Route::get('/enrollment/stats', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getEnrollmentStats']);
    Route::get('/faculties', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getFaculties']);
    Route::get('/courses-by-faculty', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getCoursesByFaculty']);

    // Course and Class creation endpoints
    Route::post('/courses', [App\Http\Controllers\Api\EnrollmentApiController::class, 'createCourse']);
    Route::post('/classes', [App\Http\Controllers\Api\EnrollmentApiController::class, 'createClass']);
});
