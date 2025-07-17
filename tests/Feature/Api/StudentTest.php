<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->user->assignRole('admin'); // Give admin permissions
        $this->token = $this->user->createToken('test-device')->plainTextToken;
    }

    /**
     * Test getting list of students.
     */
    public function test_can_get_students_list()
    {
        // Create some test students
        Student::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/students');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'students' => [
                            '*' => [
                                'id',
                                'admission_number',
                                'status',
                                'first_name',
                                'last_name',
                                'email',
                            ]
                        ],
                        'pagination' => [
                            'current_page',
                            'last_page',
                            'per_page',
                            'total',
                        ],
                    ]
                ]);
    }

    /**
     * Test creating a new student.
     */
    public function test_can_create_student()
    {
        $studentData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->date(),
            'gender' => 'male',
            'address' => $this->faker->address,
            'faculty_id' => 1, // Assuming faculty exists
            'department_id' => 1, // Assuming department exists
            'guardian_name' => $this->faker->name,
            'guardian_phone' => $this->faker->phoneNumber,
            'guardian_email' => $this->faker->email,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/v1/students', $studentData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'student' => [
                            'id',
                            'admission_number',
                            'status',
                            'user' => [
                                'first_name',
                                'last_name',
                                'email',
                            ],
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'email' => $studentData['email'],
            'first_name' => $studentData['first_name'],
            'last_name' => $studentData['last_name'],
        ]);
    }

    /**
     * Test getting a specific student.
     */
    public function test_can_get_specific_student()
    {
        $student = Student::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/students/' . $student->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'student' => [
                            'id',
                            'admission_number',
                            'status',
                            'user' => [
                                'id',
                                'first_name',
                                'last_name',
                                'email',
                            ],
                        ]
                    ]
                ]);
    }

    /**
     * Test updating a student.
     */
    public function test_can_update_student()
    {
        $student = Student::factory()->create();

        $updateData = [
            'first_name' => 'Updated Name',
            'status' => 'inactive',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/v1/students/' . $student->id, $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'student'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $student->user_id,
            'first_name' => 'Updated Name',
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'inactive',
        ]);
    }

    /**
     * Test deleting a student.
     */
    public function test_can_delete_student()
    {
        $student = Student::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/v1/students/' . $student->id);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Student deleted successfully'
                ]);

        $this->assertSoftDeleted('students', [
            'id' => $student->id,
        ]);
    }

    /**
     * Test getting student statistics.
     */
    public function test_can_get_student_statistics()
    {
        // Create students with different statuses
        Student::factory()->count(3)->create(['status' => 'active']);
        Student::factory()->count(2)->create(['status' => 'graduated']);
        Student::factory()->count(1)->create(['status' => 'suspended']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/students/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total',
                        'active',
                        'graduated',
                        'suspended',
                        'recent_registrations',
                        'by_faculty',
                        'by_status',
                    ]
                ]);
    }

    /**
     * Test bulk actions on students.
     */
    public function test_can_perform_bulk_actions()
    {
        $students = Student::factory()->count(3)->create();
        $studentIds = $students->pluck('id')->toArray();

        $bulkData = [
            'action' => 'activate',
            'student_ids' => $studentIds,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/v1/students/bulk-action', $bulkData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'affected_count',
                        'action',
                    ]
                ]);

        // Verify all students are activated
        foreach ($students as $student) {
            $this->assertDatabaseHas('students', [
                'id' => $student->id,
                'status' => 'active',
            ]);
        }
    }

    /**
     * Test searching students.
     */
    public function test_can_search_students()
    {
        $student = Student::factory()->create();
        $user = $student->user;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/students/search?query=' . $user->first_name);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'students' => [
                            '*' => [
                                'id',
                                'admission_number',
                                'first_name',
                                'last_name',
                                'email',
                            ]
                        ],
                        'query',
                        'count',
                    ]
                ]);
    }

    /**
     * Test filtering students.
     */
    public function test_can_filter_students()
    {
        Student::factory()->count(2)->create(['status' => 'active']);
        Student::factory()->count(1)->create(['status' => 'inactive']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/students?status=active');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals(2, count($data['data']['students']));
    }

    /**
     * Test pagination.
     */
    public function test_students_are_paginated()
    {
        Student::factory()->count(25)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/students?per_page=10');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals(10, count($data['data']['students']));
        $this->assertEquals(10, $data['data']['pagination']['per_page']);
        $this->assertEquals(3, $data['data']['pagination']['last_page']);
    }

    /**
     * Test unauthorized access.
     */
    public function test_unauthorized_access_is_denied()
    {
        $response = $this->getJson('/api/v1/students');

        $response->assertStatus(401);
    }

    /**
     * Test invalid student ID returns 404.
     */
    public function test_invalid_student_id_returns_404()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/v1/students/99999');

        $response->assertStatus(404);
    }

    /**
     * Test validation errors on student creation.
     */
    public function test_validation_errors_on_student_creation()
    {
        $invalidData = [
            'first_name' => '', // Required field
            'email' => 'invalid-email', // Invalid email
            'faculty_id' => 99999, // Non-existent faculty
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/v1/students', $invalidData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ]);
    }
}
