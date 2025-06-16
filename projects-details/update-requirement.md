Bajra International College Management System - Full Documentation
Project Overview
The Bajra International College Management System is a comprehensive web-based application designed to streamline and automate the administrative, academic, and financial operations of Tribhuvan University (TU) affiliated colleges. Re-architected with a robust Laravel backend and a modern HTML5, JavaScript, and Tailwind CSS frontend, the system provides an integrated platform for managing all aspects of college operations with enhanced control and performance.

Main Objectives
Primary Objectives
Digitalization of College Operations: Transform traditional paper-based processes into efficient digital workflows.

Centralized Data Management: Create a unified database system for all college-related information.

Academic Management: Streamline course management, student enrollment, examination processes, and result generation.

Financial Management: Automate fee collection, payment tracking, and salary management.

Administrative Efficiency: Reduce manual work and improve operational efficiency through automation.

Data Accuracy and Integrity: Ensure consistent and reliable data across all modules.

User-Friendly Interface: Provide intuitive interfaces for different user types.

Scalability: Design system architecture that can grow with institutional needs.

Secondary Objectives
Real-time Reporting: Generate instant reports and analytics.

Audit Trail: Maintain comprehensive logs of all system activities.

Integration Capabilities: Support for future integrations with external systems.

Mobile Responsiveness: Ensure accessibility across different devices.

Security: Implement robust security measures for data protection.

System Architecture
The system now leverages a powerful Laravel backend with a decoupled, traditional web frontend for flexibility and control.

Backend Architecture (Laravel)
MVC Framework: Utilizes Laravel's Model-View-Controller pattern for structured development.

Eloquent ORM: For intuitive database interactions.

RESTful API Layer: Built-in capabilities for creating robust APIs.

Authentication & Authorization: Laravel Breeze/Sanctum for secure user management.

File Management: Native Laravel storage for uploads and organization.

Queue System: For background tasks (e.g., sending emails, processing reports).

Frontend Architecture (HTML5, JavaScript, Tailwind CSS)
Blade Templating: Laravel's powerful templating engine for server-side rendered HTML.

Tailwind CSS: A utility-first CSS framework for rapid and consistent styling.

Vanilla JavaScript: For client-side interactivity, AJAX requests to the Laravel API, and DOM manipulation.

AJAX (Fetch API / Axios): For asynchronous communication with the backend.

Responsive Design: Achieved through Tailwind's utility classes for multi-device support.

System Modules
The system is organized into four primary modules:

1. Academic Management Module
Components:

Faculty Management: Organization of academic departments.

Course Management: Degree program administration.

Class Management: Semester-wise class organization.

Subject Management: Course curriculum and subject details.

Student Management: Student registration and profile management.

Teacher Management: Faculty information and assignment management.

Key Features:

Hierarchical organization (Faculty → Course → Class → Subject).

Student enrollment and class assignment.

Teacher-subject allocation.

Academic calendar management.

2. Examination Management Module
Components:

Exam Creation: Scheduling and organizing examinations.

Marks Entry: Grade recording and management.

Result Generation: Automated result compilation and ranking.

Marksheet Generation: Individual student performance reports.

Key Features:

Multiple exam types (Terminal, Final, Supplementary).

Automated grade calculation.

Result publishing and distribution.

Performance analytics and ranking.

3. Financial Management Module
Components:

Student Finance: Fee management and payment tracking.

Invoice Generation: Automated billing system.

Payment Processing: Multiple payment method support.

Teacher Salary Management: Payroll processing and tracking.

Key Features:

Automated fee calculation.

Payment history and tracking.

Salary computation with allowances and deductions.

Financial reporting and analytics.

4. Administrative Module
Components:

User Profile Management: Personal information management.

Dashboard: Centralized overview and analytics.

Reporting System: Comprehensive report generation.

System Settings: Configuration and customization.

Key Features:

Role-based access control (future enhancement).

Real-time dashboard metrics.

Customizable reports.

System configuration options.

Database Design and Entity Relationships
The system's database schema is designed to manage college data efficiently.

Core Entities
Faculty:

{
  "faculty_name": "string",
  "dean_name": "string",
  "description": "string"
}

Course:

{
  "course_name": "string",
  "course_code": "string",
  "faculty_id": "string",
  "duration_years": "number",
  "total_semesters": "number",
  "description": "string"
}

CollegeClass:

{
  "class_name": "string",
  "course_id": "string",
  "semester": "number",
  "academic_year": "string"
}

Subject:

{
  "subject_name": "string",
  "subject_code": "string",
  "class_id": "string",
  "credit_hours": "number",
  "full_marks": "number",
  "pass_marks": "number",
  "teacher_name": "string"
}

Student:

{
  "student_name": "string",
  "registration_number": "string",
  "roll_number": "string",
  "class_id": "string",
  "admission_year": "string",
  "contact_number": "string",
  "email": "string",
  "address": "string"
}

Teacher:

{
  "teacher_name": "string",
  "employee_id": "string",
  "email": "string",
  "phone": "string",
  "department": "string",
  "position": "string",
  "hire_date": "date",
  "basic_salary": "number",
  "status": "string",
  "bank_account": "string",
  "address": "string"
}

Exam:

{
  "exam_name": "string",
  "course_id": "string",
  "semester": "number",
  "exam_year": "string",
  "exam_type": "string",
  "start_date": "date",
  "end_date": "date",
  "status": "string"
}

ExamResult:

{
  "exam_id": "string",
  "student_id": "string",
  "subject_id": "string",
  "marks_obtained": "number",
  "grade": "string",
  "remarks": "string"
}

Invoice: (Implicitly added based on ERD)

{
  "student_id": "string",
  "amount": "number",
  "status": "string",
  "due_date": "date"
}

Payment: (Implicitly added based on ERD)

{
  "invoice_id": "string",
  "amount": "number",
  "date": "date",
  "method": "string"
}

SalaryPayment: (Implicitly added based on ERD)

{
  "teacher_id": "string",
  "month": "string",
  "salary": "number",
  "status": "string"
}

Key Relationships (Textual Description)
Faculty → Course (1:M): Each faculty can have multiple courses.

Course → CollegeClass (1:M): Each course can have multiple classes.

CollegeClass → Student (1:M): Each class can have multiple students.

CollegeClass → Subject (1:M): Each class can have multiple subjects.

Exam → ExamResult (1:M): Each exam can have multiple results.

Student → ExamResult (1:M): Each student can have multiple exam results.

Subject → ExamResult (1:M): Each subject can have multiple exam results.

Teacher → SalaryPayment (1:M): Each teacher can have multiple salary payments.

Student → Invoice (1:M): Each student can have multiple invoices.

Invoice → Payment (1:M): Each invoice can have multiple payments.

User Roles and Access Control
Current Implementation
The system currently implements a unified access model where all authenticated users have access to all features and modules. This design choice was made to simplify initial deployment and testing, allow maximum flexibility for administrators, and reduce complexity in role management.

Authentication Flow:
User Login → Google OAuth → Laravel Authentication → Full System Access

Potential Role-Based Structure (Future Enhancement)
The system is designed for future implementation of granular role-based access control:

Admin: All Access, System Config, Reports.

Teacher: Students, Exams, Results, Profile.

Accountant: Finance, Invoices, Payments, Reports.

Student: Results, Profile, Payments.

System Interactions and Workflow
1. Academic Workflow
Faculty Creation → Course Setup → Class Organization → Subject Assignment → Student Enrollment

2. Examination Workflow
Exam Creation → Subject Association → Student Registration → Marks Entry → Result Generation → Marksheet Distribution

3. Financial Workflow
Fee Structure Setup → Invoice Generation → Payment Processing → Receipt Generation → Financial Reporting

4. Administrative Workflow
User Authentication → Dashboard Access → Module Navigation → Data Management → Report Generation

Technical Specifications
Frontend Technologies (HTML5, JavaScript, Tailwind CSS)
Templating: Laravel Blade for efficient, server-rendered views.

Styling: Tailwind CSS for a utility-first approach, ensuring responsive and consistent design.

Interactivity: Vanilla JavaScript for dynamic content, AJAX requests, and DOM manipulation.

Form Handling: Standard HTML forms complemented by JavaScript for asynchronous submission and validation feedback.

Icons: Inline SVG or a font icon library (e.g., Font Awesome, Phosphor Icons) for scalable vector icons.

Backend Technologies (Laravel)
Framework: Laravel 10+ (PHP) for robust, scalable backend development.

Entity Management: Eloquent ORM for database abstraction and model relationships.

API Layer: RESTful APIs using Laravel's routing and controllers for data exchange.

Authentication: Laravel Breeze (for basic scaffolding) and Laravel Sanctum (for API token authentication).

File Management: Laravel's built-in Storage facade for secure file uploads and storage.

Database: MySQL (recommended) or PostgreSQL.

Integration Capabilities
Core Integrations: LLM integration (via HTTP client to external APIs), email services, file uploads.

External APIs: Support for third-party service integrations via Laravel's HTTP Client.

Data Import/Export: CSV and JSON data exchange capabilities.

Security Features
Authentication & Authorization: Laravel's robust authentication system with session management and token-based API access via Laravel Sanctum. OAuth2 integration with Google handled by Laravel Socialite.

Data Protection: Encrypted data transmission (HTTPS), input validation and sanitization, SQL injection prevention through Eloquent ORM.

Access Control: Middleware for route protection, user session management, secure logout functionality, protection against unauthorized access attempts.

Performance and Scalability
Frontend Performance: Efficient HTML rendering, minimal JavaScript for faster load times, responsive design for multi-device support. Tailwind's purging ensures minimal CSS bundle size.

Backend Scalability: Cloud-agnostic Laravel architecture, efficient database queries with proper indexing, caching mechanisms (e.g., Redis, Memcached) for improved response times, queue system for asynchronous tasks.

Deployment Architecture
The production environment follows a standard web application deployment model:

Frontend (HTML5/JS/CSS):

Build Optimization: Minification and bundling of CSS and JS assets via Laravel Mix/Vite.

CDN Distribution: Static assets can be served from a CDN for faster global access.

Progressive Web App Features: Can be gradually added for offline capabilities and faster loading.

Backend (Laravel Platform):

API Gateway: If deployed in a microservices architecture, but typically handled by Laravel's routing.

Database Layer: Managed database service (e.g., AWS RDS, Google Cloud SQL).

Authentication Service: Handled by Laravel's built-in authentication system.

Integration Services: Laravel's job queues and HTTP client for external service communication.

Infrastructure:

Load Balancing: Distributes traffic across multiple application instances.

Auto Scaling: Adjusts resources based on demand to maintain performance.

Monitoring & Logging: Centralized systems for application health and debugging.

Backup & Recovery: Regular database and file backups.

Future Enhancements
Planned Features
Mobile Application: Native mobile app for iOS and Android.

Advanced Analytics: AI-powered insights and predictions using LLM integrations.

Integration Hub: Dedicated module for managing third-party system integrations.

Automated Notifications: Email and SMS notification system for alerts and updates.

Document Management: Digital document storage and retrieval.

Attendance Management: Biometric and digital attendance tracking.

Library Management: Book and resource management system.

Hostel Management: Accommodation and facility management.

Technical Improvements
Role-Based Access Control: Granular permission system for all modules and actions.

Advanced Reporting: Custom report builder with dynamic filtering and export options.

API Documentation: Comprehensive API documentation (e.g., using Swagger/OpenAPI).

Testing Framework: Automated unit, feature, and browser testing implementation.

Performance Monitoring: Real-time performance analytics and alerting.

Conclusion
The Bajra International College Management System, re-imagined with a Laravel backend and a traditional yet powerful HTML5, JavaScript, and Tailwind CSS frontend, represents a robust and flexible solution for modern educational institution management. Built with scalability, security, and user experience in mind, the system provides a solid foundation for digital transformation in academic institutions. The modular architecture ensures that the system can evolve with changing requirements while maintaining high performance and reliability standards.

The system's design prioritizes ease of use, data integrity, and operational efficiency, making it an ideal solution for colleges seeking to modernize their administrative and academic processes. With its strong technical foundation and comprehensive feature set, the system is well-positioned to support the growing needs of educational institutions in the digital age.

Requirements for Bajra International College Management System
This section outlines the essential software, tools, and configurations required to set up and run the Bajra International College Management System with its new Laravel backend and HTML5, JavaScript, and Tailwind CSS frontend.

1. Server Requirements (Backend)
The Laravel backend requires a web server environment with PHP and a database.

PHP:

Version: PHP 8.1 or higher (Laravel 10+ recommended)

Extensions: Ensure the following PHP extensions are enabled:

BCMath

Ctype

cURL

DOM

Fileinfo

Filter

JSON

Mbstring

OpenSSL

PCRE

PDO

Session

Tokenizer

XML

Database:

Type: MySQL (8.0+) or PostgreSQL (10+)

A database instance accessible by the Laravel application.

Composer:

Version: Composer 2.x or higher

Required for managing PHP dependencies.

Web Server:

Apache (with mod_rewrite enabled) or Nginx

Properly configured to serve the Laravel application.

2. Frontend Build Tools
The frontend, built with HTML5, JavaScript, and Tailwind CSS, requires Node.js and npm for compiling assets.

Node.js:

Version: Node.js 16.x or higher

Required for npm and frontend build processes.

npm (Node Package Manager):

Version: Latest stable version (comes with Node.js)

Used for managing JavaScript and frontend development dependencies.

Laravel Mix / Vite:

Depending on the Laravel version, either Laravel Mix (for Laravel 8.x and below) or Vite (for Laravel 9.x+) will be used for asset compilation (Tailwind CSS, JavaScript bundling).

These tools are installed via npm during the project setup.

3. Recommended Development Tools
While not strictly required to run the application, these tools enhance the development experience significantly.

Version Control:

Git: Essential for source code management.

Integrated Development Environment (IDE):

VS Code: Highly recommended with extensions for PHP, Laravel, JavaScript, and Tailwind CSS.

PhpStorm: A powerful commercial IDE for PHP development.

Database Client:

TablePlus, DBeaver, MySQL Workbench, pgAdmin: For easier database management and inspection.

4. Environment Configuration
.env File:

A .env file at the root of the Laravel project with correct configurations for database connection, application key, and other environment-specific variables.

Example: APP_NAME, APP_ENV, APP_KEY, DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, MAIL_MAILER, etc.

5. Browser Compatibility
Modern web browsers (Chrome, Firefox, Edge, Safari) are supported, designed with responsive layouts to ensure compatibility across various devices.

Laravel & Tailwind CSS Implementation Details
This section provides key code examples for building the Bajra International College Management System using Laravel for the backend and HTML5, JavaScript, and Tailwind CSS for the frontend.

1. Project Setup (Conceptual Steps)
To begin, you'd typically start by creating a new Laravel project and installing necessary frontend dependencies.

# Create a new Laravel project
composer create-project laravel/laravel bajra-cms

# Navigate into the project directory
cd bajra-cms

# Install frontend dependencies (Tailwind CSS, PostCSS, Autoprefixer)
npm install -D tailwindcss postcss autoprefixer

# Initialize Tailwind CSS config files
npx tailwindcss init -p

# Install Laravel Breeze for authentication scaffolding (optional but recommended)
composer require laravel/breeze --dev
php artisan breeze:install blade --dark # Using Blade for frontend
php artisan migrate # Run migrations for users table

# Set up Laravel Mix (if using Laravel < 9) or Vite (Laravel 9+)
# For Laravel Mix, update webpack.mix.js (example provided later)
# For Vite, it's configured by default with Laravel 9+ (update vite.config.js if needed)

2. Backend Implementation (Laravel)
The backend will handle data storage, business logic, API endpoints, and authentication.

2.1. Database Migrations
Laravel migrations define your database schema. Here are examples for Faculty, Course, Student, and Teacher, reflecting the entity relationships.

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Faculty Table
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('faculty_name');
            $table->string('dean_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Course Table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_name');
            $table->string('course_code')->unique();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade'); // Foreign key to faculties table
            $table->integer('duration_years');
            $table->integer('total_semesters');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // CollegeClass Table (renamed to avoid conflict with PHP's Class keyword)
        Schema::create('college_classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); // Foreign key to courses table
            $table->integer('semester');
            $table->string('academic_year'); // e.g., "2023-2024"
            $table->timestamps();
        });

        // Subject Table
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name');
            $table->string('subject_code')->unique();
            $table->foreignId('college_class_id')->constrained('college_classes')->onDelete('cascade'); // Foreign key to college_classes table
            $table->integer('credit_hours');
            $table->integer('full_marks')->default(100);
            $table->integer('pass_marks')->default(40);
            $table->string('teacher_name')->nullable(); // Can be a foreign key to teachers table if user roles are fully implemented
            $table->timestamps();
        });

        // Student Table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('registration_number')->unique();
            $table->string('roll_number')->nullable();
            $table->foreignId('college_class_id')->nullable()->constrained('college_classes')->onDelete('set null'); // Student can be assigned later
            $table->string('admission_year');
            $table->string('contact_number')->nullable();
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Teacher Table
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_name');
            $table->string('employee_id')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('hire_date');
            $table->decimal('basic_salary', 10, 2);
            $table->string('status')->default('active'); // e.g., active, inactive, on_leave
            $table->string('bank_account')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Exam Table
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('exam_name');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->integer('semester');
            $table->string('exam_year');
            $table->string('exam_type'); // e.g., Terminal, Final, Supplementary
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('scheduled'); // e.g., scheduled, ongoing, completed
            $table->timestamps();
        });

        // ExamResult Table
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id', 'subject_id']); // Ensure unique result per student per subject per exam
        });

        // SalaryPayment Table
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('month'); // e.g., "January 2024"
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('status')->default('paid'); // e.g., paid, pending
            $table->timestamps();
        });

        // Invoice Table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // e.g., pending, paid, overdue
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Payment Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method'); // e.g., cash, online, bank_transfer
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('salary_payments');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('students');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('college_classes');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('faculties');
    }
};

2.2. Eloquent Models
Each database table will have a corresponding Eloquent Model, defining relationships and enabling easy data interaction.

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = ['faculty_name', 'dean_name', 'description'];

    // A Faculty can have many Courses
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['course_name', 'course_code', 'faculty_id', 'duration_years', 'total_semesters', 'description'];

    // A Course belongs to a Faculty
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    // A Course can have many CollegeClasses
    public function collegeClasses()
    {
        return $this->hasMany(CollegeClass::class);
    }

    // A Course can have many Exams
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}

class CollegeClass extends Model
{
    use HasFactory;

    protected $table = 'college_classes'; // Explicitly set table name

    protected $fillable = ['class_name', 'course_id', 'semester', 'academic_year'];

    // A CollegeClass belongs to a Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // A CollegeClass can have many Students
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // A CollegeClass can have many Subjects
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['subject_name', 'subject_code', 'college_class_id', 'credit_hours', 'full_marks', 'pass_marks', 'teacher_name'];

    // A Subject belongs to a CollegeClass
    public function collegeClass()
    {
        return $this->belongsTo(CollegeClass::class);
    }

    // A Subject can have many ExamResults
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }
}

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['student_name', 'registration_number', 'roll_number', 'college_class_id', 'admission_year', 'contact_number', 'email', 'address'];

    // A Student belongs to a CollegeClass
    public function collegeClass()
    {
        return $this->belongsTo(CollegeClass::class);
    }

    // A Student can have many ExamResults
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    // A Student can have many Invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_name', 'employee_id', 'email', 'phone', 'department', 'position', 'hire_date', 'basic_salary', 'status', 'bank_account', 'address'];

    // A Teacher can have many SalaryPayments
    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }
}

class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['exam_name', 'course_id', 'semester', 'exam_year', 'exam_type', 'start_date', 'end_date', 'status'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // An Exam belongs to a Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // An Exam can have many ExamResults
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }
}

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = ['exam_id', 'student_id', 'subject_id', 'marks_obtained', 'grade', 'remarks'];

    // An ExamResult belongs to an Exam
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // An ExamResult belongs to a Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // An ExamResult belongs to a Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_id', 'month', 'amount', 'payment_date', 'status'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    // A SalaryPayment belongs to a Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'amount', 'status', 'due_date', 'description'];

    protected $casts = [
        'due_date' => 'date',
    ];

    // An Invoice belongs to a Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // An Invoice can have many Payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'amount', 'payment_date', 'payment_method', 'transaction_id'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    // A Payment belongs to an Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

2.3. API Routes
API routes define the endpoints for your frontend to interact with. These would typically be placed in routes/api.php.

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CollegeClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SalaryPaymentController;
use App\Http\Controllers\AuthController; // Custom AuthController for API

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes (e.g., login, registration if allowed via API)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Academic Management Module
    Route::apiResource('faculties', FacultyController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('college-classes', CollegeClassController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('teachers', TeacherController::class);

    // Examination Management Module
    Route::apiResource('exams', ExamController::class);
    Route::apiResource('exam-results', ExamResultController::class);

    // Financial Management Module
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('salary-payments', SalaryPaymentController::class);

    // Administrative Module (Dashboard, Reports, Settings - will have specific routes)
    // Example for a simple dashboard data endpoint
    Route::get('/dashboard-summary', [DashboardController::class, 'summary']);
});

2.4. API Controllers
Controllers handle incoming requests, process data using models, and return responses.

<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Retrieve all faculties with their associated courses
        $faculties = Faculty::with('courses')->latest()->get();
        return response()->json($faculties);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'faculty_name' => 'required|string|max:255|unique:faculties,faculty_name',
                'dean_name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Create a new faculty record
            $faculty = Faculty::create($validatedData);

            // Return the created faculty with a 201 Created status
            return response()->json($faculty, 201);
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Handle other potential errors
            return response()->json([
                'message' => 'Error creating faculty',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Faculty  $faculty
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Faculty $faculty)
    {
        // Load associated courses for the faculty
        $faculty->load('courses');
        return response()->json($faculty);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Faculty  $faculty
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Faculty $faculty)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'faculty_name' => 'required|string|max:255|unique:faculties,faculty_name,' . $faculty->id, // Exclude current faculty's ID
                'dean_name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Update the faculty record
            $faculty->update($validatedData);

            // Return the updated faculty
            return response()->json($faculty);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating faculty',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Faculty  $faculty
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Faculty $faculty)
    {
        try {
            // Delete the faculty record
            $faculty->delete();

            // Return a success message with a 204 No Content status
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting faculty',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

3. Frontend Implementation (HTML5, JavaScript, Tailwind CSS)
The frontend will consist of Blade templates for structure, Tailwind CSS for styling, and vanilla JavaScript for interactivity and API communication.

3.1. Tailwind CSS Configuration (tailwind.config.js)
Configure Tailwind to scan your Blade views for classes.

/** @type {import('tailwindcss').Config} */
module.exports = {
  // Specify files where Tailwind should look for classes
  content: [
    "./resources/**/*.blade.php", // Blade templates
    "./resources/**/*.js",       // JavaScript files
    "./resources/**/*.vue",      // If you decide to use Vue later
  ],
  theme: {
    extend: {
      // Custom fonts, colors, spacing, etc.
      fontFamily: {
        inter: ['Inter', 'sans-serif'], // Example: If you want to use Inter font
      },
      colors: {
        // Define custom colors for your college theme
        'primary-blue': '#2563eb', // A primary blue color
        'accent-green': '#10b981', // An accent green
      },
    },
  },
  plugins: [],
}

3.2. Laravel Mix / Vite Configuration (webpack.mix.js or vite.config.js)
This file tells Laravel how to compile your assets.

// webpack.mix.js (for Laravel Mix, if using older Laravel versions)
const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

mix.js('resources/js/app.js', 'public/js') // Compile app.js to public/js
   .postCss('resources/css/app.css', 'public/css', [ // Compile app.css (with Tailwind) to public/css
       tailwindcss('./tailwind.config.js'),
   ])
   .version(); // Add version hashing for cache busting
```javascript
// vite.config.js (for Laravel 9+ with Vite)
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss'; // Although Vite handles PostCSS plugins automatically

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});

3.3. Main Layout Blade Template (resources/views/layouts/app.blade.php)
This will be your base HTML structure.

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bajra CMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="[https://fonts.bunny.net](https://fonts.bunny.net)">
    <link href="[https://fonts.bunny.net/css?family=inter:400,500,600&display=swap](https://fonts.bunny.net/css?family=inter:400,500,600&display=swap)" rel="stylesheet" />

    <!-- Styles (Tailwind CSS) -->
    @vite('resources/css/app.css') {{-- For Vite --}}
    {{-- <link href="{{ mix('css/app.css') }}" rel="stylesheet"> --}} {{-- For Laravel Mix --}}

    <!-- Optional: Lucide React for icons (via CDN if not bundled) -->
    {{-- You would typically use inline SVGs or a font icon library for vanilla JS --}}
    {{-- <script src="[https://unpkg.com/lucide@latest](https://unpkg.com/lucide@latest)"></script> --}}

</head>
<body class="font-inter antialiased bg-gray-100 text-gray-800">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation Header -->
        <nav class="bg-white shadow-md p-4">
            <div class="container mx-auto flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-primary-blue">Bajra CMS</a>
                <div class="space-x-4">
                    <a href="/dashboard" class="text-gray-600 hover:text-primary-blue">Dashboard</a>
                    <a href="/faculties" class="text-gray-600 hover:text-primary-blue">Faculties</a>
                    <a href="/students" class="text-gray-600 hover:text-primary-blue">Students</a>
                    <a href="/teachers" class="text-gray-600 hover:text-primary-blue">Teachers</a>
                    <!-- Add more navigation links here -->
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline-block">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-500">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary-blue">Log In</a>
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-primary-blue">Register</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow container mx-auto p-6">
            {{ $slot }} {{-- This is where content from other Blade views will be injected --}}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white p-4 text-center text-sm">
            &copy; {{ date('Y') }} Bajra International College Management System. All rights reserved.
        </footer>
    </div>

    <!-- Scripts -->
    @vite('resources/js/app.js') {{-- For Vite --}}
    {{-- <script src="{{ mix('js/app.js') }}"></script> --}} {{-- For Laravel Mix --}}
</body>
</html>

3.4. Example Blade View (resources/views/faculties/index.blade.php)
This view will display faculties and allow adding new ones, interacting with the Laravel API using JavaScript.

<x-app-layout> {{-- Using the app layout defined above --}}
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Faculty Management</h2>

        <!-- Add New Faculty Form -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-2xl font-semibold text-gray-700 mb-4">Add New Faculty</h3>
            <form id="addFacultyForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf {{-- CSRF token for Laravel forms --}}
                <div>
                    <label for="faculty_name" class="block text-sm font-medium text-gray-700">Faculty Name</label>
                    <input type="text" id="faculty_name" name="faculty_name" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary-blue focus:border-primary-blue">
                    <p id="faculty_name_error" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                <div>
                    <label for="dean_name" class="block text-sm font-medium text-gray-700">Dean Name (Optional)</label>
                    <input type="text" id="dean_name" name="dean_name"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary-blue focus:border-primary-blue">
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary-blue focus:border-primary-blue"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-blue hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-blue transition ease-in-out duration-150">
                        Add Faculty
                    </button>
                </div>
            </form>
        </div>

        <!-- Faculties List Table -->
        <div>
            <h3 class="text-2xl font-semibold text-gray-700 mb-4">Existing Faculties</h3>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Faculty Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dean Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="facultiesTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Faculty rows will be inserted here by JavaScript -->
                    </tbody>
                </table>
                <p id="loadingMessage" class="p-4 text-center text-gray-500">Loading faculties...</p>
                <p id="errorMessage" class="p-4 text-center text-red-500 hidden">Error loading faculties.</p>
            </div>
        </div>
    </div>

    <!-- Edit Faculty Modal (Hidden by default) -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-xl font-bold mb-4">Edit Faculty</h3>
            <form id="editFacultyForm" data-faculty-id="">
                @csrf
                @method('PUT') {{-- Use PUT method for update --}}
                <div class="mb-4">
                    <label for="edit_faculty_name" class="block text-sm font-medium text-gray-700">Faculty Name</label>
                    <input type="text" id="edit_faculty_name" name="faculty_name" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    <p id="edit_faculty_name_error" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                <div class="mb-4">
                    <label for="edit_dean_name" class="block text-sm font-medium text-gray-700">Dean Name</label>
                    <input type="text" id="edit_dean_name" name="dean_name"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>
                <div class="mb-4">
                    <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="edit_description" name="description" rows="3"
                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeModal"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                            class="px-4 py-2 bg-accent-green text-white rounded-md hover:bg-green-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

3.5. JavaScript for Interactivity (resources/js/app.js)
This file will contain the vanilla JavaScript logic to fetch data from your Laravel API, handle form submissions, and update the DOM.

// Ensure this script runs after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {

    const facultiesTableBody = document.getElementById('facultiesTableBody');
    const addFacultyForm = document.getElementById('addFacultyForm');
    const loadingMessage = document.getElementById('loadingMessage');
    const errorMessage = document.getElementById('errorMessage');

    // Edit Modal elements
    const editModal = document.getElementById('editModal');
    const closeModalButton = document.getElementById('closeModal');
    const editFacultyForm = document.getElementById('editFacultyForm');
    const editFacultyNameInput = document.getElementById('edit_faculty_name');
    const editDeanNameInput = document.getElementById('edit_dean_name');
    const editDescriptionInput = document.getElementById('edit_description');
    const editFacultyNameError = document.getElementById('edit_faculty_name_error');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Function to display errors for a specific field
    function displayError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    // Function to clear all errors
    function clearErrors() {
        const errorElements = document.querySelectorAll('[id$="_error"]');
        errorElements.forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    // Function to fetch and display faculties
    async function fetchFaculties() {
        loadingMessage.classList.remove('hidden');
        errorMessage.classList.add('hidden');
        facultiesTableBody.innerHTML = ''; // Clear existing rows

        try {
            const response = await fetch('/api/faculties', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken // Include CSRF token for API calls
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to fetch faculties');
            }

            const faculties = await response.json();

            if (faculties.length === 0) {
                facultiesTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            No faculties found.
                        </td>
                    </tr>
                `;
            } else {
                faculties.forEach(faculty => {
                    const row = `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${faculty.faculty_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${faculty.dean_name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${faculty.description || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button data-id="${faculty.id}"
                                        data-name="${faculty.faculty_name}"
                                        data-dean="${faculty.dean_name || ''}"
                                        data-description="${faculty.description || ''}"
                                        class="edit-button text-primary-blue hover:text-blue-900 mr-3 p-1 rounded hover:bg-blue-100">
                                    Edit
                                </button>
                                <button data-id="${faculty.id}"
                                        class="delete-button text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-100">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `;
                    facultiesTableBody.insertAdjacentHTML('beforeend', row);
                });

                // Attach event listeners to new edit/delete buttons
                document.querySelectorAll('.edit-button').forEach(button => {
                    button.addEventListener('click', openEditModal);
                });
                document.querySelectorAll('.delete-button').forEach(button => {
                    button.addEventListener('click', deleteFaculty);
                });
            }
        } catch (error) {
            console.error('Error fetching faculties:', error);
            errorMessage.textContent = `Could not load faculties: ${error.message}`;
            errorMessage.classList.remove('hidden');
        } finally {
            loadingMessage.classList.add('hidden');
        }
    }

    // Handle adding a new faculty
    if (addFacultyForm) {
        addFacultyForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearErrors(); // Clear previous errors

            const formData = new FormData(this);
            const facultyData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('/api/faculties', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(facultyData)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    if (response.status === 422 && errorData.errors) {
                        for (const field in errorData.errors) {
                            displayError(`${field}_error`, errorData.errors[field][0]);
                        }
                    }
                    throw new Error(errorData.message || 'Failed to add faculty');
                }

                // If successful, clear the form and re-fetch faculties
                this.reset();
                fetchFaculties();
                console.log('Faculty added successfully!');

            } catch (error) {
                console.error('Error adding faculty:', error);
                // General error message for user or specific for add form
                displayError('faculty_name_error', error.message); // Example placement
            }
        });
    }

    // Open Edit Modal
    function openEditModal(event) {
        const button = event.target;
        const facultyId = button.dataset.id;
        const facultyName = button.dataset.name;
        const deanName = button.dataset.dean;
        const description = button.dataset.description;

        editFacultyForm.dataset.facultyId = facultyId;
        editFacultyNameInput.value = facultyName;
        editDeanNameInput.value = deanName;
        editDescriptionInput.value = description;

        clearErrors(); // Clear errors when opening modal
        editModal.classList.remove('hidden'); // Show the modal
    }

    // Close Edit Modal
    if (closeModalButton) {
        closeModalButton.addEventListener('click', function() {
            editModal.classList.add('hidden'); // Hide the modal
        });
    }

    // Handle updating a faculty
    if (editFacultyForm) {
        editFacultyForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearErrors(); // Clear previous errors

            const facultyId = this.dataset.facultyId;
            const formData = new FormData(this);
            const facultyData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`/api/faculties/${facultyId}`, {
                    method: 'PUT', // or 'PATCH'
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(facultyData)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    if (response.status === 422 && errorData.errors) {
                        for (const field in errorData.errors) {
                            // Prepend 'edit_' to field name to match modal input IDs
                            displayError(`edit_${field}_error`, errorData.errors[field][0]);
                        }
                    }
                    throw new Error(errorData.message || 'Failed to update faculty');
                }

                // If successful, hide modal and re-fetch faculties
                editModal.classList.add('hidden');
                fetchFaculties();
                console.log('Faculty updated successfully!');

            } catch (error) {
                console.error('Error updating faculty:', error);
                displayError('edit_faculty_name_error', error.message); // Example: generic error for modal
            }
        });
    }


    // Handle deleting a faculty
    async function deleteFaculty(event) {
        const facultyId = event.target.dataset.id;
        if (!confirm('Are you sure you want to delete this faculty? This action cannot be undone.')) {
            return; // User cancelled the deletion
        }

        try {
            const response = await fetch(`/api/faculties/${facultyId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to delete faculty');
            }

            // If successful, re-fetch faculties
            fetchFaculties();
            console.log('Faculty deleted successfully!');

        } catch (error) {
            console.error('Error deleting faculty:', error);
            errorMessage.textContent = `Could not delete faculty: ${error.message}`;
            errorMessage.classList.remove('hidden');
        }
    }

    // Initial fetch of faculties when the page loads
    fetchFaculties();
});

3.6. CSS Entry Point (resources/css/app.css)
This file will import Tailwind's base styles and any custom CSS.

/* Import Tailwind CSS directives */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* You can add custom CSS here if needed, but Tailwind is preferred for utilities */
/* For example, if you need custom fonts not from CDN, you'd define them here */

/* Example custom component if you absolutely need one */
/* @layer components {
  .btn-custom {
    @apply px-4 py-2 rounded-md font-semibold;
  }
} */
