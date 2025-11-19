# Event Planning API - Authentication Documentation

Complete API reference for authentication endpoints. All endpoints return JSON responses.

## Base URL

```
http://localhost:8000/api
```

## Response Format

All API responses follow this structure:

```json
{
  "success": true|false,
  "message": "Description of the result",
  "data": { ... }
}
```

---

## Authentication Endpoints

### 1. Register New User

Create a new user account and receive an authentication token.

**Endpoint:** `POST /auth/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "password_confirmation": "SecurePassword123!"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-11-19T19:45:00.000000Z"
    },
    "access_token": "1|abcdef123456...",
    "token_type": "Bearer"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 2. Login

Authenticate user and receive an access token.

**Endpoint:** `POST /auth/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "SecurePassword123!"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "access_token": "2|xyz789...",
    "token_type": "Bearer"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### 3. Get Authenticated User

Retrieve the currently authenticated user's information.

**Endpoint:** `GET /auth/user`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {your_token_here}
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2025-11-19T19:45:00.000000Z"
    }
  }
}
```

**Error Response (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

### 4. Logout

Revoke the current access token.

**Endpoint:** `POST /auth/logout`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {your_token_here}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

### 5. Refresh Token

Revoke the current token and issue a new one.

**Endpoint:** `POST /auth/refresh`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {your_token_here}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "access_token": "3|newtoken123...",
    "token_type": "Bearer"
  }
}
```

---

### 6. Forgot Password

Request a password reset link to be sent to the user's email.

**Endpoint:** `POST /auth/forgot-password`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Password reset link sent to your email"
}
```

**Error Response (422):**
```json
{
  "message": "We can't find a user with that email address.",
  "errors": {
    "email": ["We can't find a user with that email address."]
  }
}
```

> **Note:** Email functionality requires proper mail configuration in `.env` file.

---

### 7. Reset Password

Reset the user's password using the token received via email.

**Endpoint:** `POST /auth/reset-password`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "token": "reset_token_from_email",
  "email": "john@example.com",
  "password": "NewSecurePassword123!",
  "password_confirmation": "NewSecurePassword123!"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Password reset successfully"
}
```

**Error Response (422):**
```json
{
  "message": "This password reset token is invalid.",
  "errors": {
    "email": ["This password reset token is invalid."]
  }
}
```

---

## Authentication Flow

### For Frontend Implementation:

1. **Registration/Login:**
   - Call `/auth/register` or `/auth/login`
   - Store the `access_token` from the response (localStorage, sessionStorage, or secure cookie)
   - Store the `token_type` (always "Bearer")

2. **Making Authenticated Requests:**
   - Include the token in the Authorization header:
     ```
     Authorization: Bearer {access_token}
     ```

3. **Token Refresh:**
   - Call `/auth/refresh` periodically or when token expires
   - Update stored token with the new one

4. **Logout:**
   - Call `/auth/logout` to revoke the token
   - Clear stored token from client storage

---

## Error Handling

### Common HTTP Status Codes:

- `200` - Success
- `201` - Created (registration)
- `401` - Unauthenticated (missing or invalid token)
- `422` - Validation Error (invalid input)
- `500` - Server Error

### Validation Errors Format:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message 1",
      "Error message 2"
    ]
  }
}
```

---

## Testing with cURL

### Register:
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Get User (replace TOKEN with actual token):
```bash
curl -X GET http://localhost:8000/api/auth/user \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN"
```

### Logout:
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN"
```

---

## Testing with Postman

1. **Import Collection:**
   - Create a new collection called "Event Planning API"
   - Add all endpoints listed above

2. **Environment Variables:**
   - Create variable `base_url` = `http://localhost:8000/api`
   - Create variable `token` = (will be set after login)

3. **Auto-set Token:**
   - In Login request, add to "Tests" tab:
     ```javascript
     pm.environment.set("token", pm.response.json().data.access_token);
     ```

4. **Use Token:**
   - In protected endpoints, set Authorization header:
     ```
     Bearer {{token}}
     ```

---

## Security Notes

- Always use HTTPS in production
- Tokens are sensitive - never expose them in URLs or logs
- Implement rate limiting for login/register endpoints
- Consider implementing token expiration
- Store tokens securely on the client side
- Revoke all tokens on password change/reset

---

## Next Steps

After authentication is working, you can extend the API with:
- Email verification
- Two-factor authentication (2FA)
- Social login (OAuth)
- Role-based access control (RBAC)
- User profile management
