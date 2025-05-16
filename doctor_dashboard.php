<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "doctor") {
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

// Handle appointment completion
if(isset($_GET["complete_appointment"]) && !empty($_GET["complete_appointment"])) {
    $appointment_id = $_GET["complete_appointment"];
    
    // Update appointment status to completed
    $update_sql = "UPDATE appointments SET status = 'completed' WHERE id = ? AND doctor_id = ?";
    
    if($stmt = mysqli_prepare($conn, $update_sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $user_id);
        
        if(mysqli_stmt_execute($stmt)) {
            // Redirect to prevent form resubmission
            header("location: doctor_dashboard.php?success=appointment_completed");
            exit;
        } else {
            // Error handling
            $error = "Something went wrong. Please try again later.";
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Success message handling
$success_message = "";
if(isset($_GET["success"]) && $_GET["success"] == "appointment_completed") {
    $success_message = "Appointment has been marked as completed successfully.";
}

// Initialize statistics variables
$today_appointments_count = 0;
$total_patients_count = 0;
$pending_appointments_count = 0;
$monthly_consultations_count = 0;

// Get today's appointments count
$today_sql = "SELECT COUNT(*) as count FROM appointments 
              WHERE doctor_id = ? 
              AND DATE(appointment_date) = CURDATE()";

if($stmt = mysqli_prepare($conn, $today_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($result)) {
            $today_appointments_count = $row['count'];
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Get total unique patients count
$patients_sql = "SELECT COUNT(DISTINCT patient_id) as count FROM appointments 
                WHERE doctor_id = ?";

if($stmt = mysqli_prepare($conn, $patients_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($result)) {
            $total_patients_count = $row['count'];
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Get pending appointments count
$pending_sql = "SELECT COUNT(*) as count FROM appointments 
               WHERE doctor_id = ? 
               AND status = 'scheduled' 
               AND appointment_date > CURDATE()";

if($stmt = mysqli_prepare($conn, $pending_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($result)) {
            $pending_appointments_count = $row['count'];
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Get monthly consultations count
$monthly_sql = "SELECT COUNT(*) as count FROM appointments 
               WHERE doctor_id = ? 
               AND MONTH(appointment_date) = MONTH(CURRENT_DATE()) 
               AND YEAR(appointment_date) = YEAR(CURRENT_DATE())";

if($stmt = mysqli_prepare($conn, $monthly_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($result)) {
            $monthly_consultations_count = $row['count'];
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Fetch today's schedule
$today_appointments = [];
$has_today_appointments = false;

$schedule_sql = "SELECT a.*, u.first_name, u.last_name 
                FROM appointments a 
                JOIN users u ON a.patient_id = u.id 
                WHERE a.doctor_id = ? 
                AND DATE(a.appointment_date) = CURDATE() 
                AND a.status != 'completed' 
                ORDER BY a.appointment_date ASC";

if($stmt = mysqli_prepare($conn, $schedule_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            $has_today_appointments = true;
            while($row = mysqli_fetch_assoc($result)) {
                $today_appointments[] = $row;
            }
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Fetch recent patients
$recent_patients = [];
$has_recent_patients = false;

$recent_sql = "SELECT DISTINCT u.id, u.first_name, u.last_name, MAX(a.appointment_date) as last_visit 
              FROM appointments a 
              JOIN users u ON a.patient_id = u.id 
              WHERE a.doctor_id = ? 
              AND a.status = 'completed' 
              GROUP BY u.id 
              ORDER BY last_visit DESC 
              LIMIT 5";

if($stmt = mysqli_prepare($conn, $recent_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            $has_recent_patients = true;
            while($row = mysqli_fetch_assoc($result)) {
                $recent_patients[] = $row;
            }
        }
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthConnect - Doctor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="doctor_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="logo">
            <h1>HealthConnect</h1>
        </div>
        <div class="header-right">
            <div class="nav-links">
                <a href="doctor_dashboard.php" class="active">Dashboard</a>
                <a href="all_patients.php">My Patients</a>
            </div>
            <div class="user-profile">
                <div class="profile-image" id="profile-toggle">
                    <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Doctor profile">
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
                <p>Manage your appointments and patients from your doctor dashboard.</p>
            </div>
        </section>
        
        <?php if(!empty($success_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <section class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="far fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $today_appointments_count; ?></div>
                <div class="stat-label">Today's Appointments</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-number"><?php echo $total_patients_count; ?></div>
                <div class="stat-label">Total Patients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="far fa-calendar-check"></i>
                </div>
                <div class="stat-number"><?php echo $pending_appointments_count; ?></div>
                <div class="stat-label">Pending Appointments</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number"><?php echo $monthly_consultations_count; ?></div>
                <div class="stat-label">Monthly Consultations</div>
            </div>
        </section>

        <section class="schedule-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="far fa-clock"></i>
                    <h3>Today's Schedule</h3>
                </div>
                <p>Your appointments for today</p>
            </div>
            
            <?php if($has_today_appointments): ?>
                <div class="appointments-list">
                    <?php foreach($today_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-time">
                                <div class="time"><?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?></div>
                            </div>
                            <div class="appointment-details">
                                <h4><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h4>
                                <p class="appointment-status">
                                    <span class="status-badge <?php echo strtolower($appointment['status']); ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </p>
                                <?php if(!empty($appointment['notes'])): ?>
                                    <p class="appointment-notes"><?php echo htmlspecialchars($appointment['notes']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="appointment-actions">
                                <a href="doctor_dashboard.php?complete_appointment=<?php echo $appointment['id']; ?>" class="complete-btn">Complete</a>
                                <a href="#" class="reschedule-btn">Reschedule</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-calendar"></i>
                    </div>
                    <h4>No Appointments Today</h4>
                    <p>You don't have any scheduled appointments for today.</p>
                    <button class="view-schedule-btn">View Schedule</button>
                </div>
            <?php endif; ?>
        </section>

        <section class="recent-patients-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-user-friends"></i>
                    <h3>Recent Patients</h3>
                </div>
                <p>Your recently consulted patients</p>
            </div>
            
            <?php if($has_recent_patients): ?>
                <div class="patients-list">
                    <?php foreach($recent_patients as $patient): ?>
                        <div class="patient-card">
                            <div class="patient-info">
                                <h4><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h4>
                                <p class="last-visit">
                                    <i class="far fa-calendar-alt"></i>
                                    Last visit: <?php echo date('M d, Y', strtotime($patient['last_visit'])); ?>
                                </p>
                            </div>
                            <div class="patient-actions">
                                <a href="#" class="view-records-btn">View Records</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h4>No Recent Patients</h4>
                    <p>Your list of recent patients will appear here.</p>
                    <a href="all_patients.php" class="view-patients-btn">View All Patients</a>
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
                profileDropdown.classList.remove('active');
            }
        });
    });
    </script>
</body>
</html>