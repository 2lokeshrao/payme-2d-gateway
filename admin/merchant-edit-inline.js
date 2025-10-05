// Improved Inline Edit Merchant Details Function with Modern UI
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
        overflow-y: auto;
    `;
    
    // Create modal content with modern styling
    modal.innerHTML = `
        <div style="
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        ">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="margin: 0; font-size: 24px; color: #333;">✏️ Edit Merchant Details</h2>
                <button onclick="closeEditModal()" style="
                    background: none;
                    border: none;
                    font-size: 28px;
                    cursor: pointer;
                    color: #999;
                    line-height: 1;
                    padding: 0;
                    width: 30px;
                    height: 30px;
                ">&times;</button>
            </div>
            
            <form id="editMerchantForm" style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;">Business Name</label>
                    <input type="text" id="businessName" value="TechMart Solutions Pvt Ltd" required style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-size: 14px;
                        transition: border-color 0.3s;
                    " onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e0e0'">
                </div>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;">Business Type</label>
                    <select id="businessType" required style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-size: 14px;
                        background: white;
                        cursor: pointer;
                    ">
                        <option value="ecommerce" selected>E-commerce</option>
                        <option value="retail">Retail</option>
                        <option value="services">Services</option>
                        <option value="saas">SaaS</option>
                        <option value="manufacturing">Manufacturing</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;">Contact Email</label>
                    <input type="email" id="contactEmail" value="contact@techmart.com" required style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-size: 14px;
                    " onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e0e0'">
                </div>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;">Contact Phone</label>
                    <input type="tel" id="contactPhone" value="+91 98765 43210" required style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-size: 14px;
                    " onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e0e0'">
                </div>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;">Website</label>
                    <input type="url" id="website" value="https://techmart.com" required style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-size: 14px;
                    " onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e0e0'">
                </div>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;">Address</label>
                    <textarea id="address" rows="3" required style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-size: 14px;
                        resize: vertical;
                        font-family: inherit;
                    " onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e0e0'">123 Business Park, Andheri East, Mumbai</textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;">City</label>
                        <input type="text" id="city" value="Mumbai" required style="
                            width: 100%;
                            padding: 12px;
                            border: 2px solid #e0e0e0;
                            border-radius: 8px;
                            font-size: 14px;
                        " onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e0e0'">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;">State</label>
                        <input type="text" id="state" value="Maharashtra" required style="
                            width: 100%;
                            padding: 12px;
                            border: 2px solid #e0e0e0;
                            border-radius: 8px;
                            font-size: 14px;
                        " onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e0e0e0'">
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 10px;">
                    <button type="button" onclick="saveMerchantDetails()" style="
                        flex: 1;
                        padding: 14px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        border: none;
                        border-radius: 8px;
                        font-weight: 600;
                        font-size: 15px;
                        cursor: pointer;
                        transition: transform 0.2s;
                    " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        💾 Save Changes
                    </button>
                    <button type="button" onclick="closeEditModal()" style="
                        flex: 1;
                        padding: 14px;
                        background: #6c757d;
                        color: white;
                        border: none;
                        border-radius: 8px;
                        font-weight: 600;
                        font-size: 15px;
                        cursor: pointer;
                        transition: background 0.2s;
                    " onmouseover="this.style.background='#5a6268'" onmouseout="this.style.background='#6c757d'">
                        ❌ Cancel
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeEditModal();
        }
    });
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
    
    // Validate form
    if (!formData.businessName || !formData.contactEmail || !formData.contactPhone) {
        alert('❌ Please fill all required fields!');
        return;
    }
    
    // Simulate API call
    console.log('Saving merchant details:', formData);
    
    // Update UI with new values (if elements exist on page)
    const businessNameEl = document.querySelector('.profile-info h2');
    if (businessNameEl) {
        businessNameEl.textContent = formData.businessName;
    }
    
    // Close modal
    closeEditModal();
    
    // Show success notification
    alert('✅ Merchant details updated successfully!\n\n' +
          'Business Name: ' + formData.businessName + '\n' +
          'Business Type: ' + formData.businessType + '\n' +
          'Email: ' + formData.contactEmail + '\n' +
          'Phone: ' + formData.contactPhone + '\n' +
          'Website: ' + formData.website + '\n' +
          'City: ' + formData.city + ', ' + formData.state);
    
    // In real application, you would:
    // 1. Send data to API endpoint
    // 2. Handle response and errors
    // 3. Update the page with new data
    // 4. Show proper toast notification
}

// Helper function to get merchant ID from URL
function getMerchantId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id') || '1';
}
