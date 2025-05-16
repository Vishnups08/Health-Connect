document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown functionality
    const profileToggle = document.getElementById('profile-toggle');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileToggle && profileDropdown) {
        // Toggle dropdown when profile image is clicked
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking elsewhere on the page
        document.addEventListener('click', function(e) {
            if (profileDropdown.classList.contains('show') && !profileToggle.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }
    
    // Handle book appointment button
    const bookAppointmentBtn = document.querySelector('.book-appointment-btn');
    if (bookAppointmentBtn) {
        bookAppointmentBtn.addEventListener('click', function() {
            console.log('Book appointment clicked');
            window.location.href = 'book_appointment.html';
        });
    }
    
    // Service card click handlers
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('click', function() {
            const serviceName = this.querySelector('h3').textContent;
            console.log(`${serviceName} service clicked`);
            
            // Navigate to the appropriate page based on the service
            if (serviceName === 'Health Assistant') {
                window.location.href = 'health_assistant.html';
            } else if (serviceName === 'Medical Documents') {
                window.location.href = 'medical_documents.html';
            } else if (serviceName === 'Book Appointment') {
                window.location.href = 'book_appointment.html';
            }
        });
    });
    
    // Example of how you might load data from an API
    function loadDashboardData() {
        // This would be an API call in a real application
        // For now, we'll simulate empty data
        const dashboardData = {
            upcomingAppointments: [],
            notifications: []
        };
        
        // Update the UI with the data
        updateDashboardUI(dashboardData);
    }
    
    function updateDashboardUI(data) {
        // If we had actual appointment data, we would update the appointments section here
        // If we had actual notification data, we would update the notifications section here
    }
    
    // Load the dashboard data when the page loads
    loadDashboardData();
});