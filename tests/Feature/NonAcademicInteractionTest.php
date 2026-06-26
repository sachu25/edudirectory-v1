<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\NonAcademicClient;
use App\Models\NonAcademicInteraction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NonAcademicInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_non_academic_interactions_index()
    {
        $user = User::factory()->create([
            'status' => 'active'
        ]);
        
        $response = $this->actingAs($user)
                         ->get('/non-academic-interactions');
                         
        $response->assertStatus(200);
    }

    public function test_can_log_non_academic_interaction()
    {
        $user = User::factory()->create([
            'status' => 'active'
        ]);
        
        $client = NonAcademicClient::create([
            'name' => 'Acme Corporation'
        ]);
        
        $response = $this->actingAs($user)
                         ->post('/non-academic-interactions', [
                             'non_academic_client_id' => $client->id,
                             'user_id' => $user->id,
                             'contact_date' => now()->format('Y-m-d H:i'),
                             'contact_mode' => 'Phone Call',
                             'interaction_status' => 'Interested',
                             'purpose' => 'Campus Placement',
                             'client_response' => 'Positive'
                         ]);
                         
        $response->assertStatus(200);
        $this->assertDatabaseHas('non_academic_interactions', [
            'non_academic_client_id' => $client->id,
            'user_id' => $user->id,
            'client_response' => 'Positive'
        ]);
    }
}
