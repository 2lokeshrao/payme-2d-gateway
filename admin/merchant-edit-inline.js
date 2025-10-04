// Inline Edit Merchant Details Function
function editMerchantDetails() {
    const merchantId = getMerchantId();
    
    // Create modal overlay
    const modal = document.createElement('div');
    modal.id = 'editMerchantModal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    `;
    
    // Create modal content
    modal.innerHTML = `
        <div style="
            background: white;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        ">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 18px;">✏️ Edit Merchant Details</h3>
                <button onclick="closeEditModal()" style="
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                    color: #666;
                ">×</button>
            </div>
            
            <form id="editMerchantForm" style="font-size: 14px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Business Name</label>
                    <input type="text" id="businessName" value="TechMart Solutions Pvt Ltd" style="
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 14px;
                    ">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Business Type</label>
                    <select id="businessType" style="
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 14px;
                    ">
                        <option value="ecommerce" selected>E-commerce</option>
                        <option value="retail">Retail</option>
                        <option value="services">Services</option>
                        <option value="saas">SaaS</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Contact Email</label>
                    <input type="email" id="contactEmail" value="contact@techmart.com" style="
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 14px;
                    ">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Contact Phone</label>
                    <input type="tel" id="contactPhone" value="+91 98765 43210" style="
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 14px;
                    ">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Website</label>
                    <input type="url" id="website" value="https://techmart.com" style="
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 14px;
                    ">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Address</label>
                    <textarea id="address" rows="3" style="
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 14px;
                        resize: vertical;
                    ">123 Business Park, Andheri East, Mumbai</textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">City</label>
                        <input type="text" id="city" value="Mumbai" style="
                            width: 100%;
                            padding: 8px 12px;
                            border: 1px solid #ddd;
                            border-radius: 4px;
                            font-size: 14px;
                        ">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">State</label>
                        <input type="text" id="state" value="Maharashtra" style="
                            width: 100%;
                            padding: 8px 12px;
                            border: 1px solid #ddd;
                            border-radius: 4px;
                            font-size: 14px;
                        ">
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="saveMerchantDetails()" style="
                        flex: 1;
                        background: #2196f3;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 4px;
                        font-size: 14px;
                        cursor: pointer;
                    ">💾 Save Changes</button>
                    <button type="button" onclick="closeEditModal()" style="
                        flex: 1;
                        background: #f5f5f5;
                        color: #333;
                        border: 1px solid #ddd;
                        padding: 10px 20px;
                        border-radius: 4px;
                        font-size: 14px;
                        cursor: pointer;
                    ">Cancel</button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Close modal function
function closeEditModal() {
    const modal = document.getElementById('editMerchantModal');
    if (modal) {
        modal.remove();
    }
}

// Save merchant details function
function saveMerchantDetails() {
    const merchantId = getMerchantId();
    
    // Get form values
    const formData = {
        businessName: document.getElementById('businessName').value,
        businessType: document.getElementById('businessType').value,
        contactEmail: document.getElementById('contactEmail').value,
        contactPhone: document.getElementById('contactPhone').value,
        website: document.getElementById('website').value,
        address: document.getElementById('address').value,
        city: document.getElementById('city').value,
        state: document.getElementById('state').value
    };
    
    // Simulate API call
    console.log('Saving merchant details:', formData);
    
    // Show success notification
    showNotification('Merchant details updated successfully!', 'success');
    
    // Close modal
    closeEditModal();
    
    // In real application, you would:
    // 1. Send data to API
    // 2. Update the page with new data
    // 3. Reload or update the merchant details section
}
