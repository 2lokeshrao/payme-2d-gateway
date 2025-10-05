// Automatic Payment Verification System
// This simulates a real payment gateway verification API

class PaymentVerificationAPI {
    constructor() {
        this.verificationEndpoint = 'https://api.payme2d.com/verify'; // Your actual API endpoint
    }

    // Simulate UPI payment verification
    async verifyUPIPayment(orderId, upiTransactionId, amount) {
        // In production, this will call actual UPI verification API
        return new Promise((resolve) => {
            setTimeout(() => {
                // Simulate 80% success rate
                const isSuccess = Math.random() > 0.2;
                
                resolve({
                    success: isSuccess,
                    orderId: orderId,
                    transactionId: upiTransactionId || 'TXN' + Date.now(),
                    amount: amount,
                    status: isSuccess ? 'SUCCESS' : 'FAILED',
                    message: isSuccess ? 'Payment verified successfully' : 'Payment verification failed',
                    timestamp: new Date().toISOString(),
                    paymentMethod: 'UPI'
                });
            }, 3000); // 3 second verification delay
        });
    }

    // Verify Card payment
    async verifyCardPayment(orderId, cardLast4, amount) {
        return new Promise((resolve) => {
            setTimeout(() => {
                const isSuccess = Math.random() > 0.15;
                
                resolve({
                    success: isSuccess,
                    orderId: orderId,
                    transactionId: 'CARD' + Date.now(),
                    amount: amount,
                    status: isSuccess ? 'SUCCESS' : 'FAILED',
                    message: isSuccess ? 'Card payment verified' : 'Card payment declined',
                    timestamp: new Date().toISOString(),
                    paymentMethod: 'CARD',
                    cardLast4: cardLast4
                });
            }, 2500);
        });
    }

    // Verify Net Banking payment
    async verifyNetBankingPayment(orderId, bankName, amount) {
        return new Promise((resolve) => {
            setTimeout(() => {
                const isSuccess = Math.random() > 0.1;
                
                resolve({
                    success: isSuccess,
                    orderId: orderId,
                    transactionId: 'NB' + Date.now(),
                    amount: amount,
                    status: isSuccess ? 'SUCCESS' : 'FAILED',
                    message: isSuccess ? 'Net Banking payment verified' : 'Net Banking payment failed',
                    timestamp: new Date().toISOString(),
                    paymentMethod: 'NET_BANKING',
                    bankName: bankName
                });
            }, 4000);
        });
    }

    // Verify Crypto payment
    async verifyCryptoPayment(orderId, cryptoCurrency, walletAddress, amount) {
        return new Promise((resolve) => {
            setTimeout(() => {
                const isSuccess = Math.random() > 0.25;
                
                resolve({
                    success: isSuccess,
                    orderId: orderId,
                    transactionId: 'CRYPTO' + Date.now(),
                    amount: amount,
                    status: isSuccess ? 'SUCCESS' : 'PENDING',
                    message: isSuccess ? 'Crypto payment confirmed' : 'Waiting for blockchain confirmation',
                    timestamp: new Date().toISOString(),
                    paymentMethod: 'CRYPTO',
                    cryptoCurrency: cryptoCurrency,
                    confirmations: isSuccess ? 6 : 0
                });
            }, 5000);
        });
    }

    // Send automatic email after successful payment
    async sendSuccessEmail(paymentData) {
        // Generate credentials
        const credentials = {
            instanceId: 'GW' + Date.now(),
            username: paymentData.email,
            password: this.generatePassword(),
            publicKey: 'payme_pk_' + this.generateRandomString(32),
            secretKey: 'payme_sk_' + this.generateRandomString(32),
            dashboardUrl: 'https://stupid-lions-guess.lindy.site/client-dashboard.html',
            plan: paymentData.plan
        };

        // Email template
        const emailContent = {
            to: paymentData.email,
            subject: `🎉 Your ${paymentData.plan} Gateway Instance is Ready!`,
            html: this.generateEmailHTML(credentials, paymentData)
        };

        console.log('📧 Sending email to:', paymentData.email);
        console.log('Credentials:', credentials);

        // In production, integrate with SendGrid/AWS SES/Mailgun
        // await this.sendEmailViaSendGrid(emailContent);

        return credentials;
    }

    generatePassword() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    generateRandomString(length) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    generateEmailHTML(credentials, paymentData) {
        return `
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .credentials { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
        .credential-item { margin: 15px 0; }
        .credential-label { font-weight: bold; color: #667eea; }
        .credential-value { background: #f0f4ff; padding: 8px 12px; border-radius: 5px; font-family: monospace; margin-top: 5px; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Payment Successful!</h1>
            <p>Your ${credentials.plan} Gateway Instance is Ready</p>
        </div>
        <div class="content">
            <p>Dear ${paymentData.fullName},</p>
            <p>Thank you for your payment of <strong>₹${paymentData.price.toLocaleString('en-IN')}</strong>. Your gateway instance has been activated!</p>
            
            <div class="credentials">
                <h3>🔐 Your Login Credentials</h3>
                <div class="credential-item">
                    <div class="credential-label">Instance ID:</div>
                    <div class="credential-value">${credentials.instanceId}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">Username:</div>
                    <div class="credential-value">${credentials.username}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">Password:</div>
                    <div class="credential-value">${credentials.password}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">Public API Key:</div>
                    <div class="credential-value">${credentials.publicKey}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">Secret API Key:</div>
                    <div class="credential-value">${credentials.secretKey}</div>
                </div>
            </div>

            <a href="${credentials.dashboardUrl}" class="button">Access Your Dashboard</a>

            <div class="warning">
                <strong>⚠️ Security Notice:</strong> Keep your credentials safe. Never share your Secret API Key with anyone.
            </div>

            <p>Need help? Contact us at support@payme2d.com</p>
        </div>
    </div>
</body>
</html>
        `;
    }
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PaymentVerificationAPI;
}
