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

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Protected routes
Route::middleware(['auth'])->group(function () {
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

    // Faculty Routes
    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::resource('faculties', FacultyController::class);
    });

    // Department Routes
    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::resource('departments', DepartmentController::class);
        Route::get('faculties/{faculty}/departments', [DepartmentController::class, 'getByFaculty'])
            ->name('departments.by-faculty');
    });

    // Course Routes
    Route::middleware(['permission:manage-courses'])->group(function () {
        Route::resource('courses', CourseController::class);
    });



    // Class Section Routes
    Route::middleware(['permission:manage-classes'])->group(function () {
        Route::resource('classes', ClassSectionController::class);
        Route::post('classes/{class}/assign-instructor', [ClassSectionController::class, 'assignInstructor'])
            ->name('classes.assign-instructor');
    });

    // Subject Routes
    Route::middleware(['permission:manage-courses'])->group(function () {
        Route::resource('subjects', SubjectController::class);
        Route::get('classes/{class}/subjects', [SubjectController::class, 'getByClass'])
            ->name('subjects.by-class');
        Route::get('subjects/next-order-sequence', [SubjectController::class, 'getNextOrderSequenceAjax'])
            ->name('subjects.next-order-sequence');
        Route::get('subjects/generate-code', [SubjectController::class, 'generateSubjectCodeSuggestion'])
            ->name('subjects.generate-code');
    });

    // Student Management Routes
    Route::middleware(['permission:manage-students'])->group(function () {
        Route::resource('students', StudentController::class);
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
        Route::resource('enrollments', EnrollmentController::class)->except(['edit', 'update']);
        Route::get('enrollments/bulk-create', [EnrollmentController::class, 'bulkCreate'])
            ->name('enrollments.bulk-create');
        Route::post('enrollments/bulk-store', [EnrollmentController::class, 'bulkStore'])
            ->name('enrollments.bulk-store');
        Route::post('enrollments/{enrollment}/drop', [EnrollmentController::class, 'drop'])
            ->name('enrollments.drop');

        // Test route for enrollment creation
        Route::get('test-enrollment', function() {
            return 'Enrollment routes are working';
        });
    });
    
    // Exam Routes
    Route::middleware(['permission:manage-exams'])->group(function () {
        Route::resource('exams', ExamController::class);
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
    Route::middleware(['permission:manage-exams'])->group(function () {
        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::get('results/exam/{exam}/generate', [ResultController::class, 'generate'])->name('results.generate');
        Route::get('results/exam/{exam}/pdf', [ResultController::class, 'generatePdf'])->name('results.generate-pdf');
        Route::get('results/exam/{exam}/student/{student}/marksheet', [ResultController::class, 'studentMarksheet'])->name('results.student-marksheet');
        Route::get('results/exam/{exam}/student/{student}/marksheet/pdf', [ResultController::class, 'studentMarksheetPdf'])->name('results.student-marksheet-pdf');
        Route::post('results/exam/{exam}/bulk-generate', [ResultController::class, 'bulkGenerate'])->name('results.bulk-generate');
    });
    
    // Finance Routes
    Route::middleware(['permission:view-finances'])->prefix('finance')->name('finance.')->group(function () {
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
        Route::post('/invoices/{invoice}/payment', [FinanceController::class, 'processPayment'])->name('invoices.payment')->middleware('permission:process-payments');

        // Fee Statement Routes
        Route::get('/statements/student/{student}', [FinanceController::class, 'studentFeeStatement'])->name('statements.student');
        Route::get('/statements/student/{student}/pdf', [FinanceController::class, 'generateFeeStatementPDF'])->name('statements.student.pdf');
    });
});

Route::get('/get-course-type', [ClassSectionController::class, 'getCourseType'])->name('getCourseType');
