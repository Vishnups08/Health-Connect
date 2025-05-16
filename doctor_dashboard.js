document.addEventListener('DOMContentLoaded', function() {
    // Get the current date for display purposes
    const currentDate = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = currentDate.toLocaleDateString('en-US', options);
    
    // You could add this to the page if needed
    // document.querySelector('.current-date').textContent = formattedDate;
    
    // Handle view schedule button
    const viewScheduleBtn = document.querySelector('.view-schedule-btn');
    if (viewScheduleBtn) {
        viewScheduleBtn.addEventListener('click', function() {
            console.log('View schedule clicked');
            // In a real app, this would navigate to the schedule page
        });
    }
    
    // Handle view all patients button
    const viewPatientsBtn = document.querySelector('.view-patients-btn');
    if (viewPatientsBtn) {
        viewPatientsBtn.addEventListener('click', function() {
            console.log('View all patients clicked');
            // In a real app, this would navigate to the patients page
        });
    }
    
    // Example of how you might load data from an API
    function loadDashboardData() {
        // This would be an API call in a real application
        // For now, we'll simulate empty data
        const dashboardData = {
            todaysAppointments: 0,
            totalPatients: 0,
            pendingAppointments: 0,
            monthlyConsultations: 0,
            schedule: [],
            recentPatients: []
        };
        
        // Update the UI with the data
        updateDashboardUI(dashboardData);
    }
    
    function updateDashboardUI(data) {
        // Update stat numbers
        document.querySelectorAll('.stat-number')[0].textContent = data.todaysAppointments;
        document.querySelectorAll('.stat-number')[1].textContent = data.totalPatients;
        document.querySelectorAll('.stat-number')[2].textContent = data.pendingAppointments;
        document.querySelectorAll('.stat-number')[3].textContent = data.monthlyConsultations;
        
        // If we had actual schedule data, we would update the schedule section here
        // If we had actual patient data, we would update the recent patients section here
    }
    
    // Add this to your existing JavaScript
    
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
    
    // Load the dashboard data when the page loads
    loadDashboardData();
});