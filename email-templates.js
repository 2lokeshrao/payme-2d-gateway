// Email Templates for PayMe 2D Gateway

const emailTemplates = {
    // Welcome email with gateway credentials
    gatewayActivation: (data) => {
        const { name, email, plan, instanceId, apiKeys, loginUrl, username, password } = data;
        
        return {
            subject: `🎉 Your ${plan} Gateway Instance is Ready!`,
            html: `
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; }
        .credentials-box { background: white; border: 2px solid #667eea; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .credential-item { padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
        .credential-label { font-weight: 600; color: #666; }
        .credential-value { font-family: monospace; background: #f0f4ff; padding: 5px 10px; border-radius: 5px; display: inline-block; margin-top: 5px; }
        .button { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block; margin: 20px 0; }
        .steps { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .step { padding: 15px; margin: 10px 0; border-left: 4px solid #667eea; background: #f8f9fa; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to PayMe 2D Gateway!</h1>
            <p>Your ${plan} Plan is now active</p>
        </div>
        
        <div class="content">
            <h2>Hi ${name},</h2>
            <p>Congratulations! Your gateway instance has been successfully activated. You can now start managing merchants and processing payments.</p>
            
            <div class="credentials-box">
                <h3>🔐 Your Gateway Credentials</h3>
                
                <div class="credential-item">
                    <div class="credential-label">Instance ID:</div>
                    <div class="credential-value">${instanceId}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Dashboard URL:</div>
                    <div class="credential-value">${loginUrl}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Username:</div>
                    <div class="credential-value">${username}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Password:</div>
                    <div class="credential-value">${password}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Public API Key:</div>
                    <div class="credential-value">${apiKeys.public}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Secret API Key:</div>
                    <div class="credential-value">${apiKeys.secret}</div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="${loginUrl}" class="button">🚀 Access Your Dashboard</a>
            </div>
            
            <div class="steps">
                <h3>📋 Next Steps:</h3>
                
                <div class="step">
                    <strong>Step 1:</strong> Login to your dashboard using the credentials above
                </div>
                
                <div class="step">
                    <strong>Step 2:</strong> Add your first merchant from the Merchants section
                </div>
                
                <div class="step">
                    <strong>Step 3:</strong> Configure your gateway settings and payment methods
                </div>
                
                <div class="step">
                    <strong>Step 4:</strong> Integrate API keys into your application
                </div>
                
                <div class="step">
                    <strong>Step 5:</strong> Start processing payments and earning revenue!
                </div>
            </div>
            
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <strong>⚠️ Important:</strong> Please keep your credentials secure. Never share your Secret API Key with anyone.
            </div>
            
            <h3>💰 Your Revenue Model:</h3>
            <p>You can charge your merchants <strong>2% + ₹3 per transaction</strong>. Payments go directly to merchant accounts with T+2 settlement cycle.</p>
            
            <h3>📚 Resources:</h3>
            <ul>
                <li><a href="${loginUrl}/documentation.html">API Documentation</a></li>
                <li><a href="${loginUrl}/setup-guide.html">Setup Guide</a></li>
                <li><a href="mailto:support@payme2d.com">Contact Support</a></li>
            </ul>
        </div>
        
        <div class="footer">
            <p>Need help? Contact us at <a href="mailto:support@payme2d.com">support@payme2d.com</a></p>
            <p>© 2025 PayMe 2D Gateway. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
            `,
            text: `
Welcome to PayMe 2D Gateway!

Hi ${name},

Your ${plan} Plan gateway instance is now active!

CREDENTIALS:
Instance ID: ${instanceId}
Dashboard: ${loginUrl}
Username: ${username}
Password: ${password}
Public Key: ${apiKeys.public}
Secret Key: ${apiKeys.secret}

Login now: ${loginUrl}

Need help? Email: support@payme2d.com
            `
        };
    },

    // Payment verification email
    paymentVerification: (data) => {
        const { name, orderId, amount, plan } = data;
        
        return {
            subject: `Payment Received - Order #${orderId}`,
            html: `
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; }
        .order-box { background: white; border: 2px solid #10b981; border-radius: 10px; padding: 20px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Payment Received!</h1>
        </div>
        <div class="content">
            <h2>Hi ${name},</h2>
            <p>We have received your payment and it's being verified by our team.</p>
            
            <div class="order-box">
                <h3>Order Details:</h3>
                <p><strong>Order ID:</strong> ${orderId}</p>
                <p><strong>Plan:</strong> ${plan}</p>
                <p><strong>Amount:</strong> ₹${amount.toLocaleString('en-IN')}</p>
                <p><strong>Status:</strong> Verification Pending</p>
            </div>
            
            <p>Your gateway credentials will be sent within 24 hours after verification.</p>
            
            <p>Thank you for choosing PayMe 2D Gateway!</p>
        </div>
    </div>
</body>
</html>
            `
        };
    }
};

// Function to send email (to be integrated with backend)
function sendEmail(to, template) {
    console.log('Sending email to:', to);
    console.log('Subject:', template.subject);
    console.log('HTML:', template.html);
    
    // In production, integrate with email service like:
    // - SendGrid
    // - AWS SES
    // - Mailgun
    // - SMTP
    
    return {
        success: true,
        message: 'Email sent successfully'
    };
}

// Generate gateway credentials
function generateGatewayCredentials(purchaseData) {
    const instanceId = 'GW' + Date.now();
    const username = purchaseData.email;
    const password = 'PW' + Math.random().toString(36).substring(2, 10).toUpperCase();
    
    const apiKeys = {
        public: 'payme_pk_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15),
        secret: 'payme_sk_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
    };
    
    return {
        instanceId,
        username,
        password,
        apiKeys,
        loginUrl: 'https://stupid-lions-guess.lindy.site/client-dashboard.html',
        createdAt: new Date().toISOString()
    };
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { emailTemplates, sendEmail, generateGatewayCredentials };
}
