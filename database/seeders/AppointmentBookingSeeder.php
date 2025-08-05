<?php

namespace Database\Seeders;

use App\Models\AppointmentBooking;
use App\Models\AvailableSlot;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create patients
        $patients = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = User::factory()->patient()->create([
                'name' => fake()->name(),
                'email' => "patient{$i}@example.com",
            ]);

            $patient = Patient::factory()->create([
                'user_id' => $user->id,
                'dob' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
                'gender' => fake()->randomElement(['male', 'female', 'other']),
            ]);

            $patients[] = $patient;
        }

        // Get booked slots
        $bookedSlots = AvailableSlot::where('is_booked', true)->get();

        // Create appointment bookings for booked slots
        foreach ($bookedSlots as $slot) {
            $patient = fake()->randomElement($patients);
            $status = fake()->randomElement(['pending', 'confirmed', 'cancelled', 'completed']);
            
            // Adjust probability based on date
            $slotDate = $slot->date;
            $now = now();
            
            if ($slotDate < $now) {
                // Past appointments are more likely to be completed or cancelled
                $status = fake()->randomElement(['completed', 'cancelled', 'confirmed']);
            } elseif ($slotDate->diffInDays($now) <= 7) {
                // Near future appointments are more likely to be confirmed
                $status = fake()->randomElement(['confirmed', 'pending']);
            } else {
                // Far future appointments are more likely to be pending
                $status = fake()->randomElement(['pending', 'confirmed']);
            }

            AppointmentBooking::factory()->create([
                'available_slot_id' => $slot->id,
                'patient_id' => $patient->id,
                'status' => $status,
                'notes' => fake()->optional(0.7)->sentence(), // 70% chance of having notes
            ]);
        }
    }
} 