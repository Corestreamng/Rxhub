# RxHub API Documentation v2.0

## Overview
The RxHub API provides secure programmatic access to healthcare supply chain management features. This RESTful API is designed for mobile and web applications with enterprise-grade security.

## Base URL
```
Production: https://rxhub.com.ng/api
Development: https://dev.rxhub.com.ng/api
```

## Authentication & Security

### Authentication Methods
1. **Web Applications**: Session-based authentication with CSRF tokens
2. **Mobile Applications**: JWT (JSON Web Token) based authentication
3. **API Keys**: For server-to-server integrations (contact admin)

### Security Requirements
- ✅ All endpoints require HTTPS (TLS 1.2+)
- ✅ Rate limiting: 100 requests per minute per IP
- ✅ Input validation and sanitization on all endpoints
- ✅ SQL injection prevention via prepared statements
- ✅ XSS protection with output encoding
- ✅ CSRF protection for web clients
- ✅ Password requirements: Min 8 characters, complexity enforced
- ✅ Account lockout after 5 failed login attempts (15 min)
- ✅ JWT tokens expire after 24 hours (refresh required)

### JWT Authentication Flow
1. Login with credentials to receive JWT token
2. Include token in `Authorization` header: `Bearer <token>`
3. Refresh token before expiration using refresh endpoint

---

## Authentication Endpoints

### 1. Register User
**POST** `/api/auth/register.php`

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "full_name": "John Doe",
  "email": "user@example.com",
  "phone": "+2348012345678",
  "facility_name": "ABC Pharmacy",
  "facility_type": "pharmacy",
  "password": "SecurePass123!",
  "address": "123 Main Street, Lagos"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Account created successfully",
  "user_id": 123,
  "email": "user@example.com"
}
```

**Validation Rules:**
- `full_name`: Required, 3-100 characters
- `email`: Required, valid email format, unique
- `phone`: Required, valid phone format
- `password`: Required, min 8 characters
- `facility_type`: Optional, enum: pharmacy|hospital|clinic|lab

---

### 2. Login
**POST** `/api/auth/login.php`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "SecurePass123!",
  "device_type": "mobile|web",
  "device_name": "iPhone 13"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refresh_token": "def50200...",
  "expires_in": 86400,
  "user": {
    "id": 123,
    "full_name": "John Doe",
    "email": "user@example.com",
    "facility_name": "ABC Pharmacy",
    "role": "user"
  }
}
```

---

### 3. Refresh Token
**POST** `/api/auth/refresh.php`

**Request Headers:**
```
Authorization: Bearer <refresh_token>
```

**Response (200 OK):**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 86400
}
```

---

### 4. Logout
**POST** `/api/auth/logout.php`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## Product Endpoints

### 5. Get Products
**GET** `/api/products.php?category={id}&search={term}&page={num}&limit={num}`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Query Parameters:**
- `category` (optional): Filter by category ID
- `search` (optional): Search products by name
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 20, max: 100)

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "sku": "MED-001",
      "name": "Paracetamol 500mg",
      "description": "Pain reliever and fever reducer",
      "category_id": 5,
      "category_name": "Analgesics",
      "manufacturer": "PharmaCo Ltd",
      "selling_price": 2500.00,
      "stock_available": true,
      "quantity_available": 150
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_items": 98,
    "items_per_page": 20
  }
}
```

---

### 6. Get Product Details
**GET** `/api/products.php?id={product_id}`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "sku": "MED-001",
    "name": "Paracetamol 500mg",
    "description": "Pain reliever and fever reducer",
    "category_id": 5,
    "category_name": "Analgesics",
    "manufacturer": "PharmaCo Ltd",
    "purchase_price": 1800.00,
    "selling_price": 2500.00,
    "stock_details": [
      {
        "batch_number": "BATCH-2024-001",
        "quantity": 50,
        "expiry_date": "2025-12-31"
      }
    ]
  }
}
```

---

## Order Endpoints

### 7. Create Order
**POST** `/api/orders/create.php`

**Request Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 10,
      "unit_price": 2500.00
    },
    {
      "product_id": 5,
      "quantity": 5,
      "unit_price": 3500.00
    }
  ],
  "notes": "Urgent delivery required",
  "delivery_address": "123 Main Street, Lagos"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Order created successfully",
  "order": {
    "id": 456,
    "order_number": "ORD-20251213-ABCD1234",
    "total": 42500.00,
    "status": "pending",
    "payment_status": "unpaid",
    "created_at": "2025-12-13T21:30:00Z"
  }
}
```

---

### 8. Get Orders
**GET** `/api/orders.php?status={status}&page={num}&limit={num}`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Query Parameters:**
- `status` (optional): Filter by status (pending|confirmed|processing|completed|cancelled)
- `page` (optional): Page number
- `limit` (optional): Items per page

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 456,
      "order_number": "ORD-20251213-ABCD1234",
      "total": 42500.00,
      "status": "confirmed",
      "payment_status": "paid",
      "item_count": 2,
      "created_at": "2025-12-13T21:30:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 3,
    "total_items": 52
  }
}
```

---

### 9. Get Order Details
**GET** `/api/orders.php?id={order_id}`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 456,
    "order_number": "ORD-20251213-ABCD1234",
    "status": "confirmed",
    "payment_status": "paid",
    "total": 42500.00,
    "notes": "Urgent delivery required",
    "items": [
      {
        "product_id": 1,
        "product_name": "Paracetamol 500mg",
        "quantity": 10,
        "unit_price": 2500.00,
        "subtotal": 25000.00
      }
    ],
    "created_at": "2025-12-13T21:30:00Z",
    "updated_at": "2025-12-13T22:00:00Z"
  }
}
```

---

## Payment Endpoints

### 10. Process Payment
**POST** `/api/payments/process.php`

**Request Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "order_id": 456,
  "payment_method": "transfer",
  "amount": 42500.00,
  "reference": "TXN-REF-123456",
  "notes": "Bank transfer completed"
}
```

**Supported Payment Methods:**
- `transfer`: Bank Transfer
- `pos`: Point of Sale
- `cash`: Cash Payment
- `card`: Card Payment
- `mobile_money`: Mobile Money

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Payment processed successfully",
  "payment": {
    "id": 789,
    "payment_reference": "PAY-20251213-XYZ9876",
    "order_id": 456,
    "amount": 42500.00,
    "payment_method": "transfer",
    "status": "confirmed",
    "payment_date": "2025-12-13T22:15:00Z"
  }
}
```

---

### 11. Get Payment History
**GET** `/api/payments.php?page={num}&limit={num}`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 789,
      "payment_reference": "PAY-20251213-XYZ9876",
      "order_number": "ORD-20251213-ABCD1234",
      "amount": 42500.00,
      "payment_method": "transfer",
      "status": "confirmed",
      "payment_date": "2025-12-13T22:15:00Z"
    }
  ]
}
```

---

## Invoice Endpoints

### 12. Get Invoices
**GET** `/api/invoices.php?status={status}&page={num}`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 101,
      "invoice_number": "INV-20251213-001",
      "order_number": "ORD-20251213-ABCD1234",
      "amount": 42500.00,
      "status": "paid",
      "issued_date": "2025-12-13T22:00:00Z",
      "due_date": "2025-12-20T23:59:59Z"
    }
  ]
}
```

---

### 13. Download Invoice PDF
**GET** `/api/invoices/download.php?id={invoice_id}`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Response:** PDF file download

---

## Analytics Endpoints

### 14. Get User Analytics
**GET** `/api/analytics/user.php?period={period}`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Query Parameters:**
- `period`: day|week|month|quarter|year (default: month)

**Response (200 OK):**
```json
{
  "success": true,
  "period": "month",
  "data": {
    "total_orders": 25,
    "total_spent": 125000.00,
    "average_order_value": 5000.00,
    "pending_payments": 15000.00,
    "top_products": [
      {
        "product_id": 1,
        "product_name": "Paracetamol 500mg",
        "quantity": 150,
        "total_spent": 37500.00
      }
    ],
    "monthly_trend": [
      {
        "month": "2025-11",
        "orders": 20,
        "total": 100000.00
      }
    ]
  }
}
```

---

## Investment Endpoints (For Investors)

### 15. Get Investment Opportunities
**GET** `/api/investments/opportunities.php`

**Request Headers:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Pharmaceutical Expansion Project",
      "description": "Expand supply chain network",
      "category": "equity",
      "min_investment": 100000.00,
      "target_amount": 5000000.00,
      "current_amount": 3500000.00,
      "expected_roi": "25% annually",
      "duration_months": 24,
      "risk_level": "medium",
      "status": "open"
    }
  ]
}
```

---

### 16. Make Investment
**POST** `/api/investments/invest.php`

**Request Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "investment_option_id": 1,
  "amount": 500000.00,
  "payment_method": "transfer",
  "payment_reference": "INV-TXN-123456"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Investment recorded successfully",
  "investment": {
    "id": 234,
    "investment_option_id": 1,
    "amount": 500000.00,
    "status": "pending",
    "investment_date": "2025-12-13T22:30:00Z"
  }
}
```

---

## Error Responses

### Standard Error Format
All errors follow this format:

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human readable error message",
    "details": {}
  }
}
```

### Common HTTP Status Codes
- `200 OK`: Request successful
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid input or validation error
- `401 Unauthorized`: Missing or invalid authentication
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `409 Conflict`: Resource already exists
- `422 Unprocessable Entity`: Validation failed
- `429 Too Many Requests`: Rate limit exceeded
- `500 Internal Server Error`: Server error
- `503 Service Unavailable`: Service temporarily unavailable

### Error Codes
- `AUTH_FAILED`: Authentication failed
- `INVALID_TOKEN`: JWT token invalid or expired
- `VALIDATION_ERROR`: Input validation failed
- `RESOURCE_NOT_FOUND`: Requested resource not found
- `INSUFFICIENT_PERMISSIONS`: User lacks required permissions
- `RATE_LIMIT_EXCEEDED`: Too many requests
- `DUPLICATE_ENTRY`: Resource already exists
- `PAYMENT_FAILED`: Payment processing failed
- `INSUFFICIENT_STOCK`: Product out of stock

---

## Rate Limiting

**Limits:**
- 100 requests per minute per IP address
- 1000 requests per hour per authenticated user

**Response Headers:**
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 85
X-RateLimit-Reset: 1702503600
```

**Rate Limit Error (429):**
```json
{
  "success": false,
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Too many requests. Please try again later.",
    "retry_after": 60
  }
}
```

---

## Webhooks (Coming Soon)

RxHub will support webhooks for real-time event notifications:
- Order status changes
- Payment confirmations
- Stock alerts
- Investment updates

---

## SDK Support

### Official SDKs (Planned)
- JavaScript/TypeScript (Node.js & Browser)
- React Native
- Flutter/Dart
- Python
- PHP

---

## Testing

### Sandbox Environment
```
Base URL: https://sandbox.rxhub.com.ng/api
```

**Test Credentials:**
```
Email: test@rxhub.com.ng
Password: Test123!
```

---

## Support & Contact

**Technical Support:**
- Email: api-support@rxhub.com.ng
- Phone: +234-800-RXHUB-API
- Documentation: https://docs.rxhub.com.ng

**Emergency Contact:**
- 24/7 Support: support@rxhub.com.ng

---

## Changelog

### Version 2.0 (2025-12-13)
- Added JWT authentication for mobile apps
- Enhanced security measures
- Expanded endpoint documentation
- Added rate limiting
- Added analytics endpoints
- Improved error handling
- Added pagination support

### Version 1.0 (2025-01-01)
- Initial API release
- Basic authentication
- Core endpoints

---

**Last Updated:** 2025-12-13  
**API Version:** 2.0  
**Documentation Version:** 2.0.0
