# Project Overview

This project is a comprehensive College Management System built with the Laravel framework. It provides a wide range of features for managing students, teachers, courses, exams, finances, and other academic and administrative tasks. The system is designed to be used by various roles, including Super Admins, Admins, Teachers, and Students, with a robust permission system to control access to different features.

## Core Features

The application includes the following core features:

*   **User Management:**
    *   User authentication and authorization.
    *   Role-based access control (Super Admin, Admin, Teacher, etc.).
    *   Permission management for fine-grained control over user actions.
*   **Academic Structure:**
    *   Management of academic years, faculties, departments, courses, and classes.
    *   Subject management, including assigning subjects to classes.
*   **Student Management:**
    *   Student profiles with detailed information.
    *   Student enrollment in courses and classes.
*   **Teacher Management:**
    *   Teacher profiles and information management.
*   **Exam Management:**
    *   Creation and management of exams.
    *   Recording and managing student marks and grades.
    *   Generation of marksheets and results.
*   **Finance Management:**
    *   Fee and invoice management.
    *   Payment tracking and verification.
    *   Teacher salary and expense management.
    *   Financial reporting and analytics.
*   **System Administration:**
    *   A central dashboard for a quick overview of the system.
    *   Notification and activity logging.
    *   System performance and security monitoring.
    *   Configuration of college settings and grading systems.
    *   Global search functionality.
    *   Comprehensive reporting module.

## Technologies Used

The project is built using the following technologies and packages:

*   **Backend:**
    *   PHP 8.2
    *   Laravel 12
    *   Inertia.js
    *   Laravel DOMPDF (for PDF generation)
    *   Spatie Laravel ActivityLog (for activity logging)
    *   Spatie Laravel Permission (for roles and permissions)
*   **Frontend:**
    *   Tailwind CSS
    *   Alpine.js
    *   Vue.js (via Inertia.js)
*   **Database:**
    *   MySQL (or other Laravel-supported database)

## Database Schema

The database schema is organized into several key tables that model the different entities of the college management system. The main tables include:

*   `users`: Stores user accounts, including students, teachers, and administrators.
*   `roles` & `permissions`: Manages user roles and permissions.
*   `students`: Contains detailed information about each student.
*   `teachers`: Stores information about teachers.
*   `academic_years`: Defines academic years.
*   `faculties`, `departments`, `courses`, `class_sections`, `subjects`: Define the academic structure of the college.
*   `enrollments`: Manages student enrollments in classes.
*   `exams`, `marks`, `grades`: Manages exams, student marks, and grades.
*   `fees`, `invoices`, `payments`: Manages financial transactions.
*   `expenses`, `salary_payments`: Manages college expenses and teacher salaries.
*   `college_settings`: Stores global settings for the college.
*   `grading_systems`: Manages different grading systems.
*   `activity_log`: Logs user activities throughout the application.
*   `notifications`: Stores system notifications for users.

## Getting Started

To set up and run the project locally, follow these steps:

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/WangbuTheeng/final-project.git
    cd final-project
    ```
2.  **Install dependencies:**
    ```bash
    composer install
    npm install
    ```
3.  **Set up the environment:**
    *   Copy the `.env.example` file to `.env`.
    *   Generate an application key: `php artisan key:generate`
    *   Configure your database connection in the `.env` file.
4.  **Run database migrations and seeders:**
    ```bash
    php artisan migrate --seed
    ```
5.  **Run the development server:**
    ```bash
    npm run dev
    ```
    This will start the PHP development server, the queue listener, the log viewer, and the Vite development server concurrently.

6.  **Access the application:**
    *   The application should be available at `http://127.0.0.1:8000`.
