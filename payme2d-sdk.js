/**
 * PayMe 2D Gateway - JavaScript SDK
 * Version: 1.0.0
 * 
 * Usage:
 * const payme = new PayMe2D('YOUR_API_KEY');
 * payme.checkout({ amount: 1000, currency: 'INR', ... });
 */

class PayMe2D {
    constructor(apiKey, options = {}) {
        this.apiKey = apiKey;
        this.baseUrl = options.baseUrl || 'https://api.payme2d.com/v1';
        this.checkoutUrl = options.checkoutUrl || 'https://checkout.payme2d.com';
        this.mode = options.mode || 'live'; // 'test' or 'live'
    }

    /**
     * Create a payment and open checkout
     */
    async checkout(options) {
        const required = ['amount', 'currency', 'customer_email'];
        for (const field of required) {
            if (!options[field]) {
                throw new Error(`Missing required field: ${field}`);
            }
        }

        try {
            // Create payment
            const payment = await this.createPayment(options);
            
            // Open checkout modal
            this.openCheckout(payment, options);
            
            return payment;
        } catch (error) {
            if (options.onError) {
                options.onError(error);
            }
            throw error;
        }
    }

    /**
     * Create a payment via API
     */
    async createPayment(data) {
        const response = await fetch(`${this.baseUrl}/payments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.apiKey}`
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Payment creation failed');
        }

        return await response.json();
    }

    /**
     * Open checkout modal
     */
    openCheckout(payment, options) {
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.id = 'payme2d-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
        `;

        // Create iframe
        const iframe = document.createElement('iframe');
        iframe.src = `${payment.checkout_url}`;
        iframe.style.cssText = `
            width: 90%;
            max-width: 500px;
            height: 90%;
            max-height: 700px;
            border: none;
            border-radius: 12px;
            background: white;
        `;

        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        `;
        closeBtn.onclick = () => {
            document.body.removeChild(overlay);
            if (options.onClose) {
                options.onClose();
            }
        };

        overlay.appendChild(iframe);
        overlay.appendChild(closeBtn);
        document.body.appendChild(overlay);

        // Listen for payment completion
        window.addEventListener('message', (event) => {
            if (event.data.type === 'payme2d_payment_success') {
                document.body.removeChild(overlay);
                if (options.onSuccess) {
                    options.onSuccess(event.data.payment);
                }
            } else if (event.data.type === 'payme2d_payment_failed') {
                document.body.removeChild(overlay);
                if (options.onError) {
                    options.onError(event.data.error);
                }
            }
        });
    }

    /**
     * Get payment status
     */
    async getPayment(transactionId) {
        const response = await fetch(`${this.baseUrl}/payments/${transactionId}`, {
            headers: {
                'Authorization': `Bearer ${this.apiKey}`
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch payment');
        }

        return await response.json();
    }

    /**
     * Refund a payment
     */
    async refund(transactionId, amount, reason) {
        const response = await fetch(`${this.baseUrl}/refunds`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.apiKey}`
            },
            body: JSON.stringify({
                transaction_id: transactionId,
                amount: amount,
                reason: reason
            })
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Refund failed');
        }

        return await response.json();
    }

    /**
     * List all payments
     */
    async listPayments(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${this.baseUrl}/payments?${params}`, {
            headers: {
                'Authorization': `Bearer ${this.apiKey}`
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch payments');
        }

        return await response.json();
    }
}

// Export for different module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PayMe2D;
}
if (typeof window !== 'undefined') {
    window.PayMe2D = PayMe2D;
}
