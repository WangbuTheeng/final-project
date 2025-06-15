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
use App\Http\Controllers\GradeController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GlobalSearchController;

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
    
    // Academic Year Routes
    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::resource('academic-years', AcademicYearController::class);
        Route::put('academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])
            ->name('academic-years.set-current');
        Route::put('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive'])
            ->name('academic-years.set-active');
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
    Route::middleware(['permission:view-courses'])->group(function () {
        Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    });

    Route::middleware(['permission:create-courses'])->group(function () {
        Route::get('courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
    });

    Route::middleware(['permission:edit-courses'])->group(function () {
        Route::get('courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::patch('courses/{course}', [CourseController::class, 'update']);
    });

    Route::middleware(['permission:delete-courses'])->group(function () {
        Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    // Class Section Routes
    Route::middleware(['permission:view-classes'])->group(function () {
        Route::get('classes', [ClassSectionController::class, 'index'])->name('classes.index');
        Route::get('classes/{classSection}', [ClassSectionController::class, 'show'])->name('classes.show');
    });

    Route::middleware(['permission:create-classes'])->group(function () {
        Route::get('classes/create', [ClassSectionController::class, 'create'])->name('classes.create');
        Route::post('classes', [ClassSectionController::class, 'store'])->name('classes.store');
    });

    Route::middleware(['permission:edit-classes'])->group(function () {
        Route::get('classes/{classSection}/edit', [ClassSectionController::class, 'edit'])->name('classes.edit');
        Route::put('classes/{classSection}', [ClassSectionController::class, 'update'])->name('classes.update');
        Route::patch('classes/{classSection}', [ClassSectionController::class, 'update']);
        Route::post('classes/{classSection}/assign-instructor', [ClassSectionController::class, 'assignInstructor'])
            ->name('classes.assign-instructor');
    });

    Route::middleware(['permission:delete-classes'])->group(function () {
        Route::delete('classes/{classSection}', [ClassSectionController::class, 'destroy'])->name('classes.destroy');
    });

    // Subject Routes
    Route::middleware(['permission:view-courses'])->group(function () {
        Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
        Route::get('classes/{class}/subjects', [SubjectController::class, 'getByClass'])
            ->name('subjects.by-class');
    });

    Route::middleware(['permission:create-courses'])->group(function () {
        Route::get('subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
        Route::post('subjects', [SubjectController::class, 'store'])->name('subjects.store');
    });

    Route::middleware(['permission:edit-courses'])->group(function () {
        Route::get('subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
        Route::put('subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::patch('subjects/{subject}', [SubjectController::class, 'update']);
    });

    Route::middleware(['permission:delete-courses'])->group(function () {
        Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    });

    // Student Management Routes
    Route::middleware(['permission:view-students'])->group(function () {
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
    });

    Route::middleware(['permission:create-students'])->group(function () {
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('students', [StudentController::class, 'store'])->name('students.store');
    });

    Route::middleware(['permission:edit-students'])->group(function () {
        Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::patch('students/{student}', [StudentController::class, 'update']);
    });

    Route::middleware(['permission:delete-students'])->group(function () {
        Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });

    // Enrollment Management Routes
    Route::middleware(['permission:view-enrollments'])->group(function () {
        Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    });

    Route::middleware(['permission:create-enrollments'])->group(function () {
        Route::get('enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::get('enrollments/bulk-create', [EnrollmentController::class, 'bulkCreate'])
            ->name('enrollments.bulk-create');
        Route::post('enrollments/bulk-store', [EnrollmentController::class, 'bulkStore'])
            ->name('enrollments.bulk-store');
    });

    Route::middleware(['permission:edit-enrollments'])->group(function () {
        Route::post('enrollments/{enrollment}/drop', [EnrollmentController::class, 'drop'])
            ->name('enrollments.drop');
    });

    Route::middleware(['permission:delete-enrollments'])->group(function () {
        Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    });
    
    // Exam Routes
    Route::middleware(['permission:view-exams'])->group(function () {
        Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
        Route::get('exams/upcoming', [ExamController::class, 'upcoming'])->name('exams.upcoming');
    });

    Route::middleware(['permission:create-exams'])->group(function () {
        Route::get('exams/create', [ExamController::class, 'create'])->name('exams.create');
        Route::post('exams', [ExamController::class, 'store'])->name('exams.store');
    });

    Route::middleware(['permission:edit-exams'])->group(function () {
        Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
        Route::put('exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
        Route::patch('exams/{exam}', [ExamController::class, 'update']);
        Route::post('exams/{exam}/start', [ExamController::class, 'start'])->name('exams.start');
        Route::post('exams/{exam}/complete', [ExamController::class, 'complete'])->name('exams.complete');
        Route::post('exams/{exam}/cancel', [ExamController::class, 'cancel'])->name('exams.cancel');
    });

    Route::middleware(['permission:delete-exams'])->group(function () {
        Route::delete('exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');
    });

    // Grade/Result Routes
    Route::middleware(['permission:view-exams'])->group(function () {
        Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('grades/{grade}', [GradeController::class, 'show'])->name('grades.show');
        Route::get('exams/{exam}/grades/create', [GradeController::class, 'createForExam'])->name('grades.create-for-exam');
        Route::get('students/{student}/results', [GradeController::class, 'studentResults'])->name('grades.student-results');
        Route::get('exams/{exam}/results', [GradeController::class, 'examResults'])->name('grades.exam-results');
        Route::get('result-sheet', [GradeController::class, 'resultSheet'])->name('grades.result-sheet');
    });

    Route::middleware(['permission:create-exams'])->group(function () {
        Route::post('exams/{exam}/grades', [GradeController::class, 'storeForExam'])->name('grades.store-for-exam');
    });

    Route::middleware(['permission:edit-exams'])->group(function () {
        Route::get('grades/{grade}/edit', [GradeController::class, 'edit'])->name('grades.edit');
        Route::put('grades/{grade}', [GradeController::class, 'update'])->name('grades.update');
        Route::patch('grades/{grade}', [GradeController::class, 'update']);
        Route::post('grades/calculate-final', [GradeController::class, 'calculateFinalGrades'])->name('grades.calculate-final');
    });

    Route::middleware(['permission:delete-exams'])->group(function () {
        Route::delete('grades/{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');
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
