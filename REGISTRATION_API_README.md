# Registration API Implementation

This document describes the registration API implementation for the Nightingale healthcare system, following TDD and SOLID principles.

## Overview

The registration API supports user registration for two user types:
- **Patient**: Healthcare recipients with personal information
- **Doctor**: Medical professionals with specialty information

## Architecture

### SOLID Principles Implementation

1. **Single Responsibility Principle (SRP)**
   - `RegistrationService`: Handles registration logic
   - `RegistrationController`: Manages HTTP requests/responses
   - `PatientRegistrationRequest` & `DoctorRegistrationRequest`: Validate input data
   - Custom exceptions: Handle specific error scenarios

2. **Open/Closed Principle (OCP)**
   - `RegistrationServiceInterface`: Allows extension without modification
   - Service binding in `AppServiceProvider`: Easy to swap implementations

3. **Liskov Substitution Principle (LSP)**
   - All exceptions extend base `Exception` class
   - Service implementations follow interface contracts

4. **Interface Segregation Principle (ISP)**
   - `RegistrationServiceInterface`: Focused on registration only
   - Separate interfaces for different concerns

5. **Dependency Inversion Principle (DIP)**
   - Controllers depend on interfaces, not concrete implementations
   - Service binding in service provider

## API Endpoints

### POST /api/register/patient
Registers a new patient account.

**Request Body:**
```json
{
    "name": "John Patient",
    "email": "patient@example.com",
    "phone": "+1234567890",
    "password": "password123",
    "password_confirmation": "password123",
    "dob": "1990-01-01",
    "gender": "male"
}
```

**Success Response (201):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Patient",
            "email": "patient@example.com",
            "phone": "+1234567890",
            "role": "patient",
            "patient": {
                "id": 1,
                "dob": "1990-01-01",
                "gender": "male"
            }
        },
        "token": "1|abc123..."
    },
    "message": "Patient registered successfully"
}
```

### POST /api/register/doctor
Registers a new doctor account.

**Request Body:**
```json
{
    "name": "Dr. Smith",
    "email": "doctor@example.com",
    "phone": "+1234567890",
    "password": "password123",
    "password_confirmation": "password123",
    "specialty_id": 1
}
```

**Success Response (201):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 2,
            "name": "Dr. Smith",
            "email": "doctor@example.com",
            "phone": "+1234567890",
            "role": "doctor",
            "doctor": {
                "id": 1,
                "specialty": {
                    "id": 1,
                    "name": "Cardiology"
                }
            }
        },
        "token": "2|def456..."
    },
    "message": "Doctor registered successfully"
}
```

## Validation Rules

### Patient Registration
- `name`: Required, string, max 255 characters
- `email`: Required, valid email format, unique in users table
- `phone`: Optional, string, max 20 characters
- `password`: Required, string, minimum 6 characters
- `password_confirmation`: Required, must match password
- `dob`: Required, valid date, must be in the past
- `gender`: Required, must be one of: male, female, other

### Doctor Registration
- `name`: Required, string, max 255 characters
- `email`: Required, valid email format, unique in users table
- `phone`: Optional, string, max 20 characters
- `password`: Required, string, minimum 6 characters
- `password_confirmation`: Required, must match password
- `specialty_id`: Required, must exist in specialties table

## Error Responses

### Validation Errors (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["This email is already registered."],
        "password": ["Password confirmation does not match."],
        "dob": ["Date of birth must be in the past."],
        "gender": ["Gender must be male, female, or other."],
        "specialty_id": ["Selected specialty does not exist."]
    }
}
```

### Registration Failed (422)
```json
{
    "success": false,
    "message": "Registration failed"
}
```

## Security Features

### Password Security
- Passwords are automatically hashed using Laravel's Hash facade
- Minimum password length of 6 characters
- Password confirmation required

### Data Validation
- Email uniqueness validation
- Date format validation for DOB
- Enum validation for gender
- Foreign key validation for specialty_id

### Database Transactions
- All registration operations use database transactions
- Ensures data consistency
- Automatic rollback on failure

## Database Operations

### Patient Registration Process
1. Create user record with role 'patient'
2. Create patient record with user_id foreign key
3. Generate authentication token
4. Return user data with patient information

### Doctor Registration Process
1. Create user record with role 'doctor'
2. Validate specialty exists
3. Create doctor record with user_id and specialty_id foreign keys
4. Generate authentication token
5. Return user data with doctor and specialty information

## Testing

### Test-Driven Development (TDD)

The implementation follows TDD principles with comprehensive test coverage:

1. **Unit Tests**
   - Service layer testing
   - Model relationship testing
   - Factory testing

2. **Feature Tests**
   - API endpoint testing
   - Registration flow testing
   - Error handling testing
   - Validation testing

3. **Test Scenarios**
   - Successful patient registration
   - Successful doctor registration
   - Duplicate email handling
   - Validation error handling
   - Password confirmation
   - Date validation
   - Gender enum validation
   - Specialty validation
   - Token generation
   - Database consistency

### Running Tests
```bash
# Run all tests
php artisan test

# Run registration tests only
php artisan test --filter=RegistrationTest

# Run with coverage
php artisan test --coverage
```

## Implementation Details

### Service Layer
- `RegistrationService`: Implements registration logic
- `RegistrationServiceInterface`: Defines service contract
- Dependency injection for testability
- Database transaction handling

### Exception Handling
- `RegistrationFailedException`: For registration failures
- Custom exception handler for consistent API responses
- Proper HTTP status codes

### Form Request Validation
- `PatientRegistrationRequest`: Patient-specific validation
- `DoctorRegistrationRequest`: Doctor-specific validation
- Custom validation messages
- Unique email validation

### Database Relationships
- User has one Patient (for patient users)
- User has one Doctor (for doctor users)
- Doctor belongs to Specialty
- Proper foreign key constraints

## Usage Examples

### JavaScript/Fetch
```javascript
// Patient Registration
const patientResponse = await fetch('/api/register/patient', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        name: 'John Patient',
        email: 'patient@example.com',
        phone: '+1234567890',
        password: 'password123',
        password_confirmation: 'password123',
        dob: '1990-01-01',
        gender: 'male'
    })
});

const patientData = await patientResponse.json();
const token = patientData.data.token;

// Doctor Registration
const doctorResponse = await fetch('/api/register/doctor', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        name: 'Dr. Smith',
        email: 'doctor@example.com',
        phone: '+1234567890',
        password: 'password123',
        password_confirmation: 'password123',
        specialty_id: 1
    })
});

const doctorData = await doctorResponse.json();
```

### PHP/cURL
```php
// Patient Registration
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/register/patient');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'name' => 'John Patient',
    'email' => 'patient@example.com',
    'phone' => '+1234567890',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'dob' => '1990-01-01',
    'gender' => 'male'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$data = json_decode($response, true);
$token = $data['data']['token'];
```

## Future Enhancements

1. **Email Verification**
   - Send verification email on registration
   - Require email verification before login

2. **Phone Verification**
   - SMS verification for phone numbers
   - OTP-based verification

3. **Profile Completion**
   - Multi-step registration process
   - Additional profile fields

4. **Admin Approval**
   - Admin approval for doctor registrations
   - Document verification workflow

5. **Registration Analytics**
   - Track registration sources
   - Conversion rate analysis

6. **Social Registration**
   - Google OAuth integration
   - Facebook login integration

## Conclusion

This registration API implementation provides a secure, scalable, and maintainable user registration system that follows industry best practices and SOLID principles. The comprehensive test coverage ensures reliability and makes future modifications safer.

The implementation supports both patient and doctor registrations with appropriate validation and data integrity, while maintaining clean separation of concerns and following Laravel best practices. 