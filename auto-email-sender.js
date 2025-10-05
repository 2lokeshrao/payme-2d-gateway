/**
 * Automatic Email Sender for PayMe 2D Gateway
 * This script automatically sends emails when payment is verified
 */

// Email configuration (integrate with your email service)
const EMAIL_CONFIG = {
    service: 'smtp', // or 'sendgrid', 'ses', 'mailgun'
    from: 'noreply@payme2d.com',
    fromName: 'PayMe 2D Gateway',
    replyTo: 'support@payme2d.com'
};

// Email template for gateway activation
function createActivationEmail(customerData, credentials) {
    const { name, email, plan, business } = customerData;
    const { instanceId, username, password, apiKeys, loginUrl } = credentials;
    
    return {
        to: email,
        subject: `🎉 Your ${plan} Gateway Instance is Ready!`,
        html: `
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 40px 30px; }
        .credentials-box { background: #f8f9fa; border: 2px solid #667eea; border-radius: 10px; padding: 25px; margin: 25px 0; }
        .credential-item { padding: 12px 0; border-bottom: 1px solid #e0e0e0; }
        .credential-item:last-child { border-bottom: none; }
        .credential-label { font-weight: 600; color: #666; font-size: 14px; margin-bottom: 5px; }
        .credential-value { font-family: 'Courier New', monospace; background: white; padding: 10px 15px; border-radius: 6px; display: block; font-size: 14px; color: #667eea; word-break: break-all; }
        .button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; margin: 20px 0; font-weight: 600; }
        .steps { background: #f8f9fa; padding: 25px; border-radius: 10px; margin: 25px 0; }
        .step { padding: 15px; margin: 12px 0; border-left: 4px solid #667eea; background: white; border-radius: 5px; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 5px; margin: 25px 0; }
        .footer { background: #f8f9fa; padding: 30px; text-align: center; color: #666; font-size: 14px; }
        .footer a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to PayMe 2D Gateway!</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">Your ${plan} Plan is now active</p>
        </div>
        
        <div class="content">
            <h2 style="color: #333; margin-bottom: 15px;">Hi ${name},</h2>
            <p style="font-size: 16px; line-height: 1.8;">
                Congratulations! 🎊 Your payment has been verified and your gateway instance has been successfully activated. 
                You can now start managing merchants and processing payments.
            </p>
            
            <div class="credentials-box">
                <h3 style="margin: 0 0 20px 0; color: #333;">🔐 Your Gateway Credentials</h3>
                
                <div class="credential-item">
                    <div class="credential-label">Instance ID</div>
                    <div class="credential-value">${instanceId}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Dashboard URL</div>
                    <div class="credential-value">${loginUrl}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Username (Email)</div>
                    <div class="credential-value">${username}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Password</div>
                    <div class="credential-value">${password}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Public API Key</div>
                    <div class="credential-value">${apiKeys.public}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Secret API Key</div>
                    <div class="credential-value">${apiKeys.secret}</div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="${loginUrl}" class="button">🚀 Access Your Dashboard Now</a>
            </div>
            
            <div class="steps">
                <h3 style="margin: 0 0 20px 0; color: #333;">📋 Quick Start Guide</h3>
                
                <div class="step">
                    <strong>Step 1: Login</strong><br>
                    Visit your dashboard and login using the credentials above
                </div>
                
                <div class="step">
                    <strong>Step 2: Add Merchants</strong><br>
                    Go to Merchants section and add your first sub-merchant
                </div>
                
                <div class="step">
                    <strong>Step 3: Configure Settings</strong><br>
                    Set up your gateway settings, payment methods, and webhooks
                </div>
                
                <div class="step">
                    <strong>Step 4: Integrate API</strong><br>
                    Use the API keys to integrate payment gateway in your application
                </div>
                
                <div class="step">
                    <strong>Step 5: Start Earning</strong><br>
                    Begin processing payments and earning revenue from transactions!
                </div>
            </div>
            
            <div class="warning">
                <strong>⚠️ Important Security Notice:</strong><br>
                • Keep your credentials secure and never share them<br>
                • Never expose your Secret API Key in client-side code<br>
                • Change your password after first login<br>
                • Enable 2FA for additional security
            </div>
            
            <h3 style="color: #333; margin: 30px 0 15px 0;">💰 Your Revenue Model</h3>
            <p style="font-size: 15px; line-height: 1.8;">
                You can charge your merchants <strong>2% + ₹3 per transaction</strong>. 
                Payments go directly to merchant accounts with T+2 settlement cycle. 
                You earn the gateway fees automatically.
            </p>
            
            <h3 style="color: #333; margin: 30px 0 15px 0;">📚 Helpful Resources</h3>
            <ul style="line-height: 2;">
                <li><a href="${loginUrl}/documentation.html" style="color: #667eea;">API Documentation</a></li>
                <li><a href="${loginUrl}/setup-guide.html" style="color: #667eea;">Complete Setup Guide</a></li>
                <li><a href="mailto:support@payme2d.com" style="color: #667eea;">Contact Support Team</a></li>
            </ul>
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 10px 0;">Need help? We're here for you!</p>
            <p style="margin: 0 0 20px 0;">
                📧 <a href="mailto:support@payme2d.com">support@payme2d.com</a><br>
                📱 WhatsApp: +91 XXXXX XXXXX
            </p>
            <p style="margin: 20px 0 0 0; color: #999; font-size: 12px;">
                © 2025 PayMe 2D Gateway. All rights reserved.<br>
                This is an automated email. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
        `,
        text: `
Welcome to PayMe 2D Gateway!

Hi ${name},

Your ${plan} Plan gateway instance is now active!

GATEWAY CREDENTIALS:
====================
Instance ID: ${instanceId}
Dashboard: ${loginUrl}
Username: ${username}
Password: ${password}
Public Key: ${apiKeys.public}
Secret Key: ${apiKeys.secret}

QUICK START:
1. Login to your dashboard
2. Add your first merchant
3. Configure gateway settings
4. Integrate API keys
5. Start processing payments

REVENUE MODEL:
Charge merchants 2% + ₹3 per transaction
Payments go directly to merchant accounts (T+2 settlement)

IMPORTANT: Keep your credentials secure. Never share your Secret API Key.

Need help? Email: support@payme2d.com

© 2025 PayMe 2D Gateway
        `
    };
}

// Function to send email via SMTP (example)
async function sendEmailViaSMTP(emailData) {
    // This is a placeholder - integrate with your actual email service
    console.log('Sending email via SMTP...');
    console.log('To:', emailData.to);
    console.log('Subject:', emailData.subject);
    
    // Example integration with popular email services:
    
    // 1. SendGrid
    // const sgMail = require('@sendgrid/mail');
    // sgMail.setApiKey(process.env.SENDGRID_API_KEY);
    // await sgMail.send(emailData);
    
    // 2. AWS SES
    // const AWS = require('aws-sdk');
    // const ses = new AWS.SES();
    // await ses.sendEmail({...}).promise();
    
    // 3. Nodemailer (SMTP)
    // const nodemailer = require('nodemailer');
    // const transporter = nodemailer.createTransport({...});
    // await transporter.sendMail(emailData);
    
    return {
        success: true,
        messageId: 'msg_' + Date.now(),
        message: 'Email sent successfully'
    };
}

// Main function to send activation email
async function sendGatewayActivationEmail(customerData, credentials) {
    try {
        const emailData = createActivationEmail(customerData, credentials);
        const result = await sendEmailViaSMTP(emailData);
        
        console.log('✅ Activation email sent successfully');
        console.log('Message ID:', result.messageId);
        
        return result;
    } catch (error) {
        console.error('❌ Failed to send email:', error);
        throw error;
    }
}

// Export functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        sendGatewayActivationEmail,
        createActivationEmail,
        sendEmailViaSMTP
    };
}
