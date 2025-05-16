document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for navigation links
    const links = document.querySelectorAll('a[href^="#"]');
    
    for (const link of links) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    }
    
    // Button event listeners
    const getStartedBtn = document.querySelector('.get-started-btn');
    const findDoctorsBtn = document.querySelector('.find-doctors-btn');
    const createAccountBtn = document.querySelector('.create-account-btn');
    const loginBtn = document.querySelector('.login-btn');
    const signupBtn = document.querySelector('.signup-btn');
    
    if (getStartedBtn) {
        getStartedBtn.addEventListener('click', function() {
            alert('Get Started clicked! This would redirect to registration page.');
        });
    }
    
    if (findDoctorsBtn) {
        findDoctorsBtn.addEventListener('click', function() {
            alert('Find Doctors clicked! This would redirect to doctor search page.');
        });
    }
    
    if (createAccountBtn) {
        createAccountBtn.addEventListener('click', function() {
            alert('Create Account clicked! This would redirect to registration page.');
        });
    }
    
    if (loginBtn) {
        loginBtn.addEventListener('click', function() {
            // Alert removed - button already links to login page
        });
    }
    
    if (signupBtn) {
        signupBtn.addEventListener('click', function() {
            // Alert removed - button already links to signup page
        });
    }
    
    // Feature cards hover effect enhancement
    const featureCards = document.querySelectorAll('.feature-card');
    
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 10px 30px rgba(138, 107, 255, 0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.05)';
        });
    });
});