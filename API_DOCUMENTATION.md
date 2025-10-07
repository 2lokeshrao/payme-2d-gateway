# PayMe 2D Gateway - API Documentation

## Table of Contents
1. [Authentication](#authentication)
2. [Authentication APIs](#authentication-apis)
3. [Payment APIs](#payment-apis)
4. [Merchant APIs](#merchant-apis)
5. [Admin APIs](#admin-apis)
6. [Error Handling](#error-handling)
7. [Rate Limiting](#rate-limiting)

---

## Authentication

All merchant API requests require an API Key in the header:

```
X-API-Key: your_api_key_here
```

Admin APIs require session-based authentication.

---

## Authentication APIs

### 1. User Login

**Endpoint:** `POST /api/auth/login.php`

**Description:** Authenticate users (Admin, Merchant, Reseller, Client)

**Request Body:**
```json
{
  "email": "merchant@test.com",
  "password": "Merchant@2025"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user_id": 2,
    "user_type": "merchant",
    "email": "merchant@test.com",
    "full_name": "Test Business Owner",
    "csrf_token": "abc123...",
    "merchant": {
      "merchant_id": "MERCH001",
      "business_name": "Test Business",
      "kyc_status": "verified",
      "api_key": "pk_test_51234567890abcdef"
    }
  }
}
```

**Error Responses:**
- `400` - Missing required fields
- `401` - Invalid credentials
- `403` - Email not verified
- `429` - Too many login attempts

---

### 2. User Registration

**Endpoint:** `POST /api/auth/register.php`

**Description:** Register new merchant or reseller

**Request Body:**
```json
{
  "user_type": "merchant",
  "email": "newmerchant@example.com",
  "password": "SecurePass@123",
  "full_name": "John Doe",
  "phone": "+919876543210",
  "business_name": "My Business",
  "business_type": "E-commerce"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Registration successful! Please check your email for verification.",
  "data": {
    "user_id": 5,
    "email": "newmerchant@example.com",
    "user_type": "merchant"
  }
}
```

**Error Responses:**
- `400` - Invalid input or weak password
- `409` - Email already registered

---

## Payment APIs

### 3. Create Payment Link

**Endpoint:** `POST /api/payment/create_payment_link.php`

**Headers:**
```
X-API-Key: pk_test_51234567890abcdef
Content-Type: application/json
```

**Request Body:**
```json
{
  "amount": 1500.00,
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "+919876543212",
  "payment_method": "upi",
  "description": "Order #12345",
  "callback_url": "https://yoursite.com/webhook",
  "redirect_url": "https://yoursite.com/success"
}
```

**Payment Methods:**
- `upi` - UPI Payment
- `crypto` - Cryptocurrency
- `bank_transfer` - Bank Transfer
- `card` - Card Payment
- `wallet` - Digital Wallet

**Success Response (201):**
```json
{
  "success": true,
  "message": "Payment link created successfully",
  "data": {
    "transaction_id": "TXN1a2b3c4d5e6f7g8h9",
    "payment_link": "https://hungry-nights-cough.lindy.site/payment.html?txn=TXN1a2b3c4d5e6f7g8h9",
    "amount": 1500.00,
    "currency": "INR",
    "payment_method": "upi",
    "status": "pending",
    "created_at": "2025-10-07 17:30:00"
  }
}
```

**Error Responses:**
- `400` - Invalid input
- `401` - Invalid API Key
- `403` - Merchant not active or KYC not verified

---

### 4. Verify Payment

**Endpoint:** `POST /api/payment/verify_payment.php`

**Headers:**
```
X-API-Key: pk_test_51234567890abcdef
Content-Type: application/json
```

**Request Body:**
```json
{
  "transaction_id": "TXN1a2b3c4d5e6f7g8h9",
  "payment_status": "success",
  "utr_number": "UTR123456789",
  "gateway_transaction_id": "GTW987654321"
}
```

**Payment Status Values:**
- `success` - Payment successful
- `failed` - Payment failed
- `pending` - Payment pending
- `processing` - Payment processing

**Success Response (200):**
```json
{
  "success": true,
  "message": "Payment verified successfully",
  "data": {
    "transaction_id": "TXN1a2b3c4d5e6f7g8h9",
    "amount": 1500.00,
    "currency": "INR",
    "status": "success",
    "utr_number": "UTR123456789",
    "gateway_transaction_id": "GTW987654321",
    "verified_at": "2025-10-07 17:35:00"
  }
}
```

**Error Responses:**
- `400` - Invalid input
- `401` - Invalid API Key
- `404` - Transaction not found

---

## Merchant APIs

### 5. Get Transactions

**Endpoint:** `GET /api/merchant/get_transactions.php`

**Headers:**
```
X-API-Key: pk_test_51234567890abcdef
```

**Query Parameters:**
- `status` (optional) - Filter by status: `success`, `pending`, `failed`
- `limit` (optional) - Number of records (max 100, default 50)
- `offset` (optional) - Pagination offset (default 0)
- `from_date` (optional) - Start date (YYYY-MM-DD)
- `to_date` (optional) - End date (YYYY-MM-DD)

**Example:**
```
GET /api/merchant/get_transactions.php?status=success&limit=10&offset=0
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Transactions retrieved successfully",
  "data": {
    "transactions": [
      {
        "transaction_id": "TXN001",
        "payment_method": "upi",
        "amount": 1500.00,
        "currency": "INR",
        "customer_name": "John Doe",
        "customer_email": "john@example.com",
        "payment_status": "success",
        "utr_number": "UTR123456",
        "created_at": "2025-10-02 10:30:00",
        "completed_at": "2025-10-02 10:32:00"
      }
    ],
    "pagination": {
      "total": 234,
      "limit": 10,
      "offset": 0,
      "has_more": true
    },
    "statistics": {
      "total_transactions": 234,
      "total_revenue": 45680.00,
      "successful_transactions": 230,
      "pending_transactions": 2,
      "failed_transactions": 2,
      "success_rate": 98.29
    }
  }
}
```

---

### 6. Upload KYC Document

**Endpoint:** `POST /api/merchant/upload_kyc.php`

**Headers:**
```
X-API-Key: pk_test_51234567890abcdef
Content-Type: multipart/form-data
```

**Form Data:**
- `document` (file) - Document file (JPG, PNG, PDF, max 5MB)
- `document_type` (string) - Type of document
- `document_number` (string, optional) - Document number

**Document Types:**
- `pan_card` - PAN Card
- `aadhar_card` - Aadhar Card
- `gst_certificate` - GST Certificate
- `bank_statement` - Bank Statement
- `business_proof` - Business Proof
- `other` - Other Documents

**Success Response (201):**
```json
{
  "success": true,
  "message": "KYC document uploaded successfully",
  "data": {
    "document_id": 5,
    "document_type": "pan_card",
    "verification_status": "pending",
    "uploaded_at": "2025-10-07 17:40:00"
  }
}
```

**Error Responses:**
- `400` - Invalid file type or size
- `401` - Invalid API Key

---

## Admin APIs

### 7. Verify KYC Document

**Endpoint:** `POST /api/admin/verify_kyc.php`

**Authentication:** Session-based (Admin only)

**Request Body:**
```json
{
  "document_id": 5,
  "action": "verify",
  "rejection_reason": ""
}
```

**Actions:**
- `verify` - Approve document
- `reject` - Reject document (requires `rejection_reason`)

**Success Response (200):**
```json
{
  "success": true,
  "message": "KYC document verified successfully",
  "data": {
    "document_id": 5,
    "merchant_id": "MERCH001",
    "verification_status": "verified",
    "merchant_kyc_status": "verified",
    "verified_at": "2025-10-07 17:45:00"
  }
}
```

**Error Responses:**
- `400` - Invalid action or missing rejection reason
- `403` - Admin access required
- `404` - Document not found

---

## Error Handling

All API errors follow this format:

```json
{
  "success": false,
  "message": "Error description here"
}
```

### HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request (Invalid input)
- `401` - Unauthorized (Invalid credentials/API key)
- `403` - Forbidden (Access denied)
- `404` - Not Found
- `405` - Method Not Allowed
- `409` - Conflict (Duplicate entry)
- `429` - Too Many Requests (Rate limit exceeded)
- `500` - Internal Server Error

---

## Rate Limiting

### Login API
- **Limit:** 10 requests per 5 minutes per IP
- **Response:** `429 Too Many Requests`

### Payment APIs
- **Limit:** 100 requests per hour per merchant
- **Response:** `429 Too Many Requests`

---

## Webhook Callbacks

When a payment is successful, PayMe Gateway sends a POST request to your `callback_url`:

**Webhook Payload:**
```json
{
  "transaction_id": "TXN1a2b3c4d5e6f7g8h9",
  "merchant_id": "MERCH001",
  "amount": 1500.00,
  "currency": "INR",
  "status": "success",
  "utr_number": "UTR123456789",
  "gateway_transaction_id": "GTW987654321",
  "customer_email": "john@example.com",
  "timestamp": "2025-10-07 17:35:00"
}
```

**Webhook Security:**
- Verify the transaction using the Verify Payment API
- Check the transaction_id matches your records
- Validate the amount and merchant_id

---

## Testing

### Test Credentials

**Admin:**
- Email: `admin@payme.com`
- Password: `admin123`

**Merchant:**
- Email: `merchant@test.com`
- Password: `Merchant@2025`
- API Key: `pk_test_51234567890abcdef`

### Test Payment Methods

**UPI:**
- Use any UPI ID for testing
- All test payments will be marked as successful

**Crypto:**
- Use test wallet addresses
- Instant confirmation in test mode

---

## Support

For API support, contact:
- Email: support@payme.com
- Documentation: https://hungry-nights-cough.lindy.site/docs
- GitHub: https://github.com/2lokeshrao/payme-2d-gateway

---

**Last Updated:** October 7, 2025
**Version:** 1.0.0
