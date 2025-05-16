document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const patientTab = document.getElementById('patient-tab');
    const doctorTab = document.getElementById('doctor-tab');
    const loginForm = document.getElementById('login-form');
    const formSubtitle = document.querySelector('.form-subtitle');

    patientTab.addEventListener('click', function(e) {
        e.preventDefault();
        patientTab.classList.add('active');
        doctorTab.classList.remove('active');
        formSubtitle.textContent = 'Enter your credentials to access your patient account';
    });

    doctorTab.addEventListener('click', function(e) {
        e.preventDefault();
        doctorTab.classList.add('active');
        patientTab.classList.remove('active');
        formSubtitle.textContent = 'Enter your credentials to access your doctor account';
    });

    // Form submission - modified for direct login
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Determine which dashboard to redirect to based on active tab
        const activeUserType = document.querySelector('.user-type.active').id;
        const userType = activeUserType === 'patient-tab' ? 'patient' : 'doctor';
        
        // Redirect to the appropriate dashboard without validation
        if (userType === 'patient') {
            window.location.href = 'patient_dashboard.html';
        } else {
            window.location.href = 'doctor_dashboard.html';
        }
    });

    // Direct login buttons - add click handlers to the tab links
    patientTab.addEventListener('dblclick', function() {
        window.location.href = 'patient_dashboard.html';
    });

    doctorTab.addEventListener('dblclick', function() {
        window.location.href = 'doctor_dashboard.html';
    });

    // Modify the login button to provide direct login
    const loginSubmitBtn = document.querySelector('.login-submit-btn');
    loginSubmitBtn.textContent = 'Login';
    
    // Remove required attribute from inputs
    document.getElementById('email').removeAttribute('required');
    document.getElementById('password').removeAttribute('required');

    // Forgot password link
    const forgotPasswordLink = document.querySelector('.forgot-password a');
    forgotPasswordLink.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = 'forgot_password.html';
    });

    // Create account link
    const createAccountLink = document.querySelector('.signup-link a');
    createAccountLink.addEventListener('click', function(e) {
        // This link should already point to signup.html, so we don't need to prevent default
        // Just adding this handler for demo purposes
        console.log('Navigating to signup page');
    });

    // Navigation buttons
    const loginBtn = document.querySelector('.login-btn');
    const signupBtn = document.querySelector('.signup-btn');

    loginBtn.addEventListener('click', function() {
        // Alert removed - we're already on the login page
    });
});