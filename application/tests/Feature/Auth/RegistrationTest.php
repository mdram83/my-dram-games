<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected string $name = 'Test User';
    protected string $email = 'test@example.com';

    protected function getRegistrationPayload(): array
    {
        $registrationPayload = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $betaRegistrationCode = config('auth.beta_registration_code');

        if ($betaRegistrationCode !== null) {
            $registrationPayload['beta_registration_code'] = $betaRegistrationCode;
        }

        return $registrationPayload;
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_is_premium(): void
    {
        $response = $this->post('/register', $this->getRegistrationPayload());
        $user = User::where('name', '=', $this->name)->first();

        $this->assertAuthenticated();
        $this->assertTrue((bool) $user->premium);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
