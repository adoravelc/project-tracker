<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::create([
            'name' => 'Admin Energeek',
            'email' => 'admin@energeek.id',
            'password' => 'admPa$$Energeek',
        ]);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@energeek.id',
            'password' => 'admPa$$Energeek',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@energeek.id',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized();
    }
}
