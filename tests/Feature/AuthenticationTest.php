<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Class AuthenticationTest
 * 
 * Test cases for authentication functionality.
 * 
 * @package Tests\Feature
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test login page displays correctly.
     *
     * @return void
     */
    public function test_login_page_displays()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('Login');
    }

    /**
     * Test user can login with valid credentials.
     *
     * @return void
     */
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test user cannot login with invalid credentials.
     *
     * @return void
     */
    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test user cannot login with inactive account.
     *
     * @return void
     */
    public function test_user_cannot_login_with_inactive_account()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test login validation.
     *
     * @return void
     */
    public function test_login_validation()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /**
     * Test email validation on login.
     *
     * @return void
     */
    public function test_email_validation_on_login()
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test registration page displays correctly.
     *
     * @return void
     */
    public function test_registration_page_displays()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSee('Register');
    }

    /**
     * Test user can register with valid data.
     *
     * @return void
     */
    public function test_user_can_register_with_valid_data()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male'
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/dashboard');
        
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test registration validation.
     *
     * @return void
     */
    public function test_registration_validation()
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors([
            'first_name', 'last_name', 'email', 'password', 'date_of_birth', 'gender'
        ]);
    }

    /**
     * Test password confirmation validation.
     *
     * @return void
     */
    public function test_password_confirmation_validation()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male'
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test duplicate email validation on registration.
     *
     * @return void
     */
    public function test_duplicate_email_validation_on_registration()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male'
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test user can logout.
     *
     * @return void
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test password reset page displays.
     *
     * @return void
     */
    public function test_password_reset_page_displays()
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.email');
    }

    /**
     * Test password reset email can be sent.
     *
     * @return void
     */
    public function test_password_reset_email_can_be_sent()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/password/email', [
            'email' => 'test@example.com'
        ]);

        $response->assertSessionHas('status');
    }

    /**
     * Test password reset email validation.
     *
     * @return void
     */
    public function test_password_reset_email_validation()
    {
        $response = $this->post('/password/email', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test authenticated user is redirected from login page.
     *
     * @return void
     */
    public function test_authenticated_user_redirected_from_login()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test authenticated user is redirected from register page.
     *
     * @return void
     */
    public function test_authenticated_user_redirected_from_register()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test guest user is redirected to login from protected routes.
     *
     * @return void
     */
    public function test_guest_redirected_to_login_from_protected_routes()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test remember me functionality.
     *
     * @return void
     */
    public function test_remember_me_functionality()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => true
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        
        // Check if remember token is set
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }
}
