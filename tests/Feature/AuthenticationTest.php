<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_client_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/client/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get('/admin/email/verify');

        $response->assertStatus(200);
    }

    public function test_session_management_with_sanctum(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        // Test session creation
        $this->actingAs($user);
        $this->assertAuthenticated();

        // Test session persistence
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $this->assertAuthenticated();
    }
}