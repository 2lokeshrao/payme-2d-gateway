# 💳 PayMe 2D Gateway - पूर्ण उपयोगकर्ता और व्यवस्थापक गाइड

## 📚 विषय सूची
1. [सिस्टम का परिचय](#सिस्टम-का-परिचय)
2. [दो मुख्य उपयोग मॉडल](#दो-मुख्य-उपयोग-मॉडल)
3. [Admin Panel गाइड](#admin-panel-गाइड)
4. [Reseller Panel गाइड](#reseller-panel-गाइड)
5. [User Panel गाइड](#user-panel-गाइड)
6. [Self Payment Gateway Setup](#self-payment-gateway-setup)
7. [Subscription Plans](#subscription-plans)
8. [Payment Methods](#payment-methods)
9. [API Integration](#api-integration)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 सिस्टम का परिचय

**PayMe 2D Gateway** एक पूर्ण Payment Gateway Solution है जो दो तरीकों से काम करता है:

### ✅ मॉडल 1: मुख्य Payment Gateway (Admin द्वारा संचालित)
- Admin पूरे सिस्टम को control करता है
- Resellers को onboard करता है
- Activation codes generate करता है
- सभी transactions को manage करता है

### ✅ मॉडल 2: Self Payment Gateway (Individual Users के लिए)
- कोई भी user subscription खरीद सकता है
- अपना खुद का payment gateway setup कर सकता है
- सीधे अपने bank account/UPI/crypto wallet में payment receive कर सकता है
- अपने customers को payment link भेज सकता है

---

## 🏢 दो मुख्य उपयोग मॉडल

### 📊 मॉडल 1: Central Gateway (Admin + Resellers + End Users)

```
┌─────────────────────────────────────────────────────────────┐
│                    MAIN ADMIN                                │
│  • पूरे system को control करता है                            │
│  • Subscription plans बनाता है                               │
│  • Resellers को onboard करता है                             │
│  • Activation codes generate करता है                         │
│  • सभी payments को manage करता है                            │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    RESELLERS                                 │
│  • Admin से plans assign होते हैं                            │
│  • Activation codes generate करते हैं                        │
│  • Customers को codes बेचते हैं                              │
│  • Commission earn करते हैं (जैसे 20%)                       │
│  • अपनी sales track करते हैं                                 │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    END USERS                                 │
│  • Activation code खरीदते हैं                                │
│  • Code enter करके subscription activate करते हैं            │
│  • Payment gateway features use करते हैं                     │
│  • Transactions manage करते हैं                              │
└─────────────────────────────────────────────────────────────┘
```

### 💼 मॉडल 2: Self Payment Gateway (Individual Business)

```
┌─────────────────────────────────────────────────────────────┐
│                    INDIVIDUAL USER                           │
│  • Subscription plan खरीदता है                               │
│  • अपना payment gateway setup करता है                        │
│  • अपने bank/UPI/crypto details add करता है                 │
│  • अपने customers को payment links भेजता है                  │
│  • सीधे अपने account में payment receive करता है             │
└─────────────────────────────────────────────────────────────┘
```

---

## 👨‍💼 Admin Panel गाइड

### 🔐 Admin Login
**URL:** `/admin/login.html`

**Default Credentials:**
- Username: `admin`
- Password: `admin123` (पहली login के बाद बदलें)

### 📊 Admin Dashboard Features

#### 1️⃣ **Plan Management** (`/admin/plan-management.html`)

**Subscription Plans कैसे बनाएं:**

1. **Plan Management** पर जाएं
2. **Create New Plan** button click करें
3. Details भरें:
   - **Plan Name:** जैसे "Monthly Pro"
   - **Plan Type:** Monthly/Quarterly/Yearly/Lifetime
   - **Price:** ₹60 या $60
   - **Duration:** 30 days (monthly के लिए)
   - **Features:** एक line में एक feature
     ```
     Self Bank Account Integration
     Self UPI Payment Collection
     Self Crypto Wallet Integration
     Unlimited Transactions
     API Access
     24/7 Support
     ```
4. **Create Plan** click करें

**Plan Types:**
- **Monthly:** 30 days validity
- **Quarterly:** 90 days validity
- **Yearly:** 365 days validity
- **Lifetime:** Unlimited validity
- **Custom:** अपनी मर्जी से days set करें

#### 2️⃣ **Reseller Management** (`/admin/reseller-management.html`)

**Reseller कैसे बनाएं:**

1. **Reseller Management** पर जाएं
2. **Add New Reseller** click करें
3. Details भरें:
   - **Name:** Reseller का नाम
   - **Email:** Reseller की email
   - **Phone:** Contact number
   - **Password:** Login password (secure रखें)
   - **Commission Rate:** 20% (या जो भी rate देना चाहें)
   - **Allowed Plans:** वो plans select करें जो reseller बेच सकता है
4. **Create Reseller** click करें
5. Login credentials reseller को share करें

**Reseller को क्या मिलता है:**
- अपना login portal: `/reseller/login.html`
- Code generation access
- Sales statistics
- Commission tracking
- Customer management

**Commission कैसे काम करता है:**
```
Plan Price: ₹100
Commission Rate: 20%
Reseller Earns: ₹20
Admin Keeps: ₹80
```

#### 3️⃣ **Activation Code Management** (`/admin/activation-codes.html`)

**Single Code Generate करना:**

1. **Activation Codes** section में जाएं
2. **Generate Single Code** tab select करें
3. Details भरें:
   - **Select Plan:** जो plan activate करना है
   - **Assign to Reseller:** (Optional) किस reseller के लिए
   - **Validity Days:** 365 (1 year)
   - **Customer Email:** (Optional) किस customer के लिए
4. **Generate Code** click करें
5. Code copy करें: `PM2D-1234-5678-9012`

**Bulk Codes Generate करना:**

1. **Generate Bulk Codes** tab select करें
2. Details भरें:
   - **Select Plan:** Plan choose करें
   - **Quantity:** 50 (maximum 100)
   - **Assign to Reseller:** (Optional)
   - **Validity Days:** 365
3. **Generate Bulk Codes** click करें
4. सभी codes download करें (CSV format में)

**Code Format:**
```
PM2D-XXXX-XXXX-XXXX
(16 characters, 4 segments)
```

**Code Status:**
- **Unused:** अभी तक use नहीं हुआ
- **Used:** Activate हो चुका है
- **Expired:** Validity खत्म हो गई
- **Revoked:** Admin ने cancel कर दिया

#### 4️⃣ **Statistics Dashboard**

Admin dashboard पर ये statistics दिखते हैं:

- **Total Plans:** कितने plans बनाए गए
- **Active Plans:** कितने plans active हैं
- **Total Resellers:** कितने resellers हैं
- **Active Resellers:** कितने resellers active हैं
- **Codes Generated:** कितने codes बनाए गए
- **Codes Used:** कितने codes activate हुए
- **Total Revenue:** कुल कमाई
- **Commission Paid:** Resellers को दिया गया commission

---

## 🎯 Reseller Panel गाइड

### 🔐 Reseller Login
**URL:** `/reseller/login.html`

**Login Process:**
1. Admin से मिले email और password enter करें
2. **Login to Dashboard** click करें
3. Reseller dashboard खुल जाएगा

### 📊 Reseller Dashboard Features

#### 1️⃣ **Quick Code Generation**

Dashboard से directly code generate करें:

1. **Select Plan** dropdown से plan choose करें (जो admin ने assign किया है)
2. **Customer Email** enter करें (optional)
3. **Generate Code** click करें
4. Code copy करें और customer को भेजें

#### 2️⃣ **Statistics Tracking**

Dashboard पर ये metrics दिखते हैं:

- **Total Sales:** कितने codes sell किए
- **Active Codes:** कितने codes अभी unused हैं
- **Total Earnings:** कुल commission earned
- **Pending Payout:** जो commission अभी pending है

#### 3️⃣ **Recent Codes**

अपने सभी generated codes की list देखें:
- Code number
- Plan name
- Status (Used/Unused)
- Customer email
- Generated date
- Commission amount

#### 4️⃣ **Customer Management**

अपने customers को track करें:
- Customer name और email
- Active subscription
- Subscription expiry date
- Total amount paid

### 💰 Commission System

**कैसे काम करता है:**

1. Admin आपको 20% commission rate देता है
2. आप ₹100 की plan का code generate करते हैं
3. Customer code activate करता है
4. System automatically calculate करता है:
   - आपका commission: ₹20
   - Admin का share: ₹80
5. आपका commission "Pending Payout" में add हो जाता है
6. Admin monthly/weekly payout process करता है

**Payout कैसे मिलता है:**
- Bank transfer
- UPI payment
- PayPal
- Crypto wallet

---

## 👤 User Panel गाइड

### 🔐 User Registration & Login

**Registration:** `/register.html`
1. Name, Email, Password enter करें
2. **Create Account** click करें
3. Email verification करें (if enabled)

**Login:** `/login.html`
1. Email और Password enter करें
2. **Login** click करें

### 📊 User Dashboard (`/dashboard.html`)

Dashboard पर ये sections हैं:

1. **Statistics Cards:**
   - Total Transactions
   - Total Amount Received
   - Pending Settlements
   - Active Subscription

2. **Quick Actions:**
   - Create Payment Link
   - View Transactions
   - Manage Subscription
   - Configure Self Payment

3. **Recent Transactions:**
   - Latest 10 transactions
   - Amount, Status, Date
   - Customer details

### 🔑 Subscription Activation (`/activate-subscription.html`)

**Activation Code कैसे use करें:**

1. **Activate Subscription** page पर जाएं
2. 16-character code enter करें:
   ```
   PM2D-1234-5678-9012
   ```
3. **Activate Subscription** click करें
4. Success message आएगा
5. Subscription details दिखेंगे:
   - Plan name
   - Duration
   - Start date
   - End date

**Code Entry Tips:**
- Paste करने पर automatically segments में divide हो जाता है
- Uppercase/lowercase दोनों काम करते हैं
- Spaces और dashes ignore हो जाते हैं

### 💎 Subscription Management (`/subscription.html`)

**Current Subscription देखें:**

यहाँ आपको दिखेगा:
- Current plan name
- Plan price
- Subscription status (Active/Expired)
- Start date
- End date
- Days remaining
- Features list

**Available Plans:**

सभी available plans देखें और compare करें:
- Monthly: ₹60/month
- Quarterly: ₹150/3 months (17% savings)
- Yearly: ₹500/year (30% savings)

**Subscription Renew करना:**

1. Expiry से पहले नया activation code खरीदें
2. Code activate करें
3. Subscription extend हो जाएगा

---

## 💰 Self Payment Gateway Setup

### 🎯 Self Payment Gateway क्या है?

**यह feature आपको अपना खुद का payment gateway बनाने देता है:**

✅ अपने bank account में directly payment receive करें
✅ अपने UPI ID पर instant payments लें
✅ अपने crypto wallets में payments लें
✅ कोई middleman नहीं - सीधे आपके account में
✅ अपने customers को payment links भेजें
✅ अपनी website पर integrate करें

### 📋 Requirements

Self Payment Gateway use करने के लिए:
1. ✅ Active subscription होना चाहिए
2. ✅ Bank account/UPI/Crypto wallet details add करें
3. ✅ Verification complete करें

### 🏦 Bank Account Setup

**URL:** `/self-payment-settings.html`

**Bank Details कैसे add करें:**

1. **Self Payment Settings** page पर जाएं
2. **Bank Account Details** section में:
   - **Bank Name:** Select करें (HDFC, ICICI, SBI, etc.)
   - **Account Type:** Savings या Current
   - **Account Holder Name:** अपना नाम (bank के अनुसार)
   - **Account Number:** अपना account number
   - **IFSC Code:** Bank की IFSC code
   - **Confirm Account Number:** फिर से account number
3. **Save Bank Details** click करें
4. Verification के लिए ₹1 test deposit होगा
5. Verify करने के बाद status "Verified" हो जाएगा

**Supported Banks:**
- HDFC Bank
- ICICI Bank
- State Bank of India
- Axis Bank
- Kotak Mahindra Bank
- Punjab National Bank
- Bank of Baroda
- Canara Bank
- और सभी major banks

### 📱 UPI Payment Setup

**UPI Details कैसे add करें:**

1. **UPI Payment Settings** section में:
   - **UPI ID:** अपनी UPI ID enter करें
     - Google Pay: `yourname@okaxis`, `yourname@okicici`
     - PhonePe: `yourname@ybl`
     - Paytm: `yourname@paytm`
     - BHIM: `yourname@upi`
   - **UPI Provider:** Select करें
   - **UPI QR Code:** (Optional) अपना QR code upload करें
2. **Save UPI Details** click करें
3. Test payment से verify होगा

**UPI Benefits:**
- ✅ Instant payments (real-time)
- ✅ 24/7 available
- ✅ No transaction charges
- ✅ Works with all UPI apps

### 💎 Cryptocurrency Wallet Setup

**Crypto Wallets कैसे add करें:**

1. **Cryptocurrency Wallets** section में:
   
   **Bitcoin (BTC):**
   - Wallet address enter करें
   - Example: `1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa`
   
   **Ethereum (ETH):**
   - Wallet address enter करें
   - Example: `0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb`
   
   **Tether (USDT):**
   - Wallet address enter करें
   - Network select करें:
     - ERC-20 (Ethereum)
     - TRC-20 (Tron)
     - BEP-20 (Binance Smart Chain)
   
   **Binance Coin (BNB):**
   - Wallet address enter करें (BSC network)

2. **Save Crypto Wallets** click करें

**⚠️ Important:**
- Wallet addresses को double-check करें
- Galat address पर भेजा गया crypto वापस नहीं आता
- Network सही select करें (USDT के लिए)

### ⚡ Settlement Settings

**Auto Settlement कैसे configure करें:**

1. **Settlement Settings** section में:
   - **Auto Settlement:** Toggle ON करें
   - **Settlement Frequency:** Select करें
     - **Instant (Recommended):** तुरंत transfer
     - **Daily:** रोज एक बार
     - **Weekly:** हफ्ते में एक बार
2. **Save Settlement Settings** click करें

**Settlement Process:**
```
Customer Payment → Gateway → Your Account
                    ↓
              (Instant/Daily/Weekly)
```

---

## 💳 Payment Methods Configuration

### 🌐 Payment Methods Page (`/payment-methods.html`)

यहाँ आप configure कर सकते हैं:

#### 1️⃣ **Card Payments**
- Visa
- Mastercard
- RuPay
- American Express

**Setup:**
1. Payment gateway provider select करें
2. API credentials enter करें
3. Test mode enable करें
4. Test transaction करें
5. Live mode activate करें

#### 2️⃣ **UPI Payments**
- Google Pay
- PhonePe
- Paytm
- BHIM UPI
- Any UPI app

**Setup:**
1. अपनी UPI ID add करें
2. QR code generate करें
3. Test payment करें

#### 3️⃣ **Net Banking**
सभी major banks support:
- HDFC Bank
- ICICI Bank
- SBI
- Axis Bank
- और 50+ banks

#### 4️⃣ **Wallets**
- Paytm Wallet
- PhonePe Wallet
- Amazon Pay
- Mobikwik

#### 5️⃣ **Cryptocurrency**
- Bitcoin (BTC)
- Ethereum (ETH)
- Tether (USDT)
- Binance Coin (BNB)

---

## 🔗 API Integration

### 📚 API Documentation

**Base URL:** `https://your-domain.com/api/`

### 1️⃣ **Create Payment Link**

**Endpoint:** `POST /api/create-payment.php`

**Request:**
```json
{
  "amount": 1000,
  "currency": "INR",
  "customer_name": "Rahul Kumar",
  "customer_email": "rahul@example.com",
  "customer_phone": "+919876543210",
  "description": "Product Purchase",
  "callback_url": "https://yoursite.com/payment-success"
}
```

**Response:**
```json
{
  "success": true,
  "payment_id": "PAY_123456789",
  "payment_link": "https://payme-gateway.com/pay/PAY_123456789",
  "qr_code": "data:image/png;base64,..."
}
```

### 2️⃣ **Check Payment Status**

**Endpoint:** `GET /api/get-transaction.php?payment_id=PAY_123456789`

**Response:**
```json
{
  "success": true,
  "transaction": {
    "payment_id": "PAY_123456789",
    "amount": 1000,
    "status": "completed",
    "customer_name": "Rahul Kumar",
    "payment_method": "UPI",
    "created_at": "2025-10-04 10:30:00",
    "completed_at": "2025-10-04 10:31:15"
  }
}
```

### 3️⃣ **Webhook Integration**

**Webhook URL Setup:**
1. `/webhooks.html` page पर जाएं
2. अपना webhook URL add करें
3. Events select करें:
   - Payment Success
   - Payment Failed
   - Refund Processed
4. Secret key generate करें

**Webhook Payload:**
```json
{
  "event": "payment.success",
  "payment_id": "PAY_123456789",
  "amount": 1000,
  "status": "completed",
  "customer_email": "rahul@example.com",
  "timestamp": "2025-10-04T10:31:15Z",
  "signature": "sha256_hash_here"
}
```

### 4️⃣ **SDK Integration**

**JavaScript SDK:**

```html
<script src="https://payme-gateway.com/payme2d-sdk.js"></script>
<script>
  const payme = new PayMe2D({
    apiKey: 'your_api_key_here',
    environment: 'production' // or 'sandbox'
  });

  // Create payment
  payme.createPayment({
    amount: 1000,
    currency: 'INR',
    customer: {
      name: 'Rahul Kumar',
      email: 'rahul@example.com',
      phone: '+919876543210'
    },
    onSuccess: function(response) {
      console.log('Payment successful:', response);
    },
    onFailure: function(error) {
      console.log('Payment failed:', error);
    }
  });
</script>
```

**PHP SDK:**

```php
<?php
require 'payme2d-sdk.php';

$payme = new PayMe2D([
    'api_key' => 'your_api_key_here',
    'environment' => 'production'
]);

$payment = $payme->createPayment([
    'amount' => 1000,
    'currency' => 'INR',
    'customer' => [
        'name' => 'Rahul Kumar',
        'email' => 'rahul@example.com',
        'phone' => '+919876543210'
    ]
]);

echo $payment['payment_link'];
?>
```

---

## 📊 Transaction Management

### 💰 Transactions Page (`/transactions.html`)

**Transaction List में क्या दिखता है:**

- **Transaction ID:** Unique identifier
- **Amount:** Payment amount
- **Customer:** Name और email
- **Payment Method:** UPI/Card/Net Banking/Crypto
- **Status:** 
  - ✅ Completed (Success)
  - ⏳ Pending (Processing)
  - ❌ Failed (Declined)
  - 🔄 Refunded
- **Date & Time:** Transaction timestamp

**Filters:**
- Status filter (All/Completed/Pending/Failed)
- Date range filter
- Payment method filter
- Amount range filter
- Search by customer name/email

**Export Options:**
- CSV export
- Excel export
- PDF report

### 💸 Refunds (`/refunds.html`)

**Refund कैसे process करें:**

1. Transaction list में जाएं
2. Transaction select करें
3. **Refund** button click करें
4. Refund details enter करें:
   - **Refund Amount:** Full या Partial
   - **Reason:** Refund का कारण
   - **Notes:** Additional information
5. **Process Refund** click करें
6. Customer को refund हो जाएगा

**Refund Timeline:**
- UPI: Instant to 24 hours
- Cards: 5-7 business days
- Net Banking: 3-5 business days
- Crypto: 1-2 hours

---

## 📈 Reports & Analytics

### 📊 Analytics Dashboard (`/analytics.html`)

**Available Reports:**

1. **Revenue Report:**
   - Daily/Weekly/Monthly revenue
   - Revenue by payment method
   - Revenue trends

2. **Transaction Report:**
   - Total transactions
   - Success rate
   - Average transaction value
   - Peak transaction times

3. **Customer Report:**
   - New customers
   - Repeat customers
   - Customer lifetime value

4. **Payment Method Report:**
   - UPI vs Card vs Net Banking
   - Most used payment method
   - Success rate by method

**Export Reports:**
- PDF format
- Excel format
- CSV format
- Email scheduled reports

---

## 🔐 Security Features

### 🛡️ Security Measures

1. **Password Security:**
   - Minimum 8 characters
   - Must include uppercase, lowercase, number
   - Hashed using bcrypt

2. **Two-Factor Authentication (2FA):**
   - Enable from account settings
   - SMS OTP या Authenticator app

3. **API Security:**
   - API keys with expiry
   - IP whitelisting
   - Rate limiting
   - Webhook signature verification

4. **Transaction Security:**
   - SSL/TLS encryption
   - PCI DSS compliant
   - Fraud detection
   - Real-time monitoring

### 🔑 API Keys Management (`/api-keys.html`)

**API Key कैसे generate करें:**

1. **API Keys** page पर जाएं
2. **Generate New Key** click करें
3. Key details enter करें:
   - **Key Name:** "Production API"
   - **Environment:** Production/Sandbox
   - **Permissions:** Select करें
   - **IP Whitelist:** (Optional) Allowed IPs
4. **Generate Key** click करें
5. Key copy करें (एक बार ही दिखेगा)

**API Key Types:**
- **Public Key:** Frontend integration के लिए
- **Secret Key:** Backend API calls के लिए
- **Webhook Secret:** Webhook verification के लिए

---

## 🎓 Use Cases & Examples

### 💼 Use Case 1: E-commerce Website

**Scenario:** आपकी online shop है और आप payments लेना चाहते हैं

**Setup:**
1. Monthly subscription खरीदें (₹60/month)
2. Bank account और UPI details add करें
3. अपनी website पर payment button integrate करें
4. Customers checkout करें और pay करें
5. Payment directly आपके account में आए

**Code Example:**
```html
<button onclick="initiatePayment()">Pay Now</button>

<script>
function initiatePayment() {
  payme.createPayment({
    amount: 999,
    currency: 'INR',
    customer: {
      name: document.getElementById('name').value,
      email: document.getElementById('email').value
    },
    onSuccess: function(response) {
      window.location.href = '/thank-you';
    }
  });
}
</script>
```

### 📱 Use Case 2: Freelancer/Service Provider

**Scenario:** आप freelancer हैं और clients से payment लेना चाहते हैं

**Setup:**
1. Subscription activate करें
2. UPI ID add करें (instant payments के लिए)
3. Payment link generate करें
4. Client को link भेजें
5. Client pay करे, आपको instant notification मिले

**Process:**
```
1. Dashboard → Create Payment Link
2. Amount: ₹5000
3. Description: "Website Design Project"
4. Generate Link
5. Copy link: https://payme-gateway.com/pay/PAY_123456
6. WhatsApp/Email पर client को भेजें
7. Client pay करे
8. आपके UPI में instant credit
```

### 🏪 Use Case 3: Small Business

**Scenario:** आपकी छोटी shop है और digital payments accept करना चाहते हैं

**Setup:**
1. Yearly subscription लें (₹500/year - 30% savings)
2. Bank account + UPI दोनों add करें
3. QR code generate करें
4. Shop में QR code display करें
5. Customers scan करके pay करें

**Benefits:**
- ✅ No POS machine needed
- ✅ No monthly rental
- ✅ Instant settlements
- ✅ All payment methods supported

### 🎓 Use Case 4: Educational Institute

**Scenario:** Coaching classes के लिए fees collection

**Setup:**
1. Subscription activate करें
2. Bank account add करें
3. Student-wise payment links generate करें
4. Parents को links भेजें
5. Automatic fee tracking

**Features:**
- Bulk payment link generation
- Student-wise reports
- Automatic receipts
- Monthly fee reminders

---

## 🔧 Troubleshooting

### ❓ Common Issues & Solutions

#### 1. **Subscription Activation Failed**

**Problem:** Activation code काम नहीं कर रहा

**Solutions:**
- ✅ Code सही से enter किया है? (16 characters)
- ✅ Code expire तो नहीं हो गया?
- ✅ Code पहले use तो नहीं हो चुका?
- ✅ Internet connection check करें
- ✅ Browser cache clear करें

#### 2. **Payment Not Received**

**Problem:** Customer ने payment किया लेकिन account में नहीं आया

**Solutions:**
- ✅ Bank/UPI details सही हैं?
- ✅ Account verified है?
- ✅ Settlement settings check करें
- ✅ Transaction status "Completed" है?
- ✅ 24 hours wait करें (bank processing time)

#### 3. **API Integration Not Working**

**Problem:** API calls fail हो रहे हैं

**Solutions:**
- ✅ API key सही है?
- ✅ API key expire तो नहीं हुई?
- ✅ Subscription active है?
- ✅ Request format सही है?
- ✅ Headers में API key include किया?

#### 4. **Reseller Can't Generate Codes**

**Problem:** Reseller को code generation error आ रहा है

**Solutions:**
- ✅ Admin ने plans assign किए हैं?
- ✅ Reseller account active है?
- ✅ Login credentials सही हैं?
- ✅ Browser cookies enabled हैं?

#### 5. **Bank Verification Failed**

**Problem:** Bank account verify नहीं हो रहा

**Solutions:**
- ✅ Account number सही है?
- ✅ IFSC code सही है?
- ✅ Account holder name exact match है?
- ✅ Account active है?
- ✅ Test deposit check करें

### 📞 Support Contact

**Technical Support:**
- Email: support@payme-gateway.com
- Phone: +91-XXXXXXXXXX
- WhatsApp: +91-XXXXXXXXXX
- Live Chat: Available on dashboard

**Support Hours:**
- Monday to Friday: 9 AM - 6 PM IST
- Saturday: 10 AM - 4 PM IST
- Sunday: Closed (Emergency support available)

**Response Time:**
- Critical Issues: Within 1 hour
- High Priority: Within 4 hours
- Normal: Within 24 hours

---

## 📝 Important Notes

### ⚠️ Do's and Don'ts

**✅ DO:**
- Regular password change करें
- 2FA enable रखें
- API keys को secure रखें
- Regular backups लें
- Transaction reports download करें
- Bank details को verify करें
- Test mode में पहले test करें

**❌ DON'T:**
- API keys share न करें
- Weak passwords use न करें
- Public computers पर login न करें
- Unverified bank accounts use न करें
- Expired subscriptions को ignore न करें
- Suspicious transactions को ignore न करें

### 💡 Best Practices

1. **Security:**
   - Strong passwords use करें
   - 2FA enable करें
   - Regular security audits करें

2. **Financial:**
   - Daily settlements enable करें
   - Regular reconciliation करें
   - Backup payment methods रखें

3. **Customer Service:**
   - Quick refunds process करें
   - Clear payment instructions दें
   - Receipt automatically send करें

4. **Technical:**
   - Webhook integration use करें
   - Error handling implement करें
   - Logs maintain करें

---

## 🎯 Quick Reference

### 📋 Important URLs

**Admin Panel:**
- Login: `/admin/login.html`
- Dashboard: `/admin/dashboard.html`
- Plans: `/admin/plan-management.html`
- Resellers: `/admin/reseller-management.html`
- Codes: `/admin/activation-codes.html`

**Reseller Panel:**
- Login: `/reseller/login.html`
- Dashboard: `/reseller/dashboard.html`

**User Panel:**
- Login: `/login.html`
- Register: `/register.html`
- Dashboard: `/dashboard.html`
- Activate: `/activate-subscription.html`
- Subscription: `/subscription.html`
- Self Payment: `/self-payment-settings.html`
- Transactions: `/transactions.html`

### 🔑 Default Credentials

**Admin:**
- Username: `admin`
- Password: `admin123`

**Test User:**
- Email: `test@example.com`
- Password: `test123`

**⚠️ Production में इन credentials को तुरंत बदलें!**

---

## 📚 Additional Resources

### 📖 Documentation Files

1. **README.md** - Quick start guide
2. **RESELLER_SYSTEM_DOCUMENTATION.md** - Technical documentation
3. **PROJECT_SUMMARY.txt** - Project overview
4. **COMPLETE_USER_ADMIN_GUIDE_HINDI.md** - यह file (Hindi guide)

### 🎥 Video Tutorials (Coming Soon)

- Admin panel setup
- Reseller onboarding
- Self payment gateway configuration
- API integration tutorial
- Troubleshooting common issues

### 💬 Community

- Forum: https://forum.payme-gateway.com
- Discord: https://discord.gg/payme-gateway
- Telegram: @payme_gateway_support

---

## ✅ Checklist

### 🚀 Getting Started Checklist

**For Admin:**
- [ ] Login to admin panel
- [ ] Change default password
- [ ] Create subscription plans
- [ ] Create first reseller
- [ ] Generate test activation code
- [ ] Test code activation
- [ ] Configure payment methods
- [ ] Set up bank account

**For Reseller:**
- [ ] Receive login credentials from admin
- [ ] Login to reseller portal
- [ ] Check assigned plans
- [ ] Generate first code
- [ ] Test code with customer
- [ ] Track commission
- [ ] Set up payout method

**For User:**
- [ ] Register account
- [ ] Verify email
- [ ] Purchase/receive activation code
- [ ] Activate subscription
- [ ] Configure self payment settings
- [ ] Add bank/UPI details
- [ ] Verify payment methods
- [ ] Create first payment link
- [ ] Test payment
- [ ] Check transaction

---

## 🎉 Conclusion

यह complete guide है PayMe 2D Gateway के सभी features के लिए। इस system को use करके आप:

✅ अपना खुद का payment gateway चला सकते हैं
✅ Resellers के through business scale कर सकते हैं
✅ Multiple payment methods support कर सकते हैं
✅ Direct settlements अपने account में ले सकते हैं
✅ Complete control अपने payments पर रख सकते हैं

किसी भी problem के लिए support team से contact करें।

**Happy Payment Processing! 💳🚀**

---

**Last Updated:** October 4, 2025
**Version:** 1.0.0
**Language:** Hindi (हिंदी)
