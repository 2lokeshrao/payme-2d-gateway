# 💳 PayMe 2D Gateway - Real Payment Gateway Integration Guide

## 🎯 Overview

PayMe 2D Gateway ab **real card payments** process kar sakta hai using multiple payment gateway providers:

1. **Razorpay** (Recommended for India)
2. **Stripe** (International)
3. **PayU** (India)
4. **Internal 2D Gateway** (Testing)

---

## 🔧 Setup Instructions

### Option 1: Razorpay Integration (Recommended)

#### Step 1: Create Razorpay Account
1. Visit https://razorpay.com
2. Sign up for merchant account
3. Complete KYC verification
4. Get API keys from Dashboard

#### Step 2: Get API Credentials
```
Test Mode:
- Key ID: rzp_test_xxxxxxxxxxxxxxxx
- Key Secret: xxxxxxxxxxxxxxxxxxxxxxxx

Live Mode:
- Key ID: rzp_live_xxxxxxxxxxxxxxxx
- Key Secret: xxxxxxxxxxxxxxxxxxxxxxxx
```

#### Step 3: Configure in PayMe 2D
Edit `config.php`:
```php
// Razorpay Configuration
define('PAYMENT_GATEWAY', 'razorpay');
define('RAZORPAY_KEY_ID', 'rzp_test_xxxxxxxxxxxxxxxx');
define('RAZORPAY_KEY_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxx');
```

Or set environment variables:
```bash
export RAZORPAY_KEY_ID="rzp_test_xxxxxxxxxxxxxxxx"
export RAZORPAY_KEY_SECRET="xxxxxxxxxxxxxxxxxxxxxxxx"
```

#### Step 4: Test Card Numbers
```
Success: 4111 1111 1111 1111
Declined: 4000 0000 0000 0002
CVV: Any 3 digits
Expiry: Any future date
```

---

### Option 2: Stripe Integration

#### Step 1: Create Stripe Account
1. Visit https://stripe.com
2. Sign up for account
3. Complete verification
4. Get API keys

#### Step 2: Get API Credentials
```
Test Mode:
- Publishable Key: pk_test_xxxxxxxxxxxxxxxx
- Secret Key: sk_test_xxxxxxxxxxxxxxxx

Live Mode:
- Publishable Key: pk_live_xxxxxxxxxxxxxxxx
- Secret Key: sk_live_xxxxxxxxxxxxxxxx
```

#### Step 3: Configure in PayMe 2D
```php
// Stripe Configuration
define('PAYMENT_GATEWAY', 'stripe');
define('STRIPE_KEY', 'sk_test_xxxxxxxxxxxxxxxx');
```

#### Step 4: Test Card Numbers
```
Success: 4242 4242 4242 4242
Declined: 4000 0000 0000 0002
CVV: Any 3 digits
Expiry: Any future date
```

---

### Option 3: PayU Integration

#### Step 1: Create PayU Account
1. Visit https://payu.in
2. Sign up for merchant account
3. Complete KYC
4. Get credentials

#### Step 2: Get API Credentials
```
Test Mode:
- Merchant Key: xxxxxxxx
- Merchant Salt: xxxxxxxx

Live Mode:
- Merchant Key: xxxxxxxx
- Merchant Salt: xxxxxxxx
```

#### Step 3: Configure in PayMe 2D
```php
// PayU Configuration
define('PAYMENT_GATEWAY', 'payu');
define('PAYU_MERCHANT_KEY', 'xxxxxxxx');
define('PAYU_MERCHANT_SALT', 'xxxxxxxx');
```

---

### Option 4: Internal 2D Gateway (Testing Only)

Yeh option testing ke liye hai. Real payments process nahi hoti.

#### Test Cards
```
Success Cards:
- 4111 1111 1111 1111 (Visa)
- 5555 5555 5554 444 (Mastercard)
- 3782 822463 10005 (Amex)

Declined Card:
- 4000 0000 0000 0002
```

---

## 🔐 Security Configuration

### 1. SSL Certificate (Required)
```bash
# Install Let's Encrypt SSL
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### 2. Environment Variables
Create `.env` file:
```
# Database
DB_HOST=localhost
DB_NAME=payme_gateway
DB_USER=payme_user
DB_PASS=secure_password

# Payment Gateway
PAYMENT_GATEWAY=razorpay
RAZORPAY_KEY_ID=rzp_test_xxxxxxxx
RAZORPAY_KEY_SECRET=xxxxxxxxxxxxxxxx

# Webhook Secret
WEBHOOK_SECRET=your_webhook_secret_key

# Environment
APP_ENV=production
APP_DEBUG=false
```

### 3. PCI Compliance
- Never store CVV
- Encrypt card data
- Use HTTPS only
- Implement rate limiting
- Log all transactions

---

## 🧪 Testing Payment Flow

### 1. Test Card Payment
```bash
curl -X POST https://yourdomain.com/api/process-payment.php \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_id": "TXN123456789",
    "payment_method": "card",
    "payment_details": {
      "card_number": "4111111111111111",
      "card_name": "Test User",
      "expiry_month": "12",
      "expiry_year": "2025",
      "cvv": "123"
    }
  }'
```

### 2. Test UPI Payment
```bash
curl -X POST https://yourdomain.com/api/process-payment.php \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_id": "TXN123456789",
    "payment_method": "upi",
    "payment_details": {
      "upi_id": "test@paytm"
    }
  }'
```

---

## 📊 Payment Flow

### 1. Customer Initiates Payment
```
Customer → Checkout Page → Select Payment Method
```

### 2. Payment Processing
```
Frontend → API → Payment Gateway → Bank
```

### 3. Response Handling
```
Bank → Payment Gateway → API → Database → Webhook → Merchant
```

### 4. Settlement
```
T+2 Days → Bank Account
```

---

## 🔔 Webhook Configuration

### 1. Setup Webhook URL
```php
// In merchant dashboard
Webhook URL: https://yoursite.com/webhook
Events: payment.success, payment.failed, refund.completed
```

### 2. Verify Webhook Signature
```php
function verifyWebhook($payload, $signature, $secret) {
    $computed = hash_hmac('sha256', json_encode($payload), $secret);
    return hash_equals($computed, $signature);
}
```

### 3. Handle Webhook Events
```php
$event = $_POST['event'];

switch($event) {
    case 'payment.success':
        // Update order status
        // Send confirmation email
        break;
    case 'payment.failed':
        // Notify customer
        // Retry logic
        break;
}
```

---

## 💰 Fee Structure

### Razorpay
- Cards: 2% + ₹0
- UPI: 0% (limited time)
- Net Banking: ₹10 per transaction
- Wallets: 2%

### Stripe
- Cards: 2.9% + $0.30
- International: 3.9% + $0.30

### PayU
- Cards: 2% + ₹3
- UPI: 0.5%
- Net Banking: ₹10

### PayMe 2D Markup
- Add 0.5% on top of gateway fees
- Minimum: ₹3 per transaction

---

## 🚀 Go Live Checklist

### Pre-Production
- [ ] KYC completed with payment gateway
- [ ] Test transactions successful
- [ ] Webhook delivery working
- [ ] SSL certificate installed
- [ ] Environment variables set
- [ ] Database backed up
- [ ] Error logging configured

### Production
- [ ] Switch to live API keys
- [ ] Update webhook URLs
- [ ] Enable monitoring
- [ ] Set up alerts
- [ ] Test with small amount
- [ ] Monitor first 24 hours

### Post-Production
- [ ] Daily reconciliation
- [ ] Settlement verification
- [ ] Customer support ready
- [ ] Refund process tested
- [ ] Dispute handling setup

---

## 🔍 Monitoring & Logs

### 1. Transaction Logs
```sql
SELECT * FROM transactions 
WHERE created_at >= NOW() - INTERVAL 1 DAY
ORDER BY created_at DESC;
```

### 2. Success Rate
```sql
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
    (SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as success_rate
FROM transactions
WHERE created_at >= NOW() - INTERVAL 1 DAY;
```

### 3. Revenue Tracking
```sql
SELECT 
    DATE(created_at) as date,
    SUM(amount) as revenue,
    COUNT(*) as transactions
FROM transactions
WHERE status = 'success'
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

---

## 🆘 Troubleshooting

### Payment Failing
1. Check API credentials
2. Verify SSL certificate
3. Check gateway status
4. Review error logs
5. Test with test cards

### Webhook Not Receiving
1. Verify webhook URL
2. Check firewall rules
3. Test webhook manually
4. Review webhook logs
5. Verify signature

### Settlement Delayed
1. Check KYC status
2. Verify bank details
3. Contact gateway support
4. Review settlement cycle
5. Check for holds

---

## 📞 Support

### Razorpay Support
- Email: support@razorpay.com
- Phone: 1800-120-020-020
- Docs: https://razorpay.com/docs

### Stripe Support
- Email: support@stripe.com
- Docs: https://stripe.com/docs

### PayU Support
- Email: support@payu.in
- Phone: 1800-103-0033
- Docs: https://docs.payu.in

### PayMe 2D Support
- Email: support@payme2d.com
- GitHub: https://github.com/2lokeshrao/payme-2d-gateway/issues

---

## 🎉 Summary

**PayMe 2D Gateway ab real card payments process kar sakta hai!**

✅ Multiple gateway support (Razorpay, Stripe, PayU)  
✅ Real card processing  
✅ UPI payments  
✅ Net banking  
✅ Wallets  
✅ Cryptocurrency  
✅ EMI options  
✅ Webhook notifications  
✅ Automated settlements  
✅ Complete security  

**Ready for production deployment!** 🚀

---

**Made with ❤️ by PayMe 2D Team**
