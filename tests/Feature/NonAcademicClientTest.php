<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\NonAcademicClient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NonAcademicClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_non_academic_clients_index()
    {
        $user = User::factory()->create([
            'status' => 'active'
        ]);
        
        $response = $this->actingAs($user)
                         ->get('/non-academic-clients');
                         
        $response->assertStatus(200);
    }

    public function test_can_create_non_academic_client()
    {
        $user = User::factory()->create([
            'status' => 'active'
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
