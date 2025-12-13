# RxHub API Documentation

## Authentication
- All endpoints require HTTPS.
- Web clients use session cookies and CSRF tokens for browser security.
- Mobile apps should use secure login endpoints to obtain a session or token.

---

## User Authentication
### Register
- **POST** `/api/auth/register.php`
- **Body:** `application/json`
```
{
  "full_name": "John Doe",
  "email": "user@example.com",
  "phone": "+2348000000000",
  "facility_name": "Pharmacy X",
  "password": "password123"
}
```
- **Response:**
```
{
  "success": true,
  "message": "Account created successfully!"
}
```

### Login
- **POST** `/api/auth/login.php`
- **Body:** `application/json`
```
{
  "email": "user@example.com",
  "password": "password123"
}
```
- **Response:**
```
{
  "success": true,
  "message": "Login successful",
  "session_id": "..." // For mobile apps, use this for subsequent requests
}
```

---

## Orders
### Create Order
- **POST** `/api/process_order.php`
- **Body:** `application/json`
```
{
  "items": [
    { "id": 1, "quantity": 2 },
    { "id": 5, "quantity": 1 }
  ]
}
```
- **Response:**
```
{
  "success": true,
  "order_number": "ORD-20251213-ABCD1234",
  "order_id": 123,
  "total": 5000.00
}
```

### Process Payment
- **POST** `/api/process_payment.php`
- **Body:** `application/json`
```
{
  "order_id": 123,
  "payment_method": "transfer|pos|cash",
  "reference": "optional-ref"
}
```
- **Response:**
```
{
  "success": true,
  "payment_reference": "PAY-20251213-XYZ9876"
}
```

---

## Admin APIs
### User Management
- **POST** `/api/admin/users.php` (create, update, delete users)
- **POST** `/api/admin/products.php` (manage products)
- **POST** `/api/admin/investments.php` (manage investments)
- **POST** `/api/admin/settings.php` (update settings)

---

## Security Notes
- All POST requests must be made over HTTPS.
- Web clients must include a valid CSRF token in forms.
- Mobile clients should use the session ID or a secure token after login.
- All input is validated and sanitized server-side.
- Rate limiting and brute-force protection are recommended for production.

---

## Error Responses
All endpoints return JSON with `success: false` and a `message` field on error.

```
{
  "success": false,
  "message": "Error description here."
}
```

---

For further details or support, contact info@rxhub.com.ng
