<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\NonAcademicClient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NonAcademicClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_can_access_non_academic_clients_index()
    {
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $user = User::factory()->create([
            'status' => 'active',
            'role_id' => $adminRole->id
        ]);
        
        $response = $this->actingAs($user)
                         ->get('/non-academic-clients');
                         
        $response->assertStatus(200);
    }

    public function test_can_create_non_academic_client()
    {
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $user = User::factory()->create([
            'status' => 'active',
            'role_id' => $adminRole->id
        ]);
        
        $response = $this->actingAs($user)
                         ->post('/non-academic-clients', [
                             'name' => 'Acme Corporation',
                             'industry' => 'IT / Software',
                             'contacted_user_id' => $user->id,
                             'contact_reason' => 'Campus Placement'
                         ]);
                         
        $response->assertStatus(200);
        $this->assertDatabaseHas('non_academic_clients', [
            'name' => 'Acme Corporation',
            'industry' => 'IT / Software'
        ]);
    }
}
