<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_force_password_flag_is_redirected_to_change_notice(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'force_password_change' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('force-password-change.notice'));
    }

    public function test_user_cannot_update_with_weak_password(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'force_password_change' => true,
        ]);

        $response = $this->actingAs($user)->post('/force-password-change', [
            'current_password' => 'password',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertTrue($user->fresh()->force_password_change);
    }

    public function test_user_can_update_with_strong_password(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'force_password_change' => true,
        ]);

        $response = $this->actingAs($user)->post('/force-password-change', [
            'current_password' => 'password',
            'password' => 'StrongPass123!#Complex',
            'password_confirmation' => 'StrongPass123!#Complex',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertFalse($user->fresh()->force_password_change);
    }
}
