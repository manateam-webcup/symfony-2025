# Enhanced Authentication System

This Symfony application features a modern, secure authentication system with multiple advanced features.

## Features

### User Management
- User registration with email, first name, last name
- Email verification
- Password reset functionality
- Remember me functionality
- Two-Factor Authentication (2FA)

### Security Features
- CSRF protection
- Rate limiting for login attempts
- Secure password hashing
- Session management

### User Interface
- Bootstrap-based responsive design
- Flash messages for user feedback
- Intuitive navigation

## Setup Instructions

1. Clone the repository
2. Install dependencies:
   ```
   composer install
   ```
3. Configure your database in `.env` file
4. Run migrations:
   ```
   php bin/console doctrine:migrations:migrate
   ```
5. Start the Symfony server:
   ```
   symfony server:start
   ```

## Authentication Flow

1. **Registration**: Users register with email, name, and password
2. **Email Verification**: Users verify their email address
3. **Login**: Users log in with email and password
4. **Two-Factor Authentication**: Users enter a verification code
5. **Password Reset**: Users can reset their password if forgotten

## Technical Implementation

- **User Entity**: Extended with firstName and lastName fields
- **Forms**: Enhanced with validation and better UX
- **Security**: Configured with multiple authenticators
- **Templates**: Modernized with Bootstrap

## Notes

- For the demo, any 6-digit code will work for 2FA
- Rate limiting is set to 5 login attempts per minute
- Remember me cookie lasts for 1 week