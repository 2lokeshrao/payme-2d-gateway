// Settings Navigation - Completely Fixed Version

document.addEventListener('DOMContentLoaded', function() {
    console.log('Settings navigation loaded');
    
    // Get all menu links
    const menuLinks = document.querySelectorAll('.settings-menu a');
    
    // Add click handlers to all menu links
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get section name from href
            const href = this.getAttribute('href');
            const sectionName = href.replace('#', '');
            
            console.log('Clicked:', sectionName);
            
            // Show the section
            showSection(sectionName);
        });
    });
    
    // Load section from URL hash on page load
    const hash = window.location.hash.substring(1);
    if (hash) {
        console.log('Loading section from hash:', hash);
        showSection(hash);
    } else {
        // Show general section by default
        showSection('general');
    }
});

function showSection(sectionName) {
    console.log('showSection called with:', sectionName);
    
    // Hide all sections
    const allSections = document.querySelectorAll('.settings-section');
    allSections.forEach(section => {
        section.style.display = 'none';
        section.classList.remove('active');
    });
    
    // Remove active class from all menu items
    const allLinks = document.querySelectorAll('.settings-menu a');
    allLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Show selected section
    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.style.display = 'block';
        targetSection.classList.add('active');
        console.log('Showing section:', sectionName + '-section');
    } else {
        console.error('Section not found:', sectionName + '-section');
    }
    
    // Add active class to clicked menu item
    const activeLink = document.querySelector(`.settings-menu a[href="#${sectionName}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
    
    // Update URL hash
    window.location.hash = sectionName;
}

// Make showSection available globally
window.showSection = showSection;

console.log('Settings navigation script loaded successfully');
