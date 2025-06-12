<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class AcademicYearActiveConstraintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'Admin']);
        
        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
        
        // Give admin permission to manage settings
        $this->admin->givePermissionTo('manage-settings');
    }

    /** @test */
    public function only_one_academic_year_can_be_active_at_a_time()
    {
        // Create first academic year and set as active
        $year1 = AcademicYear::create([
            'name' => '2023/2024',
            'code' => '2023-24',
            'start_date' => '2023-09-01',
            'end_date' => '2024-08-31',
            'is_active' => true,
            'is_current' => false,
        ]);

        // Create second academic year and set as active
        $year2 = AcademicYear::create([
            'name' => '2024/2025',
            'code' => '2024-25',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'is_active' => true,
            'is_current' => false,
        ]);

        // Refresh models to get updated data
        $year1->refresh();
        $year2->refresh();

        // Only the second year should be active
        $this->assertFalse($year1->is_active);
        $this->assertTrue($year2->is_active);

        // Verify only one active year exists in database
        $this->assertEquals(1, AcademicYear::where('is_active', true)->count());
    }

    /** @test */
    public function set_as_active_method_works_correctly()
    {
        // Create two academic years
        $year1 = AcademicYear::create([
            'name' => '2023/2024',
            'code' => '2023-24',
            'start_date' => '2023-09-01',
            'end_date' => '2024-08-31',
            'is_active' => true,
            'is_current' => false,
        ]);

        $year2 = AcademicYear::create([
            'name' => '2024/2025',
            'code' => '2024-25',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'is_active' => false,
            'is_current' => false,
        ]);

        // Use setAsActive method on year2
        $year2->setAsActive();

        // Refresh models
        $year1->refresh();
        $year2->refresh();

        // Only year2 should be active
        $this->assertFalse($year1->is_active);
        $this->assertTrue($year2->is_active);

        // Verify only one active year exists
        $this->assertEquals(1, AcademicYear::where('is_active', true)->count());
    }

    /** @test */
    public function controller_enforces_single_active_year_on_create()
    {
        // Create first active year
        AcademicYear::create([
            'name' => '2023/2024',
            'code' => '2023-24',
            'start_date' => '2023-09-01',
            'end_date' => '2024-08-31',
            'is_active' => true,
            'is_current' => false,
        ]);

        // Create second year via controller
        $response = $this->actingAs($this->admin)->post(route('academic-years.store'), [
            'name' => '2024/2025',
            'code' => '2024-25',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'is_active' => true,
            'is_current' => false,
        ]);

        $response->assertRedirect(route('academic-years.index'));

        // Verify only one active year exists
        $this->assertEquals(1, AcademicYear::where('is_active', true)->count());
        
        // Verify the new year is the active one
        $activeYear = AcademicYear::where('is_active', true)->first();
        $this->assertEquals('2024/2025', $activeYear->name);
    }

    /** @test */
    public function controller_set_active_route_works()
    {
        // Create two academic years
        $year1 = AcademicYear::create([
            'name' => '2023/2024',
            'code' => '2023-24',
            'start_date' => '2023-09-01',
            'end_date' => '2024-08-31',
            'is_active' => true,
            'is_current' => false,
        ]);

        $year2 = AcademicYear::create([
            'name' => '2024/2025',
            'code' => '2024-25',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'is_active' => false,
            'is_current' => false,
        ]);

        // Use the setActive route
        $response = $this->actingAs($this->admin)
            ->put(route('academic-years.set-active', $year2));

        $response->assertRedirect(route('academic-years.index'));

        // Refresh models
        $year1->refresh();
        $year2->refresh();

        // Only year2 should be active
        $this->assertFalse($year1->is_active);
        $this->assertTrue($year2->is_active);

        // Verify only one active year exists
        $this->assertEquals(1, AcademicYear::where('is_active', true)->count());
    }
}
