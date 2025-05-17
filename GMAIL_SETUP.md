# Gmail SMTP Configuration for Symfony Application

This document provides instructions on how to configure Gmail SMTP for sending emails in the Symfony application, specifically for password reset and email confirmation functionalities.

## Prerequisites

1. A Gmail account
2. An App Password for your Gmail account (since Gmail no longer allows less secure apps)

## Creating a Gmail App Password

1. Go to your Google Account settings: https://myaccount.google.com/
2. Navigate to "Security"
3. Under "Signing in to Google," select "2-Step Verification" (you must have this enabled)
4. At the bottom of the page, select "App passwords"
5. Generate a new app password (select "Other" as the app and give it a name like "Symfony App")
6. Google will generate a 16-character password - save this password as you'll need it for configuration

## Configuring the Application

1. Open the `.env` file in your project root
2. Locate the `MAILER_DSN` setting under the `symfony/mailer` section
3. Replace the placeholder with your actual Gmail credentials:

```
MAILER_DSN=gmail://YOUR_GMAIL_USERNAME:YOUR_GMAIL_APP_PASSWORD@default
```

For example:
```
MAILER_DSN=gmail://johndoe:abcdefghijklmnop@default
```

Where:
- `johndoe` is your Gmail username (without the @gmail.com part)
- `abcdefghijklmnop` is the App Password you generated

4. Open the following files and update the email addresses to match your Gmail account:
   - `src/Controller/RegistrationController.php`
   - `src/Controller/ResetPasswordController.php`

   Replace `YOUR_GMAIL_USERNAME@gmail.com` with your actual Gmail address.

## Important Notes

1. **Never commit your actual Gmail credentials to version control**. Consider using environment variables or a `.env.local` file (which should be in your .gitignore).

2. Gmail has sending limits (e.g., 500 emails per day for regular Gmail accounts). For production applications with high email volume, consider using a dedicated email service like SendGrid, Mailgun, or Amazon SES.

3. If you're still experiencing issues, check that:
   - Your Gmail account has 2-Step Verification enabled
   - The App Password is correctly generated and entered
   - Your Gmail account doesn't have any security blocks in place

## Testing

After configuration, test both functionalities:
1. Register a new user to test the email confirmation
2. Use the "Forgot Password" feature to test the password reset functionality