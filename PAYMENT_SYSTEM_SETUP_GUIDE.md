# 💳 Payment System Setup Guide - PayMe 2D Gateway

## 📚 Complete Guide for Setting Up Payment Collection

This guide explains how to configure the payment system so that when customers purchase activation codes, payments come directly to your bank account/UPI/crypto wallet, and customers automatically receive activation codes via email.

---

## 🎯 Overview

### What This System Does:

1. **Customer purchases activation code** from your website
2. **Payment goes directly to YOUR account** (Bank/UPI/Crypto)
3. **System automatically generates activation code**
4. **Customer receives code via email** with activation instructions
5. **Admin receives notification** of the purchase

### No Middleman, No Commission - 100% Direct Payment!

---

## 🚀 Quick Setup (5 Steps)

### Step 1: Admin Login
- Go to: `https://your-domain.com/admin/login.html`
- Login with admin credentials
- Username: `admin`
- Password: `admin123` (change this immediately!)

### Step 2: Configure Payment Details
- Go to: **Payment Configuration** from admin menu
- Or directly: `https://your-domain.com/admin/payment-configuration.html`

### Step 3: Add Your Payment Details
Configure at least one payment method:
- ✅ Bank Account (for NEFT/RTGS/IMPS)
- ✅ UPI ID (for instant payments)
- ✅ Crypto Wallets (optional, for global payments)

### Step 4: Configure Email Settings
- Add your SMTP details
- Test email functionality
- Ensure emails are being sent

### Step 5: Test the System
- Go to purchase page: `https://your-domain.com/purchase-code.html`
- Make a test purchase
- Verify code is generated and email is sent

---

## 🏦 Bank Account Configuration

### Required Information:

1. **Bank Name**: Select from dropdown (HDFC, ICICI, SBI, Axis, etc.)
2. **Account Type**: Savings or Current
3. **Account Holder Name**: Exact name as per bank records
4. **Account Number**: Your bank account number
5. **IFSC Code**: Bank branch IFSC code

### Example:
```
Bank Name: HDFC Bank
Account Type: Savings
Account Holder Name: Rajesh Kumar
Account Number: 50100123456789
IFSC Code: HDFC0001234
```

### How Customers Will Pay:
- Customers will see your bank details on purchase page
- They can transfer money via:
  - Net Banking
  - NEFT/RTGS/IMPS
  - Bank app transfer
- After transfer, they enter transaction reference number
- System verifies and generates code

---

## 📱 UPI Payment Configuration

### Required Information:

1. **UPI ID**: Your UPI ID (e.g., yourname@okhdfc)
2. **UPI Name**: Name registered with UPI
3. **UPI Provider**: Google Pay, PhonePe, Paytm, BHIM, etc.

### Example:
```
UPI ID: rajesh@okhdfc
UPI Name: Rajesh Kumar
UPI Provider: Google Pay
```

### How Customers Will Pay:
- Customers will see your UPI ID
- System automatically generates UPI QR code
- Customers can:
  - Scan QR code with any UPI app
  - Copy UPI ID and pay manually
  - Payment is instant (24/7)
- After payment, they enter UPI transaction ID
- System verifies and generates code

### UPI ID Formats:
- **Google Pay**: `yourname@okaxis`, `yourname@okhdfc`, `yourname@okicici`
- **PhonePe**: `yourname@ybl`
- **Paytm**: `yourname@paytm`
- **BHIM**: `yourname@upi`

---

## 💎 Cryptocurrency Configuration (Optional)

### Supported Cryptocurrencies:

1. **Bitcoin (BTC)**
2. **Ethereum (ETH)**
3. **Tether (USDT)** - ERC-20, TRC-20, BEP-20
4. **Binance Coin (BNB)** - BSC

### Required Information:

For each crypto you want to accept, add wallet address:

```
Bitcoin Wallet: 1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa
Ethereum Wallet: 0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb
USDT Wallet: 0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb (specify network)
BNB Wallet: 0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb
```

### ⚠️ Important:
- Double-check wallet addresses
- Wrong address = lost funds (irreversible)
- For USDT, specify network (ERC-20/TRC-20/BEP-20)
- Test with small amount first

### How Customers Will Pay:
- Customers select cryptocurrency
- System shows your wallet address
- System generates QR code for easy scanning
- Customer sends crypto to your wallet
- Customer enters transaction hash
- System verifies and generates code

---

## 📧 Email Configuration

### Why Email Configuration is Important:
- Activation codes are sent via email
- Customers need codes to activate subscription
- Admin receives purchase notifications

### Required Information:

#### For Gmail (Recommended):

1. **SMTP Host**: `smtp.gmail.com`
2. **SMTP Port**: `587`
3. **SMTP Username**: Your Gmail address
4. **SMTP Password**: Gmail App Password (NOT your regular password)
5. **From Email**: `noreply@payme-gateway.com` (or your domain)
6. **From Name**: `PayMe 2D Gateway`
7. **Admin Notification Email**: Your email for notifications

### How to Get Gmail App Password:

1. Go to Google Account Settings
2. Security → 2-Step Verification (enable if not enabled)
3. App Passwords
4. Select "Mail" and "Other (Custom name)"
5. Enter "PayMe Gateway"
6. Copy the 16-character password
7. Use this password in SMTP settings

### Example Configuration:
```
SMTP Host: smtp.gmail.com
SMTP Port: 587
SMTP Username: your-email@gmail.com
SMTP Password: abcd efgh ijkl mnop (16-char app password)
From Email: noreply@payme-gateway.com
From Name: PayMe 2D Gateway
Admin Notification Email: admin@payme-gateway.com
```

### Test Email:
- After configuration, use "Send Test Email" button
- Enter your email address
- Click "Send Test Email"
- Check inbox (and spam folder)
- If received, configuration is correct!

---

## 🎨 Customer Purchase Flow

### Step-by-Step Process:

1. **Customer visits**: `https://your-domain.com/purchase-code.html`

2. **Selects Plan**:
   - Monthly: ₹60
   - Quarterly: ₹150
   - Yearly: ₹500
   - Lifetime: ₹2,999

3. **Enters Details**:
   - Full Name
   - Email Address
   - Phone Number

4. **Selects Payment Method**:
   - UPI (instant)
   - Bank Transfer (NEFT/RTGS)
   - Cryptocurrency

5. **Makes Payment**:
   - Sees your payment details
   - Makes payment to your account
   - Gets transaction ID/reference number

6. **Enters Transaction ID**:
   - Enters UPI transaction ID
   - Or bank reference number
   - Or crypto transaction hash

7. **Clicks "Verify Payment"**:
   - System generates activation code
   - Format: `PM2D-XXXX-XXXX-XXXX`
   - Code saved to database

8. **Receives Email**:
   - Professional email with activation code
   - Plan details
   - Activation instructions
   - Direct link to activate

9. **Activates Subscription**:
   - Goes to activation page
   - Enters code
   - Subscription activated!

---

## 📊 Admin Notifications

### What Admin Receives:

When a customer purchases a code, admin receives email with:

- Customer name and email
- Plan purchased
- Amount paid
- Payment method used
- Transaction ID
- Date and time

### Example Admin Notification:

```
Subject: New Code Purchase - PayMe 2D Gateway

New Code Purchase Notification

Customer Details:
- Name: Rajesh Kumar
- Email: rajesh@example.com

Purchase Details:
- Plan: Yearly Plan
- Amount: ₹500
- Payment Method: UPI
- Transaction ID: 123456789012
- Date: 2025-10-04 10:30:00
```

---

## 🔐 Security Features

### Payment Security:
- ✅ No payment gateway integration needed
- ✅ Direct bank-to-bank transfer
- ✅ No card details stored
- ✅ Transaction IDs verified
- ✅ Secure database storage

### Code Security:
- ✅ Unique 16-character codes
- ✅ One-time use only
- ✅ Expiry date tracking
- ✅ Cannot be reused
- ✅ Tied to customer email

### Email Security:
- ✅ SMTP encryption
- ✅ App passwords (not regular passwords)
- ✅ Professional email templates
- ✅ No sensitive data in emails

---

## 🧪 Testing the System

### Test Checklist:

#### 1. Test Bank Transfer:
- [ ] Bank details display correctly
- [ ] Account number is correct
- [ ] IFSC code is correct
- [ ] Copy buttons work

#### 2. Test UPI Payment:
- [ ] UPI ID displays correctly
- [ ] QR code generates properly
- [ ] QR code scans successfully
- [ ] Payment goes to correct UPI ID

#### 3. Test Crypto Payment:
- [ ] Wallet addresses display correctly
- [ ] QR codes generate for wallets
- [ ] Addresses are correct (double-check!)

#### 4. Test Email Delivery:
- [ ] Test email sends successfully
- [ ] Email arrives in inbox (not spam)
- [ ] Email formatting looks good
- [ ] Links in email work

#### 5. Test Code Generation:
- [ ] Make test purchase
- [ ] Enter transaction ID
- [ ] Code generates successfully
- [ ] Code format is correct (PM2D-XXXX-XXXX-XXXX)
- [ ] Email with code is sent
- [ ] Admin notification is sent

#### 6. Test Code Activation:
- [ ] Go to activation page
- [ ] Enter generated code
- [ ] Code activates successfully
- [ ] Subscription shows correct plan
- [ ] Expiry date is correct

---

## 🎯 Best Practices

### For Bank Transfers:
1. Use a dedicated business account
2. Enable SMS/email alerts for transactions
3. Check transactions daily
4. Verify transaction IDs manually if needed

### For UPI Payments:
1. Use a business UPI ID if possible
2. Enable transaction notifications
3. Keep UPI app updated
4. Monitor payments in real-time

### For Crypto Payments:
1. Use hardware wallet for large amounts
2. Verify wallet addresses multiple times
3. Test with small amounts first
4. Keep private keys secure
5. Monitor blockchain transactions

### For Email:
1. Use professional email address
2. Keep SMTP credentials secure
3. Monitor email delivery rates
4. Check spam folder regularly
5. Update email templates as needed

---

## 🔧 Troubleshooting

### Issue: Emails Not Sending

**Solutions:**
- Check SMTP credentials
- Verify Gmail App Password
- Enable "Less secure app access" (if using old Gmail)
- Check spam folder
- Try different SMTP port (465 instead of 587)
- Verify internet connection

### Issue: Payment Details Not Showing

**Solutions:**
- Check if payment config is saved
- Verify database connection
- Check browser console for errors
- Clear browser cache
- Reload the page

### Issue: Code Not Generated

**Solutions:**
- Check database connection
- Verify plan exists in database
- Check API endpoint is working
- Look at browser console errors
- Check server logs

### Issue: Customer Not Receiving Email

**Solutions:**
- Check customer email address is correct
- Verify email configuration
- Send test email first
- Check spam folder
- Try different email provider

---

## 📞 Support

### Need Help?

If you face any issues:

1. **Check Documentation**: Read this guide thoroughly
2. **Check Logs**: Look at browser console and server logs
3. **Test Configuration**: Use test email feature
4. **Verify Details**: Double-check all payment details
5. **Contact Support**: Email support@payme-gateway.com

---

## ✅ Final Checklist

Before going live, ensure:

- [ ] Admin payment details configured
- [ ] Bank account details verified
- [ ] UPI ID tested and working
- [ ] Email configuration tested
- [ ] Test purchase completed successfully
- [ ] Test code activation successful
- [ ] Admin notifications working
- [ ] Customer emails arriving
- [ ] All payment methods tested
- [ ] Security measures in place
- [ ] Backup of configuration taken
- [ ] Documentation reviewed

---

## 🎉 You're Ready!

Once all the above is configured and tested, your payment system is ready to accept real customers!

**Share your purchase page**: `https://your-domain.com/purchase-code.html`

Customers can now:
- Purchase activation codes
- Pay directly to your account
- Receive codes instantly via email
- Activate and start using the gateway

**No middleman, no commission - 100% of the payment is yours!**

---

**Last Updated**: October 4, 2025
**Version**: 1.0.0
**System**: PayMe 2D Gateway
