document.addEventListener('DOMContentLoaded', function() {
    // Get all forms and elements
    const patientTab = document.getElementById('patient-tab');
    const doctorTab = document.getElementById('doctor-tab');
    const emailForm = document.getElementById('email-form');
    const newPasswordForm = document.getElementById('new-password-form');
    const resetSuccess = document.getElementById('reset-success');
    const formSubtitle = document.querySelector('.form-subtitle');
    const passwordMessage = document.getElementById('password-message');

    // Tab switching functionality
    patientTab.addEventListener('click', function(e) {
        e.preventDefault();
        patientTab.classList.add('active');
        doctorTab.classList.remove('active');
        updateSubtitle();
    });

    doctorTab.addEventListener('click', function(e) {
        e.preventDefault();
        doctorTab.classList.add('active');
        patientTab.classList.remove('active');
        updateSubtitle();
    });

    function updateSubtitle() {
        const isDoctor = doctorTab.classList.contains('active');
        const currentForm = document.querySelector('form:not(.hidden)');
        
        if (currentForm === emailForm) {
            formSubtitle.textContent = isDoctor 
                ? 'Enter your email to reset your doctor account password' 
                : 'Enter your email to reset your password';
        } else if (currentForm === newPasswordForm) {
            formSubtitle.textContent = isDoctor 
                ? 'Create a new password for your doctor account' 
                : 'Create a new password for your account';
        }
    }

    // Email form submission
    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        
        if (email) {
            // In a real app, you would verify the email exists in the system
            // For demo purposes, we'll just proceed to the next step
            emailForm.classList.add('hidden');
            newPasswordForm.classList.remove('hidden');
            updateSubtitle();
        }
    });

    // New password form submission
    newPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        if (newPassword && confirmPassword) {
            if (newPassword === confirmPassword) {
                // In a real app, you would send the new password to the server
                // For demo purposes, we'll just show the success message
                newPasswordForm.classList.add('hidden');
                resetSuccess.classList.remove('hidden');
            } else {
                passwordMessage.classList.remove('hidden');
            }
        }
    });

    // Hide password mismatch message when user starts typing
    document.getElementById('new-password').addEventListener('input', function() {
        passwordMessage.classList.add('hidden');
    });

    document.getElementById('confirm-password').addEventListener('input', function() {
        passwordMessage.classList.add('hidden');
    });
});