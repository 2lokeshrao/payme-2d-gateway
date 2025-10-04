// Settings Navigation - Show content in right panel on menu click

// Function to show specific section
function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.settings-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
    
    // Show selected section
    const selectedSection = document.getElementById(sectionId + '-section');
    if (selectedSection) {
        selectedSection.style.display = 'block';
    }
    
    // Update active menu item
    const menuItems = document.querySelectorAll('.settings-menu li a');
    menuItems.forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to clicked menu item
    const activeItem = document.querySelector(`a[href="#${sectionId}"]`);
    if (activeItem) {
        activeItem.classList.add('active');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get hash from URL
    const hash = window.location.hash.substring(1); // Remove #
    
    // If hash exists, show that section
    if (hash) {
        showSection(hash);
    } else {
        // Show first section by default (general)
        showSection('general');
    }
    
    // Add click handlers to menu items
    const menuLinks = document.querySelectorAll('.settings-menu li a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('href').substring(1);
            showSection(sectionId);
            
            // Update URL hash
            window.location.hash = sectionId;
        });
    });
});

// Save settings function (placeholder)
function saveSettings(section) {
    console.log('Saving settings for section:', section);
    
    // Show notification
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #4caf50;
        color: white;
        padding: 15px 20px;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        z-index: 10000;
        font-size: 14px;
    `;
    notification.textContent = '✅ Settings saved successfully!';
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
    
    // In real application, you would send data to API here
}
