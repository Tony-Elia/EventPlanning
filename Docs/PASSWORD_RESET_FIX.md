# Password Reset Fix for API

## What Was Fixed

The password reset was failing because Laravel's default password reset notification tries to generate a web route URL (`password.reset`), but you're building an API-only application.

## Solution

Created a custom notification that:
1. Includes the reset token in the email
2. Links to your **frontend** reset password page (not backend)
3. Works with API-based authentication

## Files Created/Modified

### Created:
- `app/Notifications/ResetPasswordNotification.php` - Custom notification for API

### Modified:
- `app/Models/User.php` - Added `sendPasswordResetNotification()` method
- `.env.example` - Added `FRONTEND_URL` configuration

## Configuration Required

Add this to your `.env` file:

```bash
FRONTEND_URL=http://localhost:3000
```

Change `http://localhost:3000` to your actual frontend URL (React, Vue, Angular, etc.)

## How It Works Now

1. **User requests password reset** → `POST /api/auth/forgot-password`
2. **Backend sends email** with:
   - Reset token
   - Link to frontend: `{FRONTEND_URL}/reset-password?token={token}&email={email}`
3. **User clicks link** → Opens frontend reset password page
4. **Frontend submits** → `POST /api/auth/reset-password` with token, email, and new password
5. **Backend resets password** → Success!

## Email Content

The email will include:
- The reset token (visible in email)
- A button linking to: `http://localhost:3000/reset-password?token=abc123&email=user@example.com`
- Token expiration time (60 minutes by default)

## Frontend Implementation Example

Your frontend reset password page should:

```javascript
// Get token and email from URL query parameters
const urlParams = new URLSearchParams(window.location.search);
const token = urlParams.get('token');
const email = urlParams.get('email');

// Submit reset password request
const resetPassword = async (password, passwordConfirmation) => {
  const response = await fetch('http://eventplanning.test/api/auth/reset-password', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      token: token,
      email: email,
      password: password,
      password_confirmation: passwordConfirmation
    })
  });
  
  return await response.json();
};
```

## Testing Without Email

If you don't have email configured yet, you can test by:

1. **Check logs** - With `MAIL_MAILER=log`, tokens are logged to `storage/logs/laravel.log`
2. **Use database** - Tokens are stored in `password_reset_tokens` table
3. **Manual testing** - Get token from database and use it in Postman

## Production Setup

For production, configure proper email service in `.env`:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@eventplanning.com
MAIL_FROM_NAME="${APP_NAME}"
FRONTEND_URL=https://your-frontend-domain.com
```

## ✅ Fixed!

The password reset endpoint should now work without the "Route [password.reset] not defined" error.
