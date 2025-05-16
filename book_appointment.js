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
    
    // Filter functionality
    const specialtyFilter = document.getElementById('specialty-filter');
    const searchInput = document.querySelector('.search-input input');
    const clearFiltersBtn = document.querySelector('.clear-filters-btn');
    const doctorCards = document.querySelectorAll('.doctor-card');
    
    // Function to filter doctors based on specialty and search term
    function filterDoctors() {
        const specialty = specialtyFilter.value;
        const searchTerm = searchInput.value.toLowerCase();
        
        doctorCards.forEach(card => {
            const doctorSpecialty = card.querySelector('.specialty').textContent.toLowerCase();
            const doctorName = card.querySelector('h4').textContent.toLowerCase();
            
            const matchesSpecialty = specialty === 'all' || doctorSpecialty === specialty.toLowerCase();
            const matchesSearch = doctorName.includes(searchTerm) || doctorSpecialty.includes(searchTerm);
            
            if (matchesSpecialty && matchesSearch) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update available doctors count
        updateDoctorCount();
    }
    
    // Function to update the doctor count
    function updateDoctorCount() {
        const visibleDoctors = document.querySelectorAll('.doctor-card[style="display: flex;"]').length;
        document.querySelector('.doctors-header p').textContent = `${visibleDoctors} doctors available`;
    }
    
    // Add event listeners for filters
    if (specialtyFilter) {
        specialtyFilter.addEventListener('change', filterDoctors);
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterDoctors);
    }
    
    // Clear filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            specialtyFilter.value = 'all';
            searchInput.value = '';
            
            doctorCards.forEach(card => {
                card.style.display = 'flex';
            });
            
            updateDoctorCount();
        });
    }
    
    // Book appointment buttons
    const bookButtons = document.querySelectorAll('.book-btn');
    bookButtons.forEach(button => {
        button.addEventListener('click', function() {
            const doctorName = this.closest('.doctor-card').querySelector('h4').textContent;
            alert(`Booking appointment with ${doctorName}. In a real application, this would open a scheduling interface.`);
        });
    });
});