<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "patient") {
    header("location: login.php");
    exit;
}

// Include database connection file
require_once "config/database.php";

// Get user information
$user_id = $_SESSION["id"];
$first_name = $_SESSION["first_name"];
$last_name = $_SESSION["last_name"];
$full_name = $first_name . " " . $last_name;

// Fetch upcoming appointments
$upcoming_appointments = [];
$has_appointments = false;

$sql = "SELECT a.*, u.first_name, u.last_name 
        FROM appointments a 
        JOIN users u ON a.doctor_id = u.id 
        WHERE a.patient_id = ? AND a.status = 'scheduled' AND a.appointment_date >= NOW() 
        ORDER BY a.appointment_date ASC 
        LIMIT 5";

if($stmt = mysqli_prepare($conn, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            $has_appointments = true;
            while($row = mysqli_fetch_assoc($result)) {
                $upcoming_appointments[] = $row;
            }
        }
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
// mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthConnect - Patient Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="patient_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="logo">
            <h1>HealthConnect</h1>
        </div>
        <div class="header-right">
            <div class="nav-links">
                <a href="patient_dashboard.php" class="active">Dashboard</a>
                <a href="book_appointment.php">My Appointments</a>
            </div>
            <div class="user-profile">
                <div class="profile-image" id="profile-toggle">
                    <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Patient profile">
                </div>
                <div class="profile-dropdown" id="profile-dropdown">
                    <ul>
                        <li><a href="#"><i class="fas fa-user"></i> View Profile</a></li>
                        <li><a href="#"><i class="fas fa-key"></i> Change Password</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main class="dashboard-container">
        <section class="welcome-section">
            <div class="welcome-text">
                <h2>Welcome, <?php echo htmlspecialchars($full_name); ?></h2>
                <p>Manage your healthcare journey from your personal dashboard.</p>
            </div>
        </section>

        <section class="service-cards">
            <div class="service-card">
                <div class="service-icon">
                    <i class="far fa-comment-dots"></i>
                </div>
                <h3>Health Assistant</h3>
                <p>Chat with our AI health assistant for quick guidance and answers.</p>
                <a href="health_assistant.php" class="service-link">Open Assistant</a>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="far fa-file"></i>
                </div>
                <h3>Medical Documents</h3>
                <p>Access and upload your medical records and documents.</p>
                <a href="medical_documents.php" class="service-link">View Documents</a>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="far fa-calendar-plus"></i>
                </div>
                <h3>Book Appointment</h3>
                <p>Find specialists and book appointments based on your needs.</p>
                <a href="book_appointment.php" class="service-link">Book Now</a>
            </div>
        </section>

        <section class="appointments-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="far fa-calendar-check"></i>
                    <h3>Upcoming Appointments</h3>
                </div>
                <p>Your scheduled appointments with healthcare professionals</p>
            </div>
            
            <?php if($has_appointments): ?>
                <div class="appointments-list">
                    <?php foreach($upcoming_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-date">
                                <div class="date"><?php echo date('d', strtotime($appointment['appointment_date'])); ?></div>
                                <div class="month"><?php echo date('M', strtotime($appointment['appointment_date'])); ?></div>
                            </div>
                            <div class="appointment-details">
                                <h4>Dr. <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h4>
                                <p class="appointment-time">
                                    <i class="far fa-clock"></i> 
                                    <?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?>
                                </p>
                                <p class="appointment-status">
                                    <span class="status-badge <?php echo strtolower($appointment['status']); ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="appointment-actions">
                                <a href="#" class="reschedule-btn">Reschedule</a>
                                <a href="#" class="cancel-btn">Cancel</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-calendar"></i>
                    </div>
                    <h4>No upcoming appointments</h4>
                    <p>You don't have any upcoming appointments.</p>
                    <a href="book_appointment.php"><button class="book-appointment-btn">Book an Appointment</button></a>
                </div>
            <?php endif; ?>
        </section>
    </main>

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
                    <li><a href="book_appointment.php">Book Appointment</a></li>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileToggle = document.getElementById('profile-toggle');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        profileToggle.addEventListener('click', function() {
            profileDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    });
    </script>
</body>
</html>