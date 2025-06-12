College Management System: Authentication and Authorization Implementation
Last modified: 29 minutes ago
College Management System: Authentication and Authorization Implementation
Introduction
The authentication and authorization system forms the security backbone of the College Management System, ensuring that users can securely access the system while maintaining appropriate access controls based on their roles and responsibilities. This comprehensive implementation guide covers the complete development of a robust authentication system using Laravel's built-in features enhanced with role-based access control and advanced security measures.
The authentication system must accommodate multiple user types including administrators, faculty members, students, staff, and parents, each with distinct access requirements and security considerations. The implementation follows security best practices including password hashing, session management, multi-factor authentication options, and comprehensive audit logging to ensure compliance with educational data privacy regulations.
Authentication System Architecture
The authentication architecture for the College Management System implements a multi-layered security approach that combines Laravel's built-in authentication features with custom enhancements specific to educational institution requirements. The system provides secure user registration, login, password management, and session handling while maintaining flexibility for future security enhancements.
The core authentication system utilizes Laravel Sanctum for API token management, enabling both web-based session authentication and token-based authentication for mobile applications and third-party integrations. This dual approach ensures that the system can support various client types while maintaining consistent security standards across all access methods.
The authentication flow begins with user registration or administrative user creation, followed by email verification to ensure valid contact information. The system implements progressive security measures including account lockout after failed login attempts, password complexity requirements, and optional multi-factor authentication for sensitive roles such as administrators and faculty members.
Session management includes automatic session timeout for inactive users, secure session storage using Redis, and comprehensive session tracking for security monitoring and compliance reporting. The system maintains detailed logs of all authentication events including successful logins, failed attempts, password changes, and account modifications.
User Registration and Account Creation
The user registration system provides multiple pathways for account creation depending on the user type and institutional policies. Self-registration may be available for certain user types such as prospective students, while administrative creation is required for faculty and staff accounts to ensure proper verification and role assignment.
The registration process implements comprehensive validation to ensure data integrity and security. Email addresses must be unique across the system and are verified through a secure email confirmation process. Usernames follow institutional naming conventions and are validated for uniqueness and format compliance.
Password requirements enforce strong security standards including minimum length, character complexity, and protection against common passwords. The system implements password strength indicators during registration to guide users in creating secure passwords that meet institutional security policies.
The UserRegistrationController handles the registration process with comprehensive validation and security measures.
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\UserService;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    protected $userService;
    protected $emailVerificationService;

    public function __construct(
        UserService $userService,
        EmailVerificationService $emailVerificationService
    ) {
        $this->userService = $userService;
        $this->emailVerificationService = $emailVerificationService;
        $this->middleware('guest');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Create user account
            $userData = $request->validated();
            $userData['password'] = Hash::make($userData['password']);
            $userData['status'] = 'pending'; // Requires email verification
            
            $user = User::create($userData);

            // Send email verification
            $this->emailVerificationService->sendVerificationEmail($user);

            // Log registration event
            activity()
                ->causedBy($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'registration_type' => 'self_registration'
                ])
                ->log('User registered');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please check your email to verify your account.',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'verification_required' => true
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'errors' => ['general' => ['An error occurred during registration.']]
            ], 500);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email'
        ]);

        try {
            $user = $this->emailVerificationService->verifyEmail(
                $request->email,
                $request->token
            );

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired verification token.'
                ], 400);
            }

            // Update user status to active
            $user->update([
                'status' => 'active',
                'email_verified_at' => now()
            ]);

            // Log verification event
            activity()
                ->causedBy($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('Email verified');

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully. You can now log in to your account.',
                'data' => [
                    'user_id' => $user->id,
                    'verified_at' => $user->email_verified_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email verification failed. Please try again or request a new verification email.'
            ], 500);
        }
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.'
            ], 400);
        }

        try {
            $this->emailVerificationService->sendVerificationEmail($user);

            return response()->json([
                'success' => true,
                'message' => 'Verification email sent successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email. Please try again later.'
            ], 500);
        }
    }
}
The RegisterRequest class implements comprehensive validation for user registration with role-specific requirements and security measures.
<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        // Allow registration for guest users or specific roles based on configuration
        return !auth()->check() || auth()->user()->can('create-users');
    }

    public function rules()
    {
        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9._-]+$/',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    // Check if email domain is allowed for self-registration
                    $allowedDomains = config('auth.allowed_registration_domains', []);
                    if (!empty($allowedDomains)) {
                        $domain = substr(strrchr($value, "@"), 1);
                        if (!in_array($domain, $allowedDomains)) {
                            $fail('Registration is not allowed for this email domain.');
                        }
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'first_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z\s\'-]+$/',
            ],
            'last_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z\s\'-]+$/',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/',
            ],
            'date_of_birth' => [
                'required',
                'date',
                'before:today',
                'after:1900-01-01',
            ],
            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other']),
            ],
            'role' => [
                'sometimes',
                Rule::in(config('auth.allowed_self_registration_roles', ['student'])),
            ],
            'terms_accepted' => [
                'required',
                'accepted',
            ],
            'privacy_policy_accepted' => [
                'required',
                'accepted',
            ],
        ];
    }

    public function messages()
    {
        return [
            'username.regex' => 'Username can only contain letters, numbers, dots, underscores, and hyphens.',
            'email.email' => 'Please enter a valid email address.',
            'password.uncompromised' => 'This password has been found in data breaches. Please choose a different password.',
            'first_name.regex' => 'First name can only contain letters, spaces, apostrophes, and hyphens.',
            'last_name.regex' => 'Last name can only contain letters, spaces, apostrophes, and hyphens.',
            'phone.regex' => 'Please enter a valid phone number.',
            'date_of_birth.before' => 'You must be born before today.',
            'date_of_birth.after' => 'Please enter a valid date of birth.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
            'privacy_policy_accepted.accepted' => 'You must accept the privacy policy.',
        ];
    }

    protected function prepareForValidation()
    {
        // Normalize input data
        $this->merge([
            'username' => strtolower(trim($this->username ?? '')),
            'email' => strtolower(trim($this->email ?? '')),
            'first_name' => ucwords(strtolower(trim($this->first_name ?? ''))),
            'last_name' => ucwords(strtolower(trim($this->last_name ?? ''))),
            'phone' => preg_replace('/[^\d\+]/', '', $this->phone ?? ''),
            'role' => $this->role ?? 'student', // Default role for self-registration
        ]);
    }
}
Login System Implementation
The login system provides secure authentication for all user types while implementing comprehensive security measures to protect against common attack vectors. The system supports both traditional username/password authentication and enhanced security options including multi-factor authentication and account lockout protection.
The login process includes rate limiting to prevent brute force attacks, comprehensive logging for security monitoring, and flexible authentication options to accommodate different user preferences and security requirements. The system maintains session security through secure cookie configuration, session rotation, and automatic timeout for inactive sessions.
The LoginController implements the core authentication logic with comprehensive security measures and error handling.
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthenticationService;
use App\Services\SecurityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    protected $authService;
    protected $securityService;

    public function __construct(
        AuthenticationService $authService,
        SecurityService $securityService
    ) {
        $this->authService = $authService;
        $this->securityService = $securityService;
        $this->middleware('guest')->except('logout');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('login', 'password');
        $rememberMe = $request->boolean('remember_me');
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check rate limiting
        $rateLimitKey = 'login-attempts:' . $ipAddress;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            
            return response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
                'retry_after' => $seconds
            ], 429);
        }

        try {
            $result = $this->authService->attemptLogin($credentials, $rememberMe, $ipAddress, $userAgent);

            if ($result['success']) {
                // Clear rate limiting on successful login
                RateLimiter::clear($rateLimitKey);

                $user = $result['user'];
                $token = $result['token'] ?? null;

                // Check if MFA is required
                if ($this->securityService->requiresMFA($user)) {
                    // Generate and send MFA code
                    $mfaToken = $this->securityService->generateMFAToken($user);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'MFA verification required.',
                        'data' => [
                            'mfa_required' => true,
                            'mfa_token' => $mfaToken,
                            'mfa_methods' => $this->securityService->getAvailableMFAMethods($user)
                        ]
                    ]);
                }

                // Complete login process
                $this->completeLogin($user, $ipAddress, $userAgent);

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful.',
                    'data' => [
                        'user' => $user->load(['student', 'faculty']),
                        'token' => $token,
                        'expires_at' => $token ? now()->addHours(24) : null,
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                        'roles' => [$user->role]
                    ]
                ]);

            } else {
                // Increment rate limiting on failed login
                RateLimiter::hit($rateLimitKey, 300); // 5 minutes

                // Log failed login attempt
                activity()
                    ->withProperties([
                        'login_identifier' => $credentials['login'],
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                        'failure_reason' => $result['reason']
                    ])
                    ->log('Failed login attempt');

                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => ['login' => [$result['message']]]
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.',
                'errors' => ['general' => ['Login system temporarily unavailable.']]
            ], 500);
        }
    }

    public function verifyMFA(Request $request): JsonResponse
    {
        $request->validate([
            'mfa_token' => 'required|string',
            'mfa_code' => 'required|string|size:6',
            'mfa_method' => 'required|in:email,sms,totp'
        ]);

        try {
            $result = $this->securityService->verifyMFACode(
                $request->mfa_token,
                $request->mfa_code,
                $request->mfa_method
            );

            if ($result['success']) {
                $user = $result['user'];
                $token = $result['token'] ?? null;

                // Complete login process
                $this->completeLogin($user, $request->ip(), $request->userAgent());

                return response()->json([
                    'success' => true,
                    'message' => 'MFA verification successful.',
                    'data' => [
                        'user' => $user->load(['student', 'faculty']),
                        'token' => $token,
                        'expires_at' => $token ? now()->addHours(24) : null,
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                        'roles' => [$user->role]
                    ]
                ]);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => ['mfa_code' => [$result['message']]]
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'MFA verification failed. Please try again.',
                'errors' => ['general' => ['MFA system temporarily unavailable.']]
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if ($user) {
                // Log logout event
                activity()
                    ->causedBy($user)
                    ->withProperties([
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'session_duration' => $this->calculateSessionDuration($user)
                    ])
                    ->log('User logged out');

                // Revoke API tokens if using Sanctum
                if ($request->bearerToken()) {
                    $user->currentAccessToken()->delete();
                }

                // Clear session
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout.'
            ], 500);
        }
    }

    protected function completeLogin($user, $ipAddress, $userAgent): void
    {
        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Log successful login
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'login_method' => 'password'
            ])
            ->log('User logged in');

        // Check for security alerts
        $this->securityService->checkForSecurityAlerts($user, $ipAddress, $userAgent);
    }

    protected function calculateSessionDuration($user): int
    {
        if ($user->last_login_at) {
            return now()->diffInMinutes($user->last_login_at);
        }
        return 0;
    }
}
The AuthenticationService class encapsulates the core authentication logic and security measures.
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticationService
{
    public function attemptLogin(array $credentials, bool $rememberMe = false, string $ipAddress = null, string $userAgent = null): array
    {
        // Determine if login identifier is email or username
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $user = User::where($loginField, $credentials['login'])->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
                'reason' => 'user_not_found'
            ];
        }

        // Check if account is active
        if ($user->status !== 'active') {
            return [
                'success' => false,
                'message' => $this->getAccountStatusMessage($user->status),
                'reason' => 'account_inactive'
            ];
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return [
                'success' => false,
                'message' => 'Please verify your email address before logging in.',
                'reason' => 'email_not_verified'
            ];
        }

        // Verify password
        if (!Hash::check($credentials['password'], $user->password)) {
            // Log failed password attempt
            $this->logFailedPasswordAttempt($user, $ipAddress, $userAgent);
            
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
                'reason' => 'invalid_password'
            ];
        }

        // Check for account lockout
        if ($this->isAccountLocked($user)) {
            return [
                'success' => false,
                'message' => 'Account is temporarily locked due to multiple failed login attempts.',
                'reason' => 'account_locked'
            ];
        }

        // Authenticate user
        Auth::login($user, $rememberMe);

        // Generate API token if needed
        $token = null;
        if (request()->expectsJson() || request()->is('api/*')) {
            $token = $user->createToken('auth-token', ['*'], now()->addHours(24))->plainTextToken;
        }

        // Clear failed login attempts
        $this->clearFailedLoginAttempts($user);

        return [
            'success' => true,
            'user' => $user,
            'token' => $token
        ];
    }

    protected function getAccountStatusMessage(string $status): string
    {
        return match($status) {
            'inactive' => 'Your account is inactive. Please contact the administrator.',
            'suspended' => 'Your account has been suspended. Please contact the administrator.',
            'pending' => 'Your account is pending approval. Please wait for administrator approval.',
            default => 'Your account is not available for login.'
        };
    }

    protected function logFailedPasswordAttempt(User $user, string $ipAddress = null, string $userAgent = null): void
    {
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'attempt_time' => now()
            ])
            ->log('Failed password attempt');

        // Increment failed attempts counter
        $cacheKey = "failed_login_attempts:{$user->id}";
        $attempts = cache()->get($cacheKey, 0) + 1;
        cache()->put($cacheKey, $attempts, now()->addMinutes(30));

        // Lock account if too many attempts
        if ($attempts >= 5) {
            $this->lockAccount($user);
        }
    }

    protected function isAccountLocked(User $user): bool
    {
        $lockKey = "account_locked:{$user->id}";
        return cache()->has($lockKey);
    }

    protected function lockAccount(User $user): void
    {
        $lockKey = "account_locked:{$user->id}";
        cache()->put($lockKey, true, now()->addMinutes(30));

        activity()
            ->causedBy($user)
            ->log('Account locked due to failed login attempts');
    }

    protected function clearFailedLoginAttempts(User $user): void
    {
        $cacheKey = "failed_login_attempts:{$user->id}";
        $lockKey = "account_locked:{$user->id}";
        
        cache()->forget($cacheKey);
        cache()->forget($lockKey);
    }
}
Role-Based Access Control Implementation
The role-based access control (RBAC) system provides fine-grained permission management that aligns with the hierarchical structure and diverse responsibilities within a college environment. The system implements both role-based and permission-based access control, allowing for flexible security configurations that can adapt to changing institutional requirements.
The RBAC implementation utilizes Laravel's built-in authorization features enhanced with custom policies and middleware to provide comprehensive access control across all system resources. The system supports dynamic permission assignment, role inheritance, and context-aware authorization that considers factors such as department affiliation, course assignments, and student-advisor relationships.
The permission system is organized around functional areas including user management, academic operations, financial management, and administrative functions. Each permission is carefully scoped to provide the minimum necessary access while supporting efficient workflow operations.
The RolePermissionSeeder class establishes the foundational roles and permissions for the system.
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'manage-user-roles',
            
            // Student Management
            'view-students',
            'create-students',
            'edit-students',
            'delete-students',
            'view-student-records',
            'edit-student-records',
            'view-student-financial',
            'manage-student-enrollment',
            
            // Faculty Management
            'view-faculty',
            'create-faculty',
            'edit-faculty',
            'delete-faculty',
            'assign-faculty-courses',
            'view-faculty-workload',
            
            // Course Management
            'view-courses',
            'create-courses',
            'edit-courses',
            'delete-courses',
            'manage-course-sections',
            'view-course-enrollments',
            
            // Academic Management
            'manage-terms',
            'manage-departments',
            'view-academic-reports',
            'manage-grades',
            'view-grades',
            'edit-grades',
            'finalize-grades',
            
            // Financial Management
            'view-financial-reports',
            'manage-student-accounts',
            'process-payments',
            'manage-financial-aid',
            'view-financial-transactions',
            'create-financial-transactions',
            
            // Administrative
            'manage-system-settings',
            'view-audit-logs',
            'manage-announcements',
            'generate-reports',
            'backup-system',
            
            // Attendance Management
            'view-attendance',
            'record-attendance',
            'edit-attendance',
            'view-attendance-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createAdminRole();
        $this->createFacultyRole();
        $this->createStudentRole();
        $this->createStaffRole();
        $this->createRegistrarRole();
        $this->createFinanceRole();
    }

    protected function createAdminRole()
    {
        $admin = Role::create(['name' => 'admin']);
        
        // Admin has all permissions
        $admin->givePermissionTo(Permission::all());
    }

    protected function createFacultyRole()
    {
        $faculty = Role::create(['name' => 'faculty']);
        
        $facultyPermissions = [
            'view-students',
            'view-student-records',
            'view-courses',
            'view-course-enrollments',
            'manage-grades',
            'view-grades',
            'edit-grades',
            'view-attendance',
            'record-attendance',
            'edit-attendance',
            'view-attendance-reports',
        ];
        
        $faculty->givePermissionTo($facultyPermissions);
    }

    protected function createStudentRole()
    {
        $student = Role::create(['name' => 'student']);
        
        $studentPermissions = [
            'view-courses',
            'view-grades',
            'view-attendance',
        ];
        
        $student->givePermissionTo($studentPermissions);
    }

    protected function createStaffRole()
    {
        $staff = Role::create(['name' => 'staff']);
        
        $staffPermissions = [
            'view-users',
            'view-students',
            'view-faculty',
            'view-courses',
            'manage-announcements',
        ];
        
        $staff->givePermissionTo($staffPermissions);
    }

    protected function createRegistrarRole()
    {
        $registrar = Role::create(['name' => 'registrar']);
        
        $registrarPermissions = [
            'view-students',
            'create-students',
            'edit-students',
            'view-student-records',
            'edit-student-records',
            'manage-student-enrollment',
            'view-courses',
            'create-courses',
            'edit-courses',
            'manage-course-sections',
            'view-course-enrollments',
            'manage-terms',
            'manage-departments',
            'view-academic-reports',
            'generate-reports',
        ];
        
        $registrar->givePermissionTo($registrarPermissions);
    }

    protected function createFinanceRole()
    {
        $finance = Role::create(['name' => 'finance']);
        
        $financePermissions = [
            'view-students',
            'view-student-financial',
            'view-financial-reports',
            'manage-student-accounts',
            'process-payments',
            'manage-financial-aid',
            'view-financial-transactions',
            'create-financial-transactions',
            'generate-reports',
        ];
        
        $finance->givePermissionTo($financePermissions);
    }
}
The authorization system implements custom policies for complex access control scenarios that go beyond simple role-based permissions.
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAnyPermission(['view-students', 'manage-student-enrollment']);
    }

    public function view(User $user, Student $student)
    {
        // Admin and registrar can view all students
        if ($user->hasAnyPermission(['view-students', 'edit-students'])) {
            return true;
        }

        // Faculty can view students in their courses or advisees
        if ($user->role === 'faculty' && $user->faculty) {
            // Check if faculty is advisor
            if ($student->advisor_faculty_id === $user->faculty->id) {
                return true;
            }

            // Check if student is enrolled in faculty's courses
            $facultyCourses = $user->faculty->courseSections()
                                          ->whereHas('enrollments', function ($query) use ($student) {
                                              $query->where('student_id', $student->id);
                                          })
                                          ->exists();
            
            if ($facultyCourses) {
                return true;
            }
        }

        // Students can view their own record
        if ($user->role === 'student' && $user->student && $user->student->id === $student->id) {
            return true;
        }

        // Parents can view their child's record
        if ($user->role === 'parent') {
            // Implementation depends on parent-student relationship model
            return $this->isParentOfStudent($user, $student);
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create-students');
    }

    public function update(User $user, Student $student)
    {
        // Admin and registrar can edit all students
        if ($user->hasPermissionTo('edit-students')) {
            return true;
        }

        // Students can edit limited fields of their own record
        if ($user->role === 'student' && $user->student && $user->student->id === $student->id) {
            return true; // Limited fields only - enforced in controller
        }

        return false;
    }

    public function delete(User $user, Student $student)
    {
        return $user->hasPermissionTo('delete-students');
    }

    public function viewFinancialInfo(User $user, Student $student)
    {
        // Finance staff can view all student financial info
        if ($user->hasPermissionTo('view-student-financial')) {
            return true;
        }

        // Students can view their own financial info
        if ($user->role === 'student' && $user->student && $user->student->id === $student->id) {
            return true;
        }

        // Parents can view their child's financial info
        if ($user->role === 'parent') {
            return $this->isParentOfStudent($user, $student);
        }

        return false;
    }

    public function manageEnrollment(User $user, Student $student)
    {
        // Registrar and admin can manage all enrollments
        if ($user->hasPermissionTo('manage-student-enrollment')) {
            return true;
        }

        // Students can manage their own enrollment during registration periods
        if ($user->role === 'student' && $user->student && $user->student->id === $student->id) {
            // Check if registration is open
            return $this->isRegistrationOpen();
        }

        return false;
    }

    protected function isParentOfStudent(User $user, Student $student): bool
    {
        // Implementation depends on parent-student relationship model
        // This is a placeholder for the actual relationship check
        return false;
    }

    protected function isRegistrationOpen(): bool
    {
        // Check if current date is within registration period
        $currentTerm = \App\Models\Term::where('status', 'registration_open')->first();
        
        if (!$currentTerm) {
            return false;
        }

        $now = now();
        return $now->between($currentTerm->registration_start_date, $currentTerm->registration_end_date);
    }
}
The GradePolicy implements complex authorization logic for grade management that considers course assignments and academic hierarchy.
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Grade;
use App\Models\Enrollment;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAnyPermission(['view-grades', 'manage-grades']);
    }

    public function view(User $user, Grade $grade)
    {
        $enrollment = $grade->enrollment;
        $student = $enrollment->student;
        $courseSection = $enrollment->courseSection;

        // Admin and registrar can view all grades
        if ($user->hasPermissionTo('view-grades')) {
            return true;
        }

        // Faculty can view grades for their courses
        if ($user->role === 'faculty' && $user->faculty) {
            if ($courseSection->instructor_id === $user->faculty->id) {
                return true;
            }
        }

        // Students can view their own grades
        if ($user->role === 'student' && $user->student && $user->student->id === $student->id) {
            // Only if grade is finalized or in progress
            return in_array($grade->grade_status, ['final', 'in_progress']);
        }

        // Advisors can view their advisees' grades
        if ($user->role === 'faculty' && $user->faculty && $student->advisor_faculty_id === $user->faculty->id) {
            return $grade->grade_status === 'final';
        }

        return false;
    }

    public function create(User $user, Enrollment $enrollment)
    {
        $courseSection = $enrollment->courseSection;

        // Admin can create grades for any course
        if ($user->hasPermissionTo('manage-grades')) {
            return true;
        }

        // Faculty can create grades for their courses
        if ($user->role === 'faculty' && $user->faculty) {
            return $courseSection->instructor_id === $user->faculty->id;
        }

        return false;
    }

    public function update(User $user, Grade $grade)
    {
        $enrollment = $grade->enrollment;
        $courseSection = $enrollment->courseSection;

        // Cannot edit finalized grades unless admin
        if ($grade->grade_status === 'final' && !$user->hasPermissionTo('manage-grades')) {
            return false;
        }

        // Admin can edit any grade
        if ($user->hasPermissionTo('manage-grades')) {
            return true;
        }

        // Faculty can edit grades for their courses
        if ($user->role === 'faculty' && $user->faculty) {
            if ($courseSection->instructor_id === $user->faculty->id) {
                // Check if grade change deadline has passed
                return $this->isWithinGradeChangeDeadline($courseSection);
            }
        }

        return false;
    }

    public function finalize(User $user, Grade $grade)
    {
        $enrollment = $grade->enrollment;
        $courseSection = $enrollment->courseSection;

        // Admin can finalize any grade
        if ($user->hasPermissionTo('finalize-grades')) {
            return true;
        }

        // Faculty can finalize grades for their courses
        if ($user->role === 'faculty' && $user->faculty) {
            if ($courseSection->instructor_id === $user->faculty->id) {
                // Check if within grade submission deadline
                return $this->isWithinGradeSubmissionDeadline($courseSection);
            }
        }

        return false;
    }

    public function delete(User $user, Grade $grade)
    {
        // Only admin can delete grades, and only if not finalized
        return $user->hasPermissionTo('manage-grades') && $grade->grade_status !== 'final';
    }

    protected function isWithinGradeChangeDeadline($courseSection): bool
    {
        $term = $courseSection->term;
        $deadline = $term->grades_due_date ?? $term->end_date->addDays(7);
        
        return now()->lte($deadline);
    }

    protected function isWithinGradeSubmissionDeadline($courseSection): bool
    {
        $term = $courseSection->term;
        $deadline = $term->grades_due_date ?? $term->end_date->addDays(3);
        
        return now()->lte($deadline);
    }
}
Multi-Factor Authentication Implementation
Multi-factor authentication (MFA) provides an additional layer of security for sensitive user accounts, particularly administrators, faculty members, and users with elevated privileges. The MFA implementation supports multiple authentication methods including email-based codes, SMS verification, and time-based one-time passwords (TOTP) for maximum flexibility and security.
The MFA system is designed to be optional for most users but can be enforced for specific roles or users based on institutional security policies. The implementation includes backup codes for account recovery and comprehensive logging for security monitoring and compliance purposes.
The SecurityService class manages MFA operations and security-related functionality.
<?php

namespace App\Services;

use App\Models\User;
use App\Models\MFAToken;
use App\Notifications\MFACodeNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class SecurityService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function requiresMFA(User $user): bool
    {
        // Check if MFA is required for user's role
        $mfaRequiredRoles = config('auth.mfa_required_roles', ['admin']);
        
        if (in_array($user->role, $mfaRequiredRoles)) {
            return true;
        }

        // Check if user has enabled MFA voluntarily
        return $user->mfa_enabled ?? false;
    }

    public function generateMFAToken(User $user): string
    {
        $token = Str::random(32);
        
        // Store MFA session data
        Cache::put("mfa_session:{$token}", [
            'user_id' => $user->id,
            'created_at' => now(),
            'verified' => false
        ], now()->addMinutes(10));

        return $token;
    }

    public function getAvailableMFAMethods(User $user): array
    {
        $methods = [];

        // Email is always available
        $methods[] = [
            'type' => 'email',
            'label' => 'Email Code',
            'target' => $this->maskEmail($user->email)
        ];

        // SMS if phone number is available
        if ($user->phone) {
            $methods[] = [
                'type' => 'sms',
                'label' => 'SMS Code',
                'target' => $this->maskPhone($user->phone)
            ];
        }

        // TOTP if configured
        if ($user->totp_secret) {
            $methods[] = [
                'type' => 'totp',
                'label' => 'Authenticator App',
                'target' => 'Authenticator App'
            ];
        }

        return $methods;
    }

    public function sendMFACode(User $user, string $method, string $mfaToken): bool
    {
        try {
            $code = $this->generateMFACode();
            
            // Store the code
            Cache::put("mfa_code:{$mfaToken}:{$method}", $code, now()->addMinutes(5));

            switch ($method) {
                case 'email':
                    $user->notify(new MFACodeNotification($code, 'email'));
                    break;
                    
                case 'sms':
                    $this->sendSMSCode($user->phone, $code);
                    break;
                    
                case 'totp':
                    // TOTP doesn't require sending a code
                    return true;
                    
                default:
                    return false;
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send MFA code', [
                'user_id' => $user->id,
                'method' => $method,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    public function verifyMFACode(string $mfaToken, string $code, string $method): array
    {
        $sessionData = Cache::get("mfa_session:{$mfaToken}");
        
        if (!$sessionData) {
            return [
                'success' => false,
                'message' => 'MFA session expired. Please log in again.'
            ];
        }

        $user = User::find($sessionData['user_id']);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid MFA session.'
            ];
        }

        $isValid = false;

        switch ($method) {
            case 'email':
            case 'sms':
                $storedCode = Cache::get("mfa_code:{$mfaToken}:{$method}");
                $isValid = $storedCode && $storedCode === $code;
                break;
                
            case 'totp':
                $isValid = $this->verifyTOTPCode($user, $code);
                break;
        }

        if ($isValid) {
            // Mark MFA session as verified
            Cache::put("mfa_session:{$mfaToken}", array_merge($sessionData, [
                'verified' => true,
                'verified_at' => now()
            ]), now()->addMinutes(5));

            // Generate API token if needed
            $token = null;
            if (request()->expectsJson() || request()->is('api/*')) {
                $token = $user->createToken('auth-token', ['*'], now()->addHours(24))->plainTextToken;
            }

            // Clear MFA codes
            Cache::forget("mfa_code:{$mfaToken}:{$method}");

            return [
                'success' => true,
                'user' => $user,
                'token' => $token
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid verification code.'
        ];
    }

    public function setupTOTP(User $user): array
    {
        $secret = $this->google2fa->generateSecretKey();
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Store secret temporarily until confirmed
        Cache::put("totp_setup:{$user->id}", $secret, now()->addMinutes(30));

        return [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'manual_entry_key' => $secret
        ];
    }

    public function confirmTOTPSetup(User $user, string $code): bool
    {
        $secret = Cache::get("totp_setup:{$user->id}");
        
        if (!$secret) {
            return false;
        }

        $isValid = $this->google2fa->verifyKey($secret, $code);

        if ($isValid) {
            // Save TOTP secret to user
            $user->update([
                'totp_secret' => encrypt($secret),
                'mfa_enabled' => true
            ]);

            // Generate backup codes
            $backupCodes = $this->generateBackupCodes($user);

            // Clear setup cache
            Cache::forget("totp_setup:{$user->id}");

            return true;
        }

        return false;
    }

    protected function verifyTOTPCode(User $user, string $code): bool
    {
        if (!$user->totp_secret) {
            return false;
        }

        try {
            $secret = decrypt($user->totp_secret);
            return $this->google2fa->verifyKey($secret, $code);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function generateMFACode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    protected function generateBackupCodes(User $user): array
    {
        $codes = [];
        
        for ($i = 0; $i < 10; $i++) {
            $codes[] = Str::random(8);
        }

        // Store encrypted backup codes
        $user->update([
            'backup_codes' => encrypt(json_encode($codes))
        ]);

        return $codes;
    }

    protected function sendSMSCode(string $phone, string $code): void
    {
        // Implementation depends on SMS service provider
        // This is a placeholder for actual SMS sending logic
    }

    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $username = $parts[0];
        $domain = $parts[1];
        
        $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
        
        return $maskedUsername . '@' . $domain;
    }

    protected function maskPhone(string $phone): string
    {
        return substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
    }

    public function checkForSecurityAlerts(User $user, string $ipAddress, string $userAgent): void
    {
        // Check for login from new location
        $recentLogins = activity()
            ->causedBy($user)
            ->where('description', 'User logged in')
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        $knownIPs = $recentLogins->pluck('properties.ip_address')->unique();
        
        if (!$knownIPs->contains($ipAddress)) {
            // Send security alert for new location
            $user->notify(new \App\Notifications\SecurityAlertNotification([
                'type' => 'new_location_login',
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'timestamp' => now()
            ]));
        }
    }
}
Password Management and Security
Password management is a critical component of the authentication system that ensures user accounts remain secure while providing convenient password recovery and update mechanisms. The system implements comprehensive password policies, secure reset procedures, and password history tracking to prevent reuse of compromised passwords.
The password security implementation includes strength validation, breach detection using the HaveIBeenPwned API, and secure storage using Laravel's built-in hashing mechanisms. The system also provides password expiration policies for sensitive accounts and supports password complexity requirements that can be customized based on institutional security policies.
The PasswordResetController handles secure password reset operations with comprehensive validation and security measures.
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
        $this->middleware('guest');
    }

    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->passwordResetService->sendResetLink($request->email);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent to your email address.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => ['email' => [$result['message']]]
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset link. Please try again later.',
                'errors' => ['general' => ['Password reset service temporarily unavailable.']]
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->passwordResetService->resetPassword(
                $request->email,
                $request->token,
                $request->password
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset successfully. You can now log in with your new password.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => ['token' => [$result['message']]]
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again.',
                'errors' => ['general' => ['Password reset failed.']]
            ], 500);
        }
    }

    public function validateResetToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email'
        ]);

        try {
            $isValid = $this->passwordResetService->validateResetToken(
                $request->email,
                $request->token
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'valid' => $isValid,
                    'email' => $request->email
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate reset token.'
            ], 500);
        }
    }
}
The PasswordResetService class implements secure password reset functionality with comprehensive security measures.
<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordReset;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetService
{
    public function sendResetLink(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Don't reveal whether email exists for security
            return [
                'success' => true,
                'message' => 'If an account with that email exists, a password reset link has been sent.'
            ];
        }

        if ($user->status !== 'active') {
            return [
                'success' => false,
                'message' => 'Account is not active. Please contact the administrator.'
            ];
        }

        // Check rate limiting
        $recentResets = PasswordReset::where('email', $email)
                                   ->where('created_at', '>=', now()->subMinutes(5))
                                   ->count();

        if ($recentResets >= 3) {
            return [
                'success' => false,
                'message' => 'Too many password reset requests. Please wait before trying again.'
            ];
        }

        DB::beginTransaction();

        try {
            // Delete existing reset tokens for this email
            PasswordReset::where('email', $email)->delete();

            // Generate new reset token
            $token = Str::random(64);
            $hashedToken = Hash::make($token);

            // Store reset token
            PasswordReset::create([
                'email' => $email,
                'token' => $hashedToken,
                'created_at' => now()
            ]);

            // Send reset email
            $user->notify(new PasswordResetNotification($token));

            // Log password reset request
            activity()
                ->causedBy($user)
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Password reset requested');

            DB::commit();

            return [
                'success' => true,
                'message' => 'Password reset link sent successfully.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function resetPassword(string $email, string $token, string $newPassword): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid reset token.'
            ];
        }

        // Find valid reset token
        $resetRecord = PasswordReset::where('email', $email)
                                  ->where('created_at', '>=', now()->subHours(1))
                                  ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return [
                'success' => false,
                'message' => 'Invalid or expired reset token.'
            ];
        }

        DB::beginTransaction();

        try {
            // Check password history
            if ($this->isPasswordReused($user, $newPassword)) {
                return [
                    'success' => false,
                    'message' => 'Cannot reuse a recent password. Please choose a different password.'
                ];
            }

            // Update password
            $user->update([
                'password' => Hash::make($newPassword),
                'password_changed_at' => now()
            ]);

            // Store password history
            $this->storePasswordHistory($user, $newPassword);

            // Delete reset token
            PasswordReset::where('email', $email)->delete();

            // Revoke all existing tokens
            $user->tokens()->delete();

            // Log password reset
            activity()
                ->causedBy($user)
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Password reset completed');

            DB::commit();

            return [
                'success' => true,
                'message' => 'Password reset successfully.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function validateResetToken(string $email, string $token): bool
    {
        $resetRecord = PasswordReset::where('email', $email)
                                  ->where('created_at', '>=', now()->subHours(1))
                                  ->first();

        return $resetRecord && Hash::check($token, $resetRecord->token);
    }

    protected function isPasswordReused(User $user, string $newPassword): bool
    {
        $passwordHistory = $user->passwordHistory()
                                ->orderBy('created_at', 'desc')
                                ->limit(5) // Check last 5 passwords
                                ->get();

        foreach ($passwordHistory as $history) {
            if (Hash::check($newPassword, $history->password_hash)) {
                return true;
            }
        }

        return false;
    }

    protected function storePasswordHistory(User $user, string $password): void
    {
        $user->passwordHistory()->create([
            'password_hash' => Hash::make($password),
            'created_at' => now()
        ]);

        // Keep only last 10 password history records
        $oldRecords = $user->passwordHistory()
                           ->orderBy('created_at', 'desc')
                           ->skip(10)
                           ->pluck('id');

        if ($oldRecords->isNotEmpty()) {
            $user->passwordHistory()->whereIn('id', $oldRecords)->delete();
        }
    }
}
Session Management and Security
Session management provides secure handling of user sessions with appropriate timeout policies, session rotation, and comprehensive security measures to prevent session hijacking and fixation attacks. The system implements both web-based session management and API token management to support different client types while maintaining consistent security standards.
The session security implementation includes secure cookie configuration, session data encryption, and automatic session cleanup to prevent unauthorized access and maintain system performance. The system also provides session monitoring capabilities for security analysis and compliance reporting.
Session configuration should be properly secured in the session configuration file.
<?php

return [
    'driver' => env('SESSION_DRIVER', 'redis'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => true,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', Str::slug(env('APP_NAME', 'laravel'), '_').'_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE', true),
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
];
The SessionManagementService provides comprehensive session handling and security features.
<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class SessionManagementService
{
    public function createSession(User $user, Request $request): void
    {
        $sessionData = [
            'user_id' => $user->id,
            'session_id' => Session::getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
            'last_activity' => now(),
            'is_active' => true
        ];

        UserSession::create($sessionData);

        // Store additional session data
        Session::put('user_id', $user->id);
        Session::put('login_time', now());
        Session::put('last_activity', now());
        Session::put('ip_address', $request->ip());
    }

    public function updateSessionActivity(User $user): void
    {
        $sessionId = Session::getId();
        
        UserSession::where('session_id', $sessionId)
                  ->where('user_id', $user->id)
                  ->update(['last_activity' => now()]);

        Session::put('last_activity', now());
    }

    public function terminateSession(string $sessionId, int $userId): bool
    {
        DB::beginTransaction();

        try {
            // Mark session as inactive
            UserSession::where('session_id', $sessionId)
                      ->where('user_id', $userId)
                      ->update([
                          'is_active' => false,
                          'logout_at' => now()
                      ]);

            // If current session, clear session data
            if (Session::getId() === $sessionId) {
                Session::flush();
                Session::regenerate();
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function terminateAllUserSessions(User $user, string $exceptSessionId = null): int
    {
        $query = UserSession::where('user_id', $user->id)
                           ->where('is_active', true);

        if ($exceptSessionId) {
            $query->where('session_id', '!=', $exceptSessionId);
        }

        $sessions = $query->get();
        $terminatedCount = 0;

        foreach ($sessions as $session) {
            if ($this->terminateSession($session->session_id, $user->id)) {
                $terminatedCount++;
            }
        }

        // Revoke API tokens
        $user->tokens()->delete();

        return $terminatedCount;
    }

    public function getActiveSessions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return UserSession::where('user_id', $user->id)
                         ->where('is_active', true)
                         ->orderBy('last_activity', 'desc')
                         ->get()
                         ->map(function ($session) {
                             return [
                                 'id' => $session->session_id,
                                 'ip_address' => $session->ip_address,
                                 'user_agent' => $this->parseUserAgent($session->user_agent),
                                 'login_at' => $session->login_at,
                                 'last_activity' => $session->last_activity,
                                 'is_current' => $session->session_id === Session::getId()
                             ];
                         });
    }

    public function cleanupExpiredSessions(): int
    {
        $expiredTime = now()->subMinutes(config('session.lifetime'));
        
        $expiredSessions = UserSession::where('last_activity', '<', $expiredTime)
                                     ->where('is_active', true)
                                     ->get();

        $cleanedCount = 0;

        foreach ($expiredSessions as $session) {
            if ($this->terminateSession($session->session_id, $session->user_id)) {
                $cleanedCount++;
            }
        }

        return $cleanedCount;
    }

    public function detectSuspiciousActivity(User $user, Request $request): array
    {
        $alerts = [];
        $currentIP = $request->ip();
        $currentUserAgent = $request->userAgent();

        // Check for multiple concurrent sessions from different IPs
        $activeSessions = UserSession::where('user_id', $user->id)
                                    ->where('is_active', true)
                                    ->where('last_activity', '>=', now()->subMinutes(30))
                                    ->get();

        $uniqueIPs = $activeSessions->pluck('ip_address')->unique();
        
        if ($uniqueIPs->count() > 3) {
            $alerts[] = [
                'type' => 'multiple_locations',
                'message' => 'Multiple active sessions from different locations detected',
                'severity' => 'high'
            ];
        }

        // Check for rapid location changes
        $recentSessions = UserSession::where('user_id', $user->id)
                                    ->where('login_at', '>=', now()->subHours(1))
                                    ->orderBy('login_at', 'desc')
                                    ->limit(5)
                                    ->get();

        $recentIPs = $recentSessions->pluck('ip_address')->unique();
        
        if ($recentIPs->count() > 2) {
            $alerts[] = [
                'type' => 'rapid_location_change',
                'message' => 'Rapid location changes detected in recent logins',
                'severity' => 'medium'
            ];
        }

        // Check for unusual user agent
        $commonUserAgents = UserSession::where('user_id', $user->id)
                                      ->where('created_at', '>=', now()->subDays(30))
                                      ->pluck('user_agent')
                                      ->unique();

        if (!$commonUserAgents->contains($currentUserAgent) && $commonUserAgents->count() > 0) {
            $alerts[] = [
                'type' => 'new_device',
                'message' => 'Login from new device or browser detected',
                'severity' => 'low'
            ];
        }

        return $alerts;
    }

    protected function parseUserAgent(string $userAgent): array
    {
        // Simple user agent parsing - in production, use a proper library
        $browser = 'Unknown';
        $platform = 'Unknown';

        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }

        if (strpos($userAgent, 'Windows') !== false) {
            $platform = 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $platform = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $platform = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $platform = 'Android';
        } elseif (strpos($userAgent, 'iOS') !== false) {
            $platform = 'iOS';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
            'full' => $userAgent
        ];
    }
}
Conclusion
This comprehensive authentication and authorization implementation provides a robust security foundation for the College Management System. The system implements industry best practices for user authentication, role-based access control, multi-factor authentication, and session management while maintaining flexibility for institutional customization and future enhancements.
The implementation emphasizes security at every level, from password policies and breach detection to comprehensive audit logging and suspicious activity monitoring. The role-based access control system provides fine-grained permissions that align with the complex organizational structure of educational institutions while maintaining ease of administration and user experience.
The next phase will build upon this security foundation to implement specific functional modules including student management, course administration, and financial operations, all protected by the comprehensive authentication and authorization system established in this phase.