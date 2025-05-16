document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const patientTab = document.getElementById('patient-tab');
    const doctorTab = document.getElementById('doctor-tab');
    const patientForm = document.getElementById('patient-form');
    const doctorForm = document.getElementById('doctor-form');

    patientTab.addEventListener('click', function(e) {
        e.preventDefault();
        patientTab.classList.add('active');
        doctorTab.classList.remove('active');
        patientForm.classList.remove('hidden');
        doctorForm.classList.add('hidden');
    });

    doctorTab.addEventListener('click', function(e) {
        e.preventDefault();
        doctorTab.classList.add('active');
        patientTab.classList.remove('active');
        doctorForm.classList.remove('hidden');
        patientForm.classList.add('hidden');
    });

    // Form validation for patient registration
    const patientRegistrationForm = document.getElementById('patient-form');
    patientRegistrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }

        // Here you would typically send the form data to your server
        alert('Patient account creation successful! (This is a demo)');
    });

    // Form validation for doctor registration
    const doctorRegistrationForm = document.getElementById('doctor-form');
    doctorRegistrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const password = document.getElementById('doctor-password').value;
        const confirmPassword = document.getElementById('doctor-confirm-password').value;
        const specialty = document.getElementById('specialty').value;

        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }

        if (!specialty) {
            alert('Please select your specialty');
            return;
        }

        // Here you would typically send the form data to your server
        alert('Doctor account creation successful! (This is a demo)');
    });

    // Navigation buttons
    const loginBtn = document.querySelector('.login-btn');
    const signupBtn = document.querySelector('.signup-btn');

    loginBtn.addEventListener('click', function() {
        // Alert removed - button already links to login page
    });

    signupBtn.addEventListener('click', function() {
        // Alert removed - we're already on the signup page
    });

    // Login link functionality
    const loginLinks = document.querySelectorAll('.login-link a');
    loginLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Removed preventDefault() and alert to allow normal navigation
            // The href="login.html" in the HTML will now work naturally
        });
    });
});