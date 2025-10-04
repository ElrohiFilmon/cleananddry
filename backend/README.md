# CleanAndDry Car Wash API Documentation

## Authentication Endpoints

| Method | Endpoint | Description | Request Body | Headers | Success Response |
|--------|----------|-------------|--------------|---------|------------------|
| POST | `/api/register` | Register new user | `{ "name": "string", "email": "string", "password": "string", "password_confirmation": "string", "role": "customer\|washer", "phone": "string", "address": "string" }` | - | `201: { "user": { ... }, "token": "string" }` |
| POST | `/api/login` | User login | `{ "email": "string", "password": "string" }` | - | `200: { "user": { ... }, "token": "string" }` |
| POST | `/api/logout` | User logout | - | `Authorization: Bearer {token}` | `200: { "message": "Logged out" }` |
| GET | `/api/user` | Get user profile | - | `Authorization: Bearer {token}` | `200: { "user": { ... } }` |

## Booking Endpoints

| Method | Endpoint | Description | Request Body | Headers | Success Response |
|--------|----------|-------------|--------------|---------|------------------|
| GET | `/api/bookings` | Get user bookings | - | `Authorization: Bearer {token}` | `200: { "bookings": [{ ... }] }` |
| POST | `/api/bookings` | Create new booking | `{ "service_type": "car_wash\|detailing", "vehicle_type": "string", "vehicle_model": "string", "vehicle_plate": "string", "location": "string", "latitude": "float", "longitude": "float", "scheduled_time": "datetime", "special_instructions": "string" }` | `Authorization: Bearer {token}` | `201: { "booking": { ... }, "message": "Booking created" }` |
| GET | `/api/bookings/{id}` | Get booking details | - | `Authorization: Bearer {token}` | `200: { "booking": { ... } }` |
| PUT | `/api/bookings/{id}/status` | Update booking status | `{ "status": "accepted\|in_progress\|completed\|cancelled" }` | `Authorization: Bearer {token}` | `200: { "booking": { ... }, "message": "Status updated" }` |
| POST | `/api/bookings/{id}/accept` | Washer accept booking | - | `Authorization: Bearer {token}` | `200: { "booking": { ... }, "message": "Booking accepted" }` |

## Payment Endpoints

| Method | Endpoint | Description | Request Body | Headers | Success Response |
|--------|----------|-------------|--------------|---------|------------------|
| GET | `/api/bookings/{id}/payment` | Get payment page | - | `Authorization: Bearer {token}` | `200: { "booking": { ... }, "stripe_key": "string" }` |
| POST | `/api/bookings/{id}/payment` | Process payment | `{ "payment_method": "string", "token": "string" }` | `Authorization: Bearer {token}` | `200: { "payment": { ... }, "message": "Payment successful" }` |
| GET | `/api/payments/{id}/success` | Payment success | - | `Authorization: Bearer {token}` | `200: { "booking": { ... }, "payment": { ... } }` |

## Rating Endpoints

| Method | Endpoint | Description | Request Body | Headers | Success Response |
|--------|----------|-------------|--------------|---------|------------------|
| GET | `/api/bookings/{id}/rate` | Get rating form | - | `Authorization: Bearer {token}` | `200: { "booking": { ... } }` |
| POST | `/api/bookings/{id}/rate` | Submit rating | `{ "rating": "integer(1-5)", "comment": "string" }` | `Authorization: Bearer {token}` | `201: { "rating": { ... }, "message": "Rating submitted" }` |

## Washer Endpoints

| Method | Endpoint | Description | Request Body | Headers | Success Response |
|--------|----------|-------------|--------------|---------|------------------|
| GET | `/api/washer/dashboard` | Washer dashboard | - | `Authorization: Bearer {token}` | `200: { "stats": { ... }, "available_bookings": [{ ... }] }` |
| GET | `/api/washer/bookings` | Washer's bookings | - | `Authorization: Bearer {token}` | `200: { "bookings": [{ ... }] }` |
| PUT | `/api/washer/location` | Update washer location | `{ "latitude": "float", "longitude": "float", "address": "string" }` | `Authorization: Bearer {token}` | `200: { "user": { ... }, "message": "Location updated" }` |

## Service Types and Pricing

### Service Types
```json
{
  "service_types": [
    {
      "id": "car_wash",
      "name": "Car Wash",
      "description": "Exterior and interior cleaning"
    },
    {
      "id": "detailing",
      "name": "Detailing",
      "description": "Premium deep cleaning and polishing"
    }
  ]
}
```

### Vehicle Types and Pricing
```json
{
  "pricing": {
    "car_wash": {
      "sedan": 25.00,
      "suv": 35.00,
      "truck": 45.00,
      "van": 40.00
    },
    "detailing": {
      "sedan": 100.00,
      "suv": 150.00,
      "truck": 200.00,
      "van": 180.00
    }
  }
}
```

## Booking Status Flow

| Status | Description | Who Can Change |
|--------|-------------|----------------|
| `pending` | Booking created, waiting for washer | System/Washer |
| `accepted` | Washer accepted the booking | Washer |
| `in_progress` | Washer started the service | Washer |
| `completed` | Service completed | Washer |
| `cancelled` | Booking cancelled | User/Washer |

## Error Responses

| HTTP Code | Error Type | Example Response |
|-----------|------------|------------------|
| 400 | Validation Error | `{ "message": "The given data was invalid.", "errors": { "field": ["Error message"] } }` |
| 401 | Unauthenticated | `{ "message": "Unauthenticated." }` |
| 403 | Forbidden | `{ "message": "This action is unauthorized." }` |
| 404 | Not Found | `{ "message": "Booking not found." }` |
| 422 | Payment Failed | `{ "message": "Payment failed: Insufficient funds." }` |
| 500 | Server Error | `{ "message": "Server error occurred." }` |

## Request/Response Examples

### Create Booking Request
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "service_type": "car_wash",
    "vehicle_type": "suv",
    "vehicle_model": "Toyota RAV4",
    "vehicle_plate": "ABC123",
    "location": "Yeka Main St, Addis Ababa",
    "latitude": -1.286389,
    "longitude": 36.817223,
    "scheduled_time": "2024-01-15 14:00:00",
    "special_instructions": "Please focus on interior vacuuming"
  }'
```

### Create Booking Response
```json
{
  "booking": {
    "id": 1,
    "user_id": 1,
    "service_type": "car_wash",
    "vehicle_type": "suv",
    "vehicle_model": "Toyota RAV4",
    "vehicle_plate": "ABC123",
    "location": "Yeka Main St, Addis Ababa",
    "latitude": -1.286389,
    "longitude": 36.817223,
    "scheduled_time": "2024-01-15 14:00:00",
    "price": 35.00,
    "status": "pending",
    "special_instructions": "Please focus on interior vacuuming",
    "created_at": "2024-01-10T10:00:00.000000Z",
    "updated_at": "2024-01-10T10:00:00.000000Z"
  },
  "message": "Booking created successfully!"
}
```

### Payment Request
```bash
curl -X POST http://localhost:8000/api/bookings/1/payment \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_method": "pm_card_visa",
    "token": "tok_visa"
  }'
```

### Rating Request
```bash
curl -X POST http://localhost:8000/api/bookings/1/rate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "rating": 5,
    "comment": "Excellent service! Very thorough cleaning."
  }'
```

## Webhook Endpoints (for external services)

| Method | Endpoint | Description | Headers | Request Body |
|--------|----------|-------------|---------|--------------|
| POST | `/api/webhooks/stripe` | Stripe webhook | `Stripe-Signature: string` | Stripe event object |
| POST | `/api/webhooks/payment-callback` | Payment callback | `Authorization: Bearer {token}` | Payment status update |

## Rate Limiting

| Endpoint Group | Requests | Per |
|----------------|----------|-----|
| Authentication | 5 | Minute |
| Bookings | 60 | Minute |
| Payments | 30 | Minute |
| General API | 1000 | Hour |

## Headers

### Required Headers for Authenticated Endpoints
```http
Authorization: Bearer {api_token}
Accept: application/json
Content-Type: application/json
```

### Optional Headers
```http
X-Requested-With: XMLHttpRequest
X-Localization: en
```

## Environment Variables

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000
```

This API documentation provides a complete reference for frontend developers to integrate with your CleanAndDry car wash application backend.