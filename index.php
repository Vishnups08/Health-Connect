<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthConnect - Your Health, Our Priority</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>HealthConnect</h1>
        </div>
        <div class="auth-buttons">
            <a href="login.php"><button class="login-btn">Login</button></a>
            <a href="signup.php"><button class="signup-btn">Sign Up</button></a>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h2>Your Health, Our Priority</h2>
            <p>Connect with specialized healthcare professionals, manage your appointments, and take control of your health journey with our comprehensive healthcare platform.</p>
            <div class="hero-buttons">
                <a href="signup.php"><button class="get-started-btn">Get Started</button></a>
                <a href="#"><button class="find-doctors-btn">Find Doctors</button></a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1584982751601-97dcc096659c?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Healthcare professionals">
        </div>
    </section>

    <section class="features">
        <div class="section-header">
            <h2>What We Offer</h2>
            <p>Our platform provides a seamless healthcare experience with these key features</p>
        </div>
        <div class="feature-cards">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-comment-medical"></i>
                </div>
                <h3>Health Assistant</h3>
                <p>Get instant answers to your health questions with our AI-powered chatbot.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>Easy Appointment Booking</h3>
                <p>Schedule appointments with your preferred healthcare specialists in just a few clicks.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <h3>Medical Records</h3>
                <p>Securely store and manage your medical documents and history in one place.</p>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="section-header">
            <h2>How It Works</h2>
            <p>Start your healthcare journey in three simple steps</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Create an Account</h3>
                <p>Sign up as a patient to access our full range of healthcare services.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Find Specialists</h3>
                <p>Search for healthcare professionals by specialty, experience, and availability.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Book & Manage</h3>
                <p>Schedule appointments and manage your healthcare journey through your dashboard.</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>Ready to Take Control of Your Health?</h2>
        <p>Join thousands of patients who have simplified their healthcare experience with our platform.</p>
        <a href="signup.php"><button class="create-account-btn">Create Your Account</button></a>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section about">
                <h3>HealthConnect</h3>
                <p>Connecting patients with the best healthcare professionals for a better quality of life.</p>
            </div>
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Find Doctors</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section for-patients">
                <h3>For Patients</h3>
                <ul>
                    <li><a href="login.php">Patient Login</a></li>
                    <li><a href="signup.php">Register</a></li>
                    <li><a href="book_appointment.html">Book Appointment</a></li>
                    <li><a href="health_assistant.html">Health Assistant</a></li>
                </ul>
            </div>
            <div class="footer-section for-doctors">
                <h3>For Doctors</h3>
                <ul>
                    <li><a href="login.php">Doctor Login</a></li>
                    <li><a href="signup.php">Join as Doctor</a></li>
                    <li><a href="#">Manage Schedule</a></li>
                    <li><a href="#">Help & Support</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 HealthConnect. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
