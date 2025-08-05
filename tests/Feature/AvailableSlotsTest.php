<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AvailableSlot;

class AvailableSlotsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_available_slots_for_a_specific_doctor()
    {
        // Arrange: create a doctor and available slots for that doctor
        $doctor = \App\Models\Doctor::factory()->create();
        \App\Models\AvailableSlot::factory()->count(2)->create([
            'doctor_id' => $doctor->id,
        ]);
        // Also create slots for another doctor to ensure filtering
        \App\Models\AvailableSlot::factory()->count(2)->create();

        // Act: call the endpoint for the specific doctor
        $response = $this->getJson("/api/doctors/{$doctor->id}/available-slots");

        // Assert: check response structure and status
        $response->assertStatus(200)
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
                        'doctor' => [
                            'id',
                            'name',
                            'specialty' => [
                                'id',
                                'name',
                            ]
                        ]
                    ]
                ],
                'message',
            ]);

        // Assert: only slots for the given doctor are returned
        $responseData = $response->json('data');
        $this->assertNotEmpty($responseData);
        foreach ($responseData as $slot) {
            $this->assertEquals($doctor->id, $slot['doctor_id']);
        }
    }

    /** @test */
    public function it_returns_all_available_slots()
    {
        // Arrange: create some available slots
        AvailableSlot::factory()->count(3)->create();

        // Act: call the endpoint
        $response = $this->getJson('/api/available-slots');

        // Assert: check response structure and status
        $response->assertStatus(200)
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
                        'doctor' => [
                            'id',
                            'name',
                            'specialty' => [
                                'id',
                                'name',
                            ]
                        ]
                    ]
                ],
                'message',
            ]);
    }
}
