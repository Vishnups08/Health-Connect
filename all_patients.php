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

// Fetch all patients for this doctor
$patients = [];
$has_patients = false;

$patients_sql = "SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, 
                MAX(a.appointment_date) as last_visit,
                COUNT(a.id) as appointment_count
                FROM appointments a 
                JOIN users u ON a.patient_id = u.id 
                WHERE a.doctor_id = ? 
                GROUP BY u.id 
                ORDER BY last_visit DESC";

if($stmt = mysqli_prepare($conn, $patients_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            $has_patients = true;
            while($row = mysqli_fetch_assoc($result)) {
                $patients[] = $row;
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
    <title>HealthConnect - All Patients</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="doctor_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .patients-container {
            margin: 20px 0;
        }
        
        .patients-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .patients-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .patients-table th,
        .patients-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .patients-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .patients-table tr:last-child td {
            border-bottom: none;
        }
        
        .patients-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .patient-actions {
            display: flex;
            gap: 10px;
        }
        
        .view-records-btn {
            padding: 6px 12px;
            background-color: #4a6bdf;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .view-records-btn:hover {
            background-color: #3a5bbf;
        }
        
        .message-btn {
            padding: 6px 12px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .message-btn:hover {
            background-color: #5a6268;
        }
        
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .search-box {
            flex: 1;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px;
            padding-left: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .patients-table {
                display: block;
                overflow-x: auto;
            }
            
            .search-filter {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="logo">
            <h1>HealthConnect</h1>
        </div>
        <div class="header-right">
            <div class="nav-links">
                <a href="doctor_dashboard.php">Dashboard</a>
                <a href="all_patients.php" class="active">My Patients</a>
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
                <h2>All Patients</h2>
                <p>View and manage all your patients in one place.</p>
            </div>
        </section>
        
        <section class="patients-container">
            <div class="search-filter">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="patient-search" placeholder="Search patients by name or email...">
                </div>
            </div>
            
            <?php if($has_patients): ?>
                <div class="patients-header">
                    <h3><i class="fas fa-user-friends"></i> Patient List</h3>
                    <p><?php echo count($patients); ?> patients found</p>
                </div>
                
                <table class="patients-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Email</th>
                            <th>Last Visit</th>
                            <th>Total Appointments</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($patients as $patient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($patient['last_visit'])); ?></td>
                                <td><?php echo $patient['appointment_count']; ?></td>
                                <td class="patient-actions">
                                    <a href="#" class="view-records-btn">View Records</a>
                                    <a href="#" class="message-btn">Message</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h4>No Patients Found</h4>
                    <p>You don't have any patients yet.</p>
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
        // Profile dropdown functionality
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
        
        // Patient search functionality
        const searchInput = document.getElementById('patient-search');
        const patientRows = document.querySelectorAll('.patients-table tbody tr');
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            
            patientRows.forEach(row => {
                const patientName = row.cells[0].textContent.toLowerCase();
                const patientEmail = row.cells[1].textContent.toLowerCase();
                
                if (patientName.includes(searchTerm) || patientEmail.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    </script>
</body>
</html>