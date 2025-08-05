<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAvailableSlotTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_available_slot_for_a_doctor()
    {
        // Arrange: create a doctor
        $doctor = \App\Models\Doctor::factory()->create();
        // Authenticate as the doctor user
        \Laravel\Sanctum\Sanctum::actingAs($doctor->user, ['*']);

        // Prepare slot data as required by the API
        $slotData = [
            [
                'date' => now()->addDays(1)->toDateString(),
                'start_time' => '10:00',
                'end_time' => '11:00',
            ]
        ];

        $payload = [
            'slots' => $slotData
        ];

        // Act: post to the endpoint
        $response = $this->postJson("/api/doctors/{$doctor->id}/available-slots", $payload);

        // Assert: check response status and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'doctor_id',
                        'date',
                        'start_time',
                        'end_time',
                        'is_booked',
                    ]
                ],
                'message',
            ]);

        // Assert: slot is created in the database
        $this->assertDatabaseHas('available_slots', [
            'doctor_id' => $doctor->id,
            'date' => $slotData[0]['date'] . ' 00:00:00',
            'start_time' => $slotData[0]['start_time'],
            'end_time' => $slotData[0]['end_time'],
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_available_slot()
    {
        // Arrange: create a doctor
        $doctor = \App\Models\Doctor::factory()->create();

        // Prepare slot data as required by the API
        $slotData = [
            [
                'date' => now()->addDays(1)->toDateString(),
                'start_time' => '10:00',
                'end_time' => '11:00',
            ]
        ];

        $payload = [
            'slots' => $slotData
        ];

        // Act: post to the endpoint without authentication
        $response = $this->postJson("/api/doctors/{$doctor->id}/available-slots", $payload);

        // Assert: should be unauthorized
        $response->assertStatus(401);
    }
}