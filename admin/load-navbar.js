// Load Universal Admin Navigation Bar
(function() {
    // Fetch and inject navbar
    fetch('admin-navbar.html')
        .then(response => response.text())
        .then(html => {
            // Insert navbar at the beginning of body
            document.body.insertAdjacentHTML('afterbegin', html);
        })
        .catch(error => {
            console.error('Error loading navbar:', error);
        });
})();
