<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterPatientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_can_register_successfully()
    {
        $payload = [
            'name' => 'John Patient',
            'email' => 'patient@example.com',
            'phone' => '+1234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'dob' => '1990-01-01',
            'gender' => 'male',
        ];

        $response = $this->postJson('/api/register/patient', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'role',
                        'patient' => [
                            'id',
                            'dob',
                            'gender',
                        ],
                    ],
                    'token',
                ],
                'message',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Patient registered successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'patient@example.com',
            'role' => 'patient',
        ]);
    }

    /** @test */
    public function patient_registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/register/patient', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password',
                    'password_confirmation',
                    'dob',
                    'gender',
                ],
            ]);
    }
}
