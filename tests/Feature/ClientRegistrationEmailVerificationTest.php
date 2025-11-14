<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClientRegistrationEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_registration_sends_email_verification()
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test Client',
            'email' => 'testclient@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'client',
        ]);

        // Check user was created
        $user = User::where('email', 'testclient@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('client', $user->role);
        $this->assertNull($user->email_verified_at);

        // Check email verification was sent
        Mail::assertSent(\Illuminate\Auth\Notifications\VerifyEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Check redirect to verification notice
        $response->assertRedirect(route('client.verification.notice'));
    }

    public function test_client_can_verify_email()
    {
        $user = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        // Generate verification URL
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        // Check user is verified
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);

        // Check redirect to client dashboard
        $response->assertRedirect(route('client.dashboard'));
    }

    public function test_client_can_resend_verification_email()
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('verification.send'));

        Mail::assertSent(\Illuminate\Auth\Notifications\VerifyEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');
    }

    public function test_verified_client_redirects_to_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));

        $response->assertRedirect(route('client.dashboard'));
    }
}