// Responsive Menu JavaScript for PayMe 2D Gateway Admin Panel

document.addEventListener('DOMContentLoaded', function() {
    // Create hamburger menu button if it doesn't exist
    const nav = document.querySelector('.admin-nav, .top-nav');
    
    if (nav && !document.querySelector('.menu-toggle')) {
        // Create hamburger button
        const menuToggle = document.createElement('button');
        menuToggle.className = 'menu-toggle';
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        menuToggle.setAttribute('aria-label', 'Toggle menu');
        
        // Insert hamburger button at the beginning of nav
        nav.insertBefore(menuToggle, nav.firstChild);
        
        // Get the menu
        const menu = nav.querySelector('ul');
        
        if (menu) {
            // Toggle menu on button click
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('active');
                
                // Change icon
                const icon = menuToggle.querySelector('i');
                if (menu.classList.contains('active')) {
                    icon.className = 'fas fa-times';
                } else {
                    icon.className = 'fas fa-bars';
                }
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!nav.contains(e.target)) {
                    menu.classList.remove('active');
                    const icon = menuToggle.querySelector('i');
                    icon.className = 'fas fa-bars';
                }
            });
            
            // Close menu when clicking a link
            const menuLinks = menu.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    menu.classList.remove('active');
                    const icon = menuToggle.querySelector('i');
                    icon.className = 'fas fa-bars';
                });
            });
        }
    }
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const menu = document.querySelector('.admin-nav ul, .top-nav ul');
            if (menu && window.innerWidth > 768) {
                menu.classList.remove('active');
                const icon = document.querySelector('.menu-toggle i');
                if (icon) {
                    icon.className = 'fas fa-bars';
                }
            }
        }, 250);
    });
});
