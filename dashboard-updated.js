// Update selectPlan function to redirect to payment page
function selectPlan(plan) {
    closeModal('upgradePlanModal');
    
    // Define plan details
    const planDetails = {
        'business': {
            name: 'Business Plan',
            price: '₹999/month'
        },
        'enterprise': {
            name: 'Enterprise Plan',
            price: 'Custom Pricing'
        }
    };
    
    // For enterprise, show contact message
    if (plan === 'enterprise') {
        alert('🎉 Thank you for your interest in the Enterprise plan!\n\nOur sales team will contact you shortly to discuss custom pricing and features.');
        return;
    }
    
    // For business plan, redirect to payment page
    const details = planDetails[plan];
    window.location.href = `upgrade-payment.html?plan=${plan}&name=${encodeURIComponent(details.name)}&price=${encodeURIComponent(details.price)}`;
}
