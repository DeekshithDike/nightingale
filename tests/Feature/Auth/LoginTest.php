<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Specialty;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'patient@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
        ]);
        $user->patient()->create([
            'dob' => '1990-01-01',
            'gender' => 'male',
        ]);

        $payload = [
            'email' => 'patient@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id', 'name', 'email', 'phone', 'role', 'patient' => ['id', 'dob', 'gender']
                    ],
                    'token',
                ],
                'message',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ]);
    }

    /** @test */
    public function doctor_can_login_successfully()
    {
        $specialty = Specialty::factory()->create();
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
        ]);
        $user->doctor()->create([
            'specialty_id' => $specialty->id,
            'name' => 'Dr. Smith',
        ]);

        $payload = [
            'email' => 'doctor@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id', 'name', 'email', 'phone', 'role', 'doctor' => ['id', 'specialty' => ['id', 'name']]
                    ],
                    'token',
                ],
                'message',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ]);
    }

    /** @test */
    public function admin_can_login_successfully()
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id', 'name', 'email', 'phone', 'role'
                    ],
                    'token',
                ],
                'message',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ]);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $payload = [
            'email' => 'notfound@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }
}
