# Event Planning API Documentation

## Import Collection Into Postman 
- ``` Collection Name: Event Planning API - Compelete_collection.json ```
---
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
  "password_confirmation": "SecurePassword123!",
  "role": "customer|provider|admin" // Default: customer
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
      "created_at": "2025-11-19T19:45:00.000000Z",
      "role": "customer|provider|admin"
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
      "email": "john@example.com",
      "created_at": "2025-11-19T19:45:00.000000Z",
      "role": "customer|provider|admin"
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
      "created_at": "2025-11-19T19:45:00.000000Z",
      "role": "customer|provider|admin"
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


# Service Module Documentation

## Summary
### Public Endpoints (No Auth Required)
```
GET /api/service-categories
GET /api/service-categories/{id}
GET /api/services?category_id=&location=&min_price=&max_price=
GET /api/services/{id}
GET /api/venues?category_id=&location=&capacity=&min_price=&max_price=
GET /api/venues/{id}
GET /api/packages
GET /api/packages/{id}
GET /api/services/{service}/reviews
GET /api/venues/{venue}/reviews
```
### Protected Endpoints (Auth & Authrized Required)
```
POST /api/v1/service-categories (Admin)
PUT /api/v1/service-categories/{id} (Admin)
DELETE /api/v1/service-categories/{id} (Admin)
POST /api/v1/services (Provider)
PUT /api/v1/services/{id} (Provider)
DELETE /api/v1/services/{id} (Provider)
GET /api/v1/my-services (Provider)
POST /api/v1/venues (Provider)
PUT /api/v1/venues/{id} (Provider)
DELETE /api/v1/venues/{id} (Provider)
GET /api/v1/my-venues (Provider)
POST /api/v1/packages (Provider)
PUT /api/v1/packages/{id} (Provider)
DELETE /api/v1/packages/{id} (Provider)
POST /api/v1/packages/{id}/items (Provider)
DELETE /api/v1/packages/{id}/items/{item_id} (Provider)
POST /api/v1/reviews (Customer)
PUT /api/v1/reviews/{id} (Customer)
DELETE /api/v1/reviews/{id} (Customer/Admin)
```

## 1. Service Categories

### List All Categories
**Endpoint:** `GET /service-categories`
**Access:** Public
**Optional Parameters:**
- `services`: true|false, default false  // include services in response

### Get Category Details with Services
**Endpoint:** `GET /service-categories/{id}`
**Access:** Public

### Create Category (Admin Only)
**Endpoint:** `POST /service-categories`
**Headers:** `Authorization: Bearer {token}`
**Request Body:**
```json
{
  "name": "Photography",
  "description": "Professional photography services"
}
```
- `name`: required, string, unique
- `description`: optional, string

### Update Category (Admin Only)
**Endpoint:** `PUT /service-categories/{id}`
**Headers:** `Authorization: Bearer {token}`
**Request Body:** Same as Create

### Delete Category (Admin Only)
**Endpoint:** `DELETE /service-categories/{id}`
**Headers:** `Authorization: Bearer {token}`

---

## 2. Services

### List All Services with its category and provider
**Endpoint:** `GET /services`
**Access:** Public
**Query Parameters:**
- `category_id`: Filter by category
- `location`: Filter by location name
- `min_price`: Minimum base price
- `max_price`: Maximum base price

### Get Service Details with its category, provider, and reviews
**Endpoint:** `GET /services/{id}`
**Access:** Public

### Create Service (Provider Only)
**Endpoint:** `POST /services`
**Headers:** `Authorization: Bearer {token}`
**Request Body:**
```json
{
  "category_id": 1,
  "name": "Wedding Photography",
  "description": "Full day wedding coverage",
  "base_price": 1500.00,
  "price_unit": "fixed|hour|person",
  "location": "New York, NY",
  "is_active": true
}
```
- `category_id`: required, exists in service_categories
- `name`: required, string
- `description`: required, string
- `base_price`: required, numeric, min 0
- `price_unit`: required, one of: `hour`, `fixed`, `person`
- `location`: required, string
- `is_active`: boolean (default: true)

### Update Service (Provider Only)
**Endpoint:** `PUT /services/{id}`
**Headers:** `Authorization: Bearer {token}`
**Request Body:** Same as Create

### Delete Service (Provider Only)
**Endpoint:** `DELETE /services/{id}`
**Headers:** `Authorization: Bearer {token}`

### Get My Services (Provider Only)
**Endpoint:** `GET /my-services`
**Headers:** `Authorization: Bearer {token}`
**Description:** Returns services owned by the authenticated provider.

---

## 3. Venues

### List All Venues
**Endpoint:** `GET /venues`
**Access:** Public
**Query Parameters:**
- `location`: Filter by location
- `min_capacity`: Minimum capacity
- `max_capacity`: Maximum capacity
- `min_price`: Minimum base price
- `max_price`: Maximum base price

### Get Venue Details
**Endpoint:** `GET /venues/{id}`
**Access:** Public

### Create Venue (Provider Only)
**Endpoint:** `POST /venues`
**Headers:** `Authorization: Bearer {token}`
**Request Body:**
```json
{
  "name": "Grand Ballroom",
  "description": "Luxurious ballroom for large events",
  "capacity": 500,
  "base_price": 5000.00,
  "price_unit": "day",
  "location": "Chicago, IL",
  "address": "123 Main St, Chicago, IL 60601",
  "latitude": 41.8781,
  "longitude": -87.6298,
  "amenities": ["WiFi", "Parking", "Catering"],
  "is_active": true
}
```
- `name`: required, string
- `description`: required, string
- `capacity`: required, integer, min 1
- `base_price`: required, numeric, min 0
- `price_unit`: required, one of: `hour`, `day`, `fixed`
- `location`: required, string
- `address`: required, string
- `amenities`: optional, array of strings
- `latitude`: optional, numeric
- `longitude`: optional, numeric

### Update Venue (Provider Only)
**Endpoint:** `PUT /venues/{id}`
**Headers:** `Authorization: Bearer {token}`
**Request Body:** Same as Create

### Delete Venue (Provider Only)
**Endpoint:** `DELETE /venues/{id}`
**Headers:** `Authorization: Bearer {token}`

### Get My Venues (Provider Only)
**Endpoint:** `GET /my-venues`
**Headers:** `Authorization: Bearer {token}`
**Description:** Returns venues owned by the authenticated provider.

---

## 4. Packages

### List All Packages
**Endpoint:** `GET /packages`
**Access:** Public

### Get Package Details
**Endpoint:** `GET /packages/{id}`
**Access:** Public

### Create Package (Provider Only)
**Endpoint:** `POST /packages`
**Headers:** `Authorization: Bearer {token}`
**Request Body:**
```json
{
  "name": "Gold Wedding Package",
  "description": "Complete package including photography and venue",
  "price": 6000.00
}
```
- `name`: required, string
- `description`: required, string
- `price`: required, numeric, min 0

### Add Item to Package (Provider Only)
**Endpoint:** `POST /packages/{id}/items`
**Headers:** `Authorization: Bearer {token}`
**Request Body:**
```json
{
  "service_id": 1,
  "description": "8 hours of photography coverage",
  "quantity": 2
}
```
- `service_id`: required, exists in services
- `description`: required, string
- `quantity`: optional, integer, min 1, defualt 1

### Remove Item from Package (Provider Only)
**Endpoint:** `DELETE /packages/{packageId}/items/{itemId}`
**Headers:** `Authorization: Bearer {token}`

---

## 5. Reviews

### List Reviews
**Endpoint:** `GET /reviews`
**Access:** Public
**Query Parameters:**
- `type`: `service` or `venue`
- `id`: ID of the service or venue

### Create Review (Customer Only)
**Endpoint:** `POST /reviews`
**Headers:** `Authorization: Bearer {token}`
**Request Body:**
```json
{
  "reviewable_type": "service",
  "reviewable_id": 1,
  "rating": 5,
  "comment": "Excellent service!"
}
```
- `reviewable_type`: required, `service` or `venue`
- `reviewable_id`: required, integer
- `rating`: required, integer, 1-5
- `comment`: optional, string

### Update Review (Customer Only)
**Endpoint:** `PUT /reviews/{id}`
**Headers:** `Authorization: Bearer {token}`
**Request Body:** Same as Create

### Delete Review (Customer Only)
**Endpoint:** `DELETE /reviews/{id}`
**Headers:** `Authorization: Bearer {token}`
