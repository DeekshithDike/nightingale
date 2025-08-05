# Available Slots and Appointment Bookings API

This document describes the implementation of APIs for managing available slots and appointment bookings in the Nightingale healthcare system, following SOLID principles and TDD methodology.

## Overview

The system provides APIs for:
- **Available Slots**: Managing doctor appointment availability
- **Appointment Bookings**: Managing patient appointments and bookings

## Architecture

### SOLID Principles Implementation

1. **Single Responsibility Principle (SRP)**
   - `AvailableSlotService`: Handles slot retrieval logic
   - `AppointmentBookingService`: Handles booking retrieval logic
   - `AvailableSlotController` & `AppointmentBookingController`: Manage HTTP requests/responses
   - Separate seeders for data generation

2. **Open/Closed Principle (OCP)**
   - `AvailableSlotServiceInterface` & `AppointmentBookingServiceInterface`: Allow extension without modification
   - Service binding in `AppServiceProvider`: Easy to swap implementations

3. **Liskov Substitution Principle (LSP)**
   - Service implementations follow interface contracts
   - Consistent return types and error handling

4. **Interface Segregation Principle (ISP)**
   - Focused interfaces for different concerns
   - Separate interfaces for slots and bookings

5. **Dependency Inversion Principle (DIP)**
   - Controllers depend on interfaces, not concrete implementations
   - Service binding in service provider

## Available Slots API

### GET /api/available-slots
Get all available slots with optional filtering.

**Query Parameters:**
- `specialty_id` (optional): Filter by specialty ID

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "doctor_id": 1,
            "date": "2024-01-15",
            "start_time": "09:00",
            "end_time": "10:00",
            "is_booked": false,
            "doctor": {
                "id": 1,
                "name": "Dr. Smith",
                "specialty": {
                    "id": 1,
                    "name": "Cardiology"
                }
            }
        }
    ],
    "message": "Available slots retrieved successfully"
}
```

### GET /api/doctors/{doctor}/available-slots
Get available slots for a specific doctor.

**Path Parameters:**
- `doctor`: Doctor ID

**Query Parameters:**
- `date` (optional): Filter by specific date (YYYY-MM-DD format)

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "doctor_id": 1,
            "date": "2024-01-15",
            "start_time": "09:00",
            "end_time": "10:00",
            "is_booked": false,
            "doctor": {
                "id": 1,
                "name": "Dr. Smith",
                "specialty": {
                    "id": 1,
                    "name": "Cardiology"
                }
            }
        }
    ],
    "message": "Available slots retrieved successfully"
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Doctor not found"
}
```

## Appointment Bookings API

### GET /api/appointment-bookings
Get all appointment bookings with optional date range filtering.

**Query Parameters:**
- `start_date` (optional): Start date for filtering (YYYY-MM-DD format)
- `end_date` (optional): End date for filtering (YYYY-MM-DD format)

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "available_slot_id": 1,
            "patient_id": 1,
            "status": "confirmed",
            "notes": "Regular checkup",
            "available_slot": {
                "id": 1,
                "date": "2024-01-15",
                "start_time": "09:00",
                "end_time": "10:00",
                "doctor": {
                    "id": 1,
                    "name": "Dr. Smith",
                    "specialty": {
                        "id": 1,
                        "name": "Cardiology"
                    }
                }
            },
            "patient": {
                "id": 1,
                "user": {
                    "id": 1,
                    "name": "John Patient",
                    "email": "patient@example.com"
                }
            }
        }
    ],
    "message": "Appointment bookings retrieved successfully"
}
```

### GET /api/patients/{patient}/appointments
Get appointment bookings for a specific patient.

**Path Parameters:**
- `patient`: Patient ID

**Query Parameters:**
- `status` (optional): Filter by status (pending, confirmed, cancelled, completed)

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "available_slot_id": 1,
            "patient_id": 1,
            "status": "confirmed",
            "notes": "Regular checkup",
            "available_slot": {
                "id": 1,
                "date": "2024-01-15",
                "start_time": "09:00",
                "end_time": "10:00",
                "doctor": {
                    "id": 1,
                    "name": "Dr. Smith",
                    "specialty": {
                        "id": 1,
                        "name": "Cardiology"
                    }
                }
            }
        }
    ],
    "message": "Appointment bookings retrieved successfully"
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Patient not found"
}
```

### GET /api/doctors/{doctor}/appointments
Get appointment bookings for a specific doctor.

**Path Parameters:**
- `doctor`: Doctor ID

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "available_slot_id": 1,
            "patient_id": 1,
            "status": "confirmed",
            "notes": "Regular checkup",
            "available_slot": {
                "id": 1,
                "date": "2024-01-15",
                "start_time": "09:00",
                "end_time": "10:00"
            },
            "patient": {
                "id": 1,
                "user": {
                    "id": 1,
                    "name": "John Patient",
                    "email": "patient@example.com"
                }
            }
        }
    ],
    "message": "Appointment bookings retrieved successfully"
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Doctor not found"
}
```

## Database Schema

### Available Slots Table
```sql
CREATE TABLE available_slots (
    id BIGINT PRIMARY KEY,
    doctor_id BIGINT FOREIGN KEY REFERENCES doctors(id),
    date DATE,
    start_time TIME,
    end_time TIME,
    is_booked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Appointment Bookings Table
```sql
CREATE TABLE appointment_bookings (
    id BIGINT PRIMARY KEY,
    available_slot_id BIGINT FOREIGN KEY REFERENCES available_slots(id),
    patient_id BIGINT FOREIGN KEY REFERENCES patients(id),
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(available_slot_id)
);
```

## Data Seeding

### AvailableSlotSeeder
- Creates 8 medical specialties
- Creates 10 doctors with random specialties
- Generates 4-6 slots per weekday for each doctor
- 30% of slots are marked as booked
- Skips weekends for realistic scheduling

### AppointmentBookingSeeder
- Creates 20 patients with realistic demographics
- Creates bookings for all booked slots
- Assigns realistic statuses based on appointment dates:
  - Past appointments: completed, cancelled, or confirmed
  - Near future (≤7 days): confirmed or pending
  - Far future: pending or confirmed
- 70% of bookings have notes

## Testing

### Test-Driven Development (TDD)

The implementation follows TDD principles with comprehensive test coverage:

1. **Available Slots Tests**
   - Fetch slots for specific doctor
   - Filter by date
   - Filter by specialty
   - Handle empty results
   - Exclude booked slots
   - Handle non-existent doctor

2. **Appointment Bookings Tests**
   - Fetch bookings for specific patient
   - Filter by status
   - Fetch bookings for specific doctor
   - Handle empty results
   - Filter by date range
   - Handle non-existent patient/doctor

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test files
php artisan test --filter=AvailableSlotsTest
php artisan test --filter=AppointmentBookingsTest

# Run with coverage
php artisan test --coverage
```

## Usage Examples

### JavaScript/Fetch
```javascript
// Get all available slots
const response = await fetch('/api/available-slots');
const data = await response.json();

// Get slots for specific doctor
const doctorSlots = await fetch('/api/doctors/1/available-slots');
const slotsData = await doctorSlots.json();

// Get patient appointments
const appointments = await fetch('/api/patients/1/appointments?status=confirmed');
const appointmentsData = await appointments.json();

// Get doctor appointments
const doctorAppointments = await fetch('/api/doctors/1/appointments');
const doctorAppointmentsData = await doctorAppointments.json();
```

### PHP/cURL
```php
// Get all available slots
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/available-slots');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);

// Get slots for specific doctor
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/doctors/1/available-slots');
$response = curl_exec($ch);
$slotsData = json_decode($response, true);
```

## Seeding Data

To populate the database with test data:

```bash
# Run all seeders
php artisan db:seed

# Run specific seeders
php artisan db:seed --class=AvailableSlotSeeder
php artisan db:seed --class=AppointmentBookingSeeder
```

## Future Enhancements

1. **Booking Management**
   - Create new appointments
   - Update appointment status
   - Cancel appointments

2. **Advanced Filtering**
   - Filter by time range
   - Filter by doctor availability
   - Search functionality

3. **Real-time Updates**
   - WebSocket integration
   - Live slot availability
   - Instant booking notifications

4. **Calendar Integration**
   - iCal export
   - Google Calendar sync
   - Outlook integration

5. **Reporting**
   - Appointment analytics
   - Doctor workload reports
   - Patient booking patterns

## Conclusion

This implementation provides a robust, scalable, and maintainable system for managing available slots and appointment bookings. The comprehensive test coverage ensures reliability, while the SOLID principles implementation makes the codebase extensible and maintainable.

The APIs support various filtering options and provide detailed information about doctors, patients, and appointments, making it suitable for a healthcare management system. 