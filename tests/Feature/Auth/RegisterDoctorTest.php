<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterDoctorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function doctor_can_register_successfully()
    {
        $specialty = \App\Models\Specialty::factory()->create();
        $payload = [
            'name' => 'Dr. Smith',
            'email' => 'doctor@example.com',
            'phone' => '+1234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'specialty_id' => $specialty->id,
        ];

        $response = $this->postJson('/api/register/doctor', $payload);

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
                        'doctor' => [
                            'id',
                            'specialty' => [
                                'id',
                                'name',
                            ],
                        ],
                    ],
                    'token',
                ],
                'message',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Doctor registered successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'doctor@example.com',
            'role' => 'doctor',
        ]);
    }

    /** @test */
    public function doctor_registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/register/doctor', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password',
                    'password_confirmation',
                    'specialty_id',
                ],
            ]);
    }
}
