# Login API Implementation

This document describes the login API implementation for the Nightingale healthcare system, following TDD and SOLID principles.

## Overview

The login API supports authentication for three user types:
- **Admin**: System administrators
- **Doctor**: Medical professionals with specialties
- **Patient**: Healthcare recipients

## Architecture

### SOLID Principles Implementation

1. **Single Responsibility Principle (SRP)**
   - `AuthService`: Handles authentication logic
   - `LoginController`: Manages HTTP requests/responses
   - `LoginRequest`: Validates input data
   - Custom exceptions: Handle specific error scenarios

2. **Open/Closed Principle (OCP)**
   - `AuthServiceInterface`: Allows extension without modification
   - Service binding in `AppServiceProvider`: Easy to swap implementations

3. **Liskov Substitution Principle (LSP)**
   - All exceptions extend base `Exception` class
   - Service implementations follow interface contracts

4. **Interface Segregation Principle (ISP)**
   - `AuthServiceInterface`: Focused on authentication only
   - Separate interfaces for different concerns

5. **Dependency Inversion Principle (DIP)**
   - Controllers depend on interfaces, not concrete implementations
   - Service binding in service provider

## API Endpoints

### POST /api/login
Authenticates a user and returns a token.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "phone": "+1234567890",
            "role": "admin"
        },
        "token": "1|abc123..."
    },
    "message": "Login successful"
}
```

**Role-specific Data:**

**Admin Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "role": "admin"
        },
        "token": "1|abc123..."
    }
}
```

**Doctor Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 2,
            "name": "Dr. Smith",
            "email": "doctor@example.com",
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
    }
}
```

**Patient Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 3,
            "name": "John Patient",
            "email": "patient@example.com",
            "role": "patient",
            "patient": {
                "id": 1,
                "dob": "1990-01-01",
                "gender": "male"
            }
        },
        "token": "3|ghi789..."
    }
}
```

### POST /api/logout
Logs out the authenticated user.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

## Error Responses

### Invalid Credentials (401)
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Account Deactivated (401)
```json
{
    "success": false,
    "message": "Account is deactivated"
}
```

### Validation Errors (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["Email is required."],
        "password": ["Password must be at least 6 characters."]
    }
}
```

### Rate Limited (429)
```json
{
    "message": "Too many login attempts. Please try again in 60 seconds."
}
```

## Security Features

### Rate Limiting
- Maximum 5 failed attempts per email/IP combination
- 60-second lockout period
- Automatic reset on successful login

### Token-based Authentication
- Uses Laravel Sanctum for API tokens
- Tokens are automatically created on successful login
- Tokens can be revoked via logout endpoint

### Input Validation
- Email format validation
- Password minimum length (6 characters)
- Required field validation

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    phone VARCHAR(20) NULL,
    email VARCHAR(255) UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255),
    role VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Doctors Table
```sql
CREATE TABLE doctors (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    user_id BIGINT FOREIGN KEY REFERENCES users(id),
    specialty_id BIGINT FOREIGN KEY REFERENCES specialties(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Patients Table
```sql
CREATE TABLE patients (
    id BIGINT PRIMARY KEY,
    user_id BIGINT FOREIGN KEY REFERENCES users(id),
    dob DATE NULL,
    gender ENUM('male', 'female', 'other') NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Specialties Table
```sql
CREATE TABLE specialties (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Testing

### Test-Driven Development (TDD)

The implementation follows TDD principles with comprehensive test coverage:

1. **Unit Tests**
   - Service layer testing
   - Model relationship testing
   - Factory testing

2. **Feature Tests**
   - API endpoint testing
   - Authentication flow testing
   - Error handling testing
   - Rate limiting testing

3. **Test Scenarios**
   - Valid login for all user types
   - Invalid credentials handling
   - Inactive account handling
   - Rate limiting
   - Input validation
   - Token generation and validation

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=LoginTest

# Run with coverage
php artisan test --coverage
```

## Implementation Details

### Service Layer
- `AuthService`: Implements authentication logic
- `AuthServiceInterface`: Defines service contract
- Dependency injection for testability

### Exception Handling
- `InvalidCredentialsException`: For wrong credentials
- `AccountDeactivatedException`: For inactive accounts
- Custom exception handler for consistent API responses

### Rate Limiting
- Uses Laravel's `RateLimiter` facade
- Throttle key based on email and IP
- Automatic cleanup on successful login

### Token Management
- Laravel Sanctum for API tokens
- Automatic token creation on login
- Token revocation on logout

## Usage Examples

### JavaScript/Fetch
```javascript
// Login
const response = await fetch('/api/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        email: 'user@example.com',
        password: 'password123'
    })
});

const data = await response.json();
const token = data.data.token;

// Use token for authenticated requests
const apiResponse = await fetch('/api/logout', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
    }
});
```

### PHP/cURL
```php
// Login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'user@example.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$data = json_decode($response, true);
$token = $data['data']['token'];

// Use token for authenticated requests
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/logout');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
```

## Future Enhancements

1. **Two-Factor Authentication (2FA)**
   - SMS/Email verification codes
   - TOTP support

2. **Password Reset**
   - Email-based password reset
   - Secure token generation

3. **Session Management**
   - Multiple device login tracking
   - Force logout from all devices

4. **Audit Logging**
   - Login attempt logging
   - Security event tracking

5. **Advanced Rate Limiting**
   - IP-based blocking
   - Geographic restrictions

## Conclusion

This login API implementation provides a secure, scalable, and maintainable authentication system that follows industry best practices and SOLID principles. The comprehensive test coverage ensures reliability and makes future modifications safer. 