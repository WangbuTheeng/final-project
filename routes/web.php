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
    Route::middleware(['permission:manage-courses'])->group(function () {
        Route::resource('courses', CourseController::class);
    });

    // Class Section Routes
    Route::middleware(['permission:manage-classes'])->group(function () {
        Route::resource('classes', ClassSectionController::class);
        Route::post('classes/{classSection}/assign-instructor', [ClassSectionController::class, 'assignInstructor'])
            ->name('classes.assign-instructor');
    });

    // Subject Routes
    Route::middleware(['permission:manage-courses'])->group(function () {
        Route::resource('subjects', SubjectController::class);
        Route::get('classes/{class}/subjects', [SubjectController::class, 'getByClass'])
            ->name('subjects.by-class');
    });

    // Student Management Routes
    Route::middleware(['permission:manage-students'])->group(function () {
        Route::resource('students', StudentController::class);
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
    });
    
    // Exam Routes
    Route::middleware(['permission:view-exams'])->group(function () {
        Route::resource('exams', ExamController::class);
        Route::post('exams/{exam}/start', [ExamController::class, 'start'])->name('exams.start');
        Route::post('exams/{exam}/complete', [ExamController::class, 'complete'])->name('exams.complete');
        Route::post('exams/{exam}/cancel', [ExamController::class, 'cancel'])->name('exams.cancel');
        Route::get('exams/upcoming', [ExamController::class, 'upcoming'])->name('exams.upcoming');
    });

    // Grade/Result Routes
    Route::middleware(['permission:view-grades'])->group(function () {
        Route::resource('grades', GradeController::class)->except(['create', 'store']);
        Route::get('exams/{exam}/grades/create', [GradeController::class, 'createForExam'])->name('grades.create-for-exam');
        Route::post('exams/{exam}/grades', [GradeController::class, 'storeForExam'])->name('grades.store-for-exam');
        Route::get('students/{student}/results', [GradeController::class, 'studentResults'])->name('grades.student-results');
        Route::get('exams/{exam}/results', [GradeController::class, 'examResults'])->name('grades.exam-results');
        Route::get('result-sheet', [GradeController::class, 'resultSheet'])->name('grades.result-sheet');
        Route::post('grades/calculate-final', [GradeController::class, 'calculateFinalGrades'])->name('grades.calculate-final');
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
