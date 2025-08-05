<?php

namespace Database\Seeders;

use App\Models\AvailableSlot;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;

class AvailableSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specialties
        $specialties = [
            'Cardiology' => 'Heart and cardiovascular system',
            'Dermatology' => 'Skin, hair, and nail conditions',
            'Neurology' => 'Nervous system disorders',
            'Orthopedics' => 'Bone and joint conditions',
            'Pediatrics' => 'Child healthcare',
            'Psychiatry' => 'Mental health conditions',
            'Oncology' => 'Cancer treatment',
            'Gastroenterology' => 'Digestive system disorders',
        ];

        foreach ($specialties as $name => $description) {
            Specialty::firstOrCreate(['name' => $name], [
                'name' => $name,
                'description' => $description,
            ]);
        }

        // Create doctors with specialties
        $doctors = [];
        $specialtyIds = Specialty::pluck('id')->toArray();

        for ($i = 1; $i <= 10; $i++) {
            $user = User::factory()->doctor()->create([
                'name' => "Dr. " . fake()->lastName(),
                'email' => "doctor{$i}@example.com",
            ]);

            $doctor = Doctor::factory()->create([
                'name' => $user->name,
                'user_id' => $user->id,
                'specialty_id' => fake()->randomElement($specialtyIds),
            ]);

            $doctors[] = $doctor;
        }

        // Create available slots for each doctor
        foreach ($doctors as $doctor) {
            $this->createSlotsForDoctor($doctor);
        }
    }

    /**
     * Create available slots for a specific doctor.
     */
    private function createSlotsForDoctor(Doctor $doctor): void
    {
        $startDate = now()->addDays(1);
        $endDate = now()->addDays(30);

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Skip weekends
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            // Create 4-6 slots per day
            $numSlots = fake()->numberBetween(4, 6);
            
            for ($i = 0; $i < $numSlots; $i++) {
                $startTime = fake()->dateTimeBetween('09:00', '16:00')->format('H:i:00');
                $endTime = fake()->dateTimeBetween($startTime, '18:00')->format('H:i:00');

                // Ensure end time is after start time
                if (strtotime($endTime) <= strtotime($startTime)) {
                    $endTime = date('H:i:00', strtotime($startTime) + 3600); // Add 1 hour
                }

                AvailableSlot::factory()->create([
                    'doctor_id' => $doctor->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_booked' => fake()->boolean(30), // 30% chance of being booked
                ]);
            }

            $currentDate->addDay();
        }
    }
} 