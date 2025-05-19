# The End.page - Your Creative Departure Platform

The End.page is a Symfony-based web application that allows users to create personalized "ending" pages to mark the conclusion of various life chapters - whether it's leaving a job, ending a relationship, or any other significant transition.

## Project Overview

The End.page transforms the experience of departures by providing a creative platform where users can craft personalized goodbye messages with multimedia elements and emotional context. Instead of awkward goodbyes or silent departures, users can express themselves in a way that's memorable, shareable, and even cathartic.

## Features

### Core Functionality
- **Personalized Ending Pages**: Create custom pages to mark the end of a chapter in your life
- **Emotional Categorization**: Choose from different emotional tones (happy, sad, angry, shocked, etc.)
- **Multimedia Support**: Add images, audio, and video to your ending pages
- **Social Sharing**: Share your ending page with specific people or publicly

### Content Creation
- **Rich Text Editor**: Format your goodbye message with styling options
- **Media Gallery**: Upload and manage multiple media files
- **Emotion Selection**: Choose the emotional tone that best fits your departure
- **Customization Options**: Personalize the look and feel of your ending page

### Social Features
- **Endings Feed**: Browse endings created by other users
- **Filtering**: Filter endings by emotion type
- **Interaction**: Like, comment, and engage with others' endings
- **Hall of Fame**: Featured section for the most popular or notable endings

### User Management
- **User Registration**: Create an account with email, first name, last name
- **Email Verification**: Verify your email address
- **Password Reset**: Reset your password if forgotten
- **User Dashboard**: Manage your endings and account settings
- **Two-Factor Authentication (2FA)**: Enhanced security for your account

### Moderation System
- **Content Review**: Endings are reviewed before being published
- **Moderation Scoring**: Automatic content scoring for faster moderation
- **Feedback System**: Moderators can provide feedback on rejected content

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

## User Flow

1. **Registration**: Create an account or log in
2. **Create Ending**: Craft your personalized ending page
   - Add a title and description
   - Select an emotional tone
   - Upload media (images, audio, video)
   - Customize your page
3. **Submit for Review**: Your ending is reviewed by moderators
4. **Share**: Once approved, share your ending with others
5. **Engage**: Browse other endings and interact with them

## Technical Implementation

- **Symfony Framework**: Built on Symfony for robust backend functionality
- **Doctrine ORM**: Database interactions and entity management
- **Twig Templates**: Dynamic frontend rendering
- **Bootstrap**: Responsive design framework
- **JavaScript/jQuery**: Enhanced user interactions
- **GSAP/ScrollCue**: Smooth animations and transitions

## Security Features

- **CSRF Protection**: Protection against cross-site request forgery
- **Rate Limiting**: Limits on login attempts
- **Secure Password Hashing**: Modern password security
- **Session Management**: Secure user sessions
- **Content Moderation**: Review system for user-generated content

## Notes

- For demo purposes, any 6-digit code will work for 2FA
- Rate limiting is set to 5 login attempts per minute
- Remember me cookie lasts for 1 week
