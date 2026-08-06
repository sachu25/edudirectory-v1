<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportsPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_admin_can_access_reports_and_staff_tab()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $user = User::factory()->create([
            'status' => 'active',
            'role_id' => $adminRole->id
        ]);

        $response = $this->actingAs($user)->get('/reports?report_tab=staff');
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_cannot_access_staff_reports_tab()
    {
        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create([
            'status' => 'active',
            'role_id' => $userRole->id
        ]);

        $response = $this->actingAs($user)->get('/reports?report_tab=staff');
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_export_reports()
    {
        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create([
            'status' => 'active',
            'role_id' => $userRole->id
        ]);

        $response = $this->actingAs($user)->get('/reports/export/excel');
        $response->assertStatus(403);
    }
}
