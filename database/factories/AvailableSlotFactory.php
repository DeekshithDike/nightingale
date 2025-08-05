<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailableSlot>
 */
class AvailableSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d');
        $startTime = fake()->dateTimeBetween('09:00', '17:00')->format('H:i:00');
        $endTime = fake()->dateTimeBetween($startTime, '18:00')->format('H:i:00');

        return [
            'doctor_id' => Doctor::factory(),
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_booked' => fake()->boolean(20), // 20% chance of being booked
        ];
    }

    /**
     * Indicate that the slot is available (not booked).
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_booked' => false,
        ]);
    }

    /**
     * Indicate that the slot is booked.
     */
    public function booked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_booked' => true,
        ]);
    }

    /**
     * Indicate that the slot is for a specific date.
     */
    public function forDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => $date,
        ]);
    }

    /**
     * Indicate that the slot is for a specific doctor.
     */
    public function forDoctor(Doctor $doctor): static
    {
        return $this->state(fn (array $attributes) => [
            'doctor_id' => $doctor->id,
        ]);
    }
} 