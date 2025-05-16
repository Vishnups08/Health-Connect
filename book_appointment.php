<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Fetch all doctors from the database
$doctors = [];
$doctor_count = 0;

$sql = "SELECT id, first_name, last_name, specialty, experience_years, rating 
        FROM users 
        WHERE role = 'doctor' 
        ORDER BY last_name ASC";

if($stmt = mysqli_prepare($conn, $sql)) {
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            $doctor_count = mysqli_num_rows($result);
            while($row = mysqli_fetch_assoc($result)) {
                $doctors[] = $row;
            }
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
}

// Handle appointment booking
$booking_success = false;
$booking_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["book_appointment"])) {
    $doctor_id = trim($_POST["doctor_id"]);
    $appointment_date = trim($_POST["appointment_date"]);
    $appointment_time = trim($_POST["appointment_time"]);
    $notes = trim($_POST["notes"]);
    
    // Validate input
    if(empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
        $booking_error = "Please fill all required fields.";
    } else {
        // Combine date and time
        $appointment_datetime = $appointment_date . " " . $appointment_time . ":00";
        
        // Insert appointment into database
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, notes) VALUES (?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiss", $user_id, $doctor_id, $appointment_datetime, $notes);
            
            if(mysqli_stmt_execute($stmt)) {
                $booking_success = true;
            } else {
                $booking_error = "Something went wrong. Please try again later.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthConnect - Book Appointment</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="patient_dashboard.css">
    <link rel="stylesheet" href="book_appointment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="logo">
            <h1>HealthConnect</h1>
        </div>
        <div class="header-right">
            <div class="nav-links">
                <a href="patient_dashboard.php">Dashboard</a>
                <a href="#" class="active">My Appointments</a>
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

    <main class="appointment-container">
        <?php if($booking_success): ?>
        <div class="alert alert-success">
            <p>Your appointment has been booked successfully!</p>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($booking_error)): ?>
        <div class="alert alert-error">
            <p><?php echo $booking_error; ?></p>
        </div>
        <?php endif; ?>
        
        <section class="appointment-header">
            <h2>Book an Appointment</h2>
            <p>Find a specialist and schedule your visit</p>
        </section>
        
        <div class="appointment-content">
            <div class="filter-section">
                <h3><i class="fas fa-filter"></i> Search & Filter</h3>
                
                <div class="search-box">
                    <h4>Search Doctors</h4>
                    <div class="search-input">
                        <input type="text" id="doctor-search" placeholder="Search by name or specialty">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                
                <div class="filter-group">
                    <h4>Specialty</h4>
                    <div class="select-wrapper">
                        <select id="specialty-filter">
                            <option value="all">All Specialties</option>
                            <option value="cardiology">Cardiology</option>
                            <option value="dermatology">Dermatology</option>
                            <option value="neurology">Neurology</option>
                            <option value="orthopedics">Orthopedics</option>
                            <option value="pediatrics">Pediatrics</option>
                            <option value="psychiatry">Psychiatry</option>
                            <option value="oncology">Oncology</option>
                            <option value="gynecology">Gynecology</option>
                        </select>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                
                <button class="clear-filters-btn">Clear Filters</button>
            </div>
            
            <div class="doctors-section">
                <div class="doctors-header">
                    <h3>Available Doctors</h3>
                    <p><?php echo $doctor_count; ?> doctors available</p>
                </div>
                
                <div class="doctors-list">
                    <?php if(count($doctors) > 0): ?>
                        <?php foreach($doctors as $doctor): ?>
                            <div class="doctor-card" data-specialty="<?php echo isset($doctor['specialty']) ? htmlspecialchars($doctor['specialty']) : 'General'; ?>">
                                <div class="doctor-info">
                                    <div class="doctor-image">
                                        <img src="https://randomuser.me/api/portraits/<?php echo (rand(0, 1) == 0) ? 'women' : 'men'; ?>/<?php echo rand(1, 99); ?>.jpg" alt="Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>">
                                    </div>
                                    <div class="doctor-details">
                                        <h4>Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h4>
                                        <span class="specialty">General</span>
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <span>4.8</span>
                                        </div>
                                        <p>8 years experience</p>
                                    </div>
                                </div>
                                <button class="book-btn" data-doctor-id="<?php echo $doctor['id']; ?>" data-doctor-name="Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>">Book Appointment</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-doctors">
                            <p>No doctors available at the moment. Please check back later.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Appointment Booking Modal -->
        <div id="booking-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Book Appointment with <span id="doctor-name"></span></h2>
                <form method="POST" action="">
                    <input type="hidden" id="doctor_id" name="doctor_id">
                    
                    <div class="form-group">
                        <label for="appointment_date">Date</label>
                        <input type="date" id="appointment_date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="appointment_time">Time</label>
                        <select id="appointment_time" name="appointment_time" required>
                            <option value="">Select Time</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Any specific concerns or information for the doctor"></textarea>
                    </div>
                    
                    <button type="submit" name="book_appointment" class="submit-btn">Confirm Booking</button>
                </form>
            </div>
        </div>
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
                    <li><a href="book_appointment.php">Find Doctors</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section for-patients">
                <h3>For Patients</h3>
                <ul>
                    <li><a href="login.php">Patient Login</a></li>
                    <li><a href="signup.php">Register</a></li>
                    <li><a href="book_appointment.php">Book Appointment</a></li>
                    <li><a href="health_assistant.php">Health Assistant</a></li>
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
        // Profile dropdown toggle
        const profileToggle = document.getElementById('profile-toggle');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        profileToggle.addEventListener('click', function() {
            profileDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('active');
            }
        });
        
        // Doctor search functionality
        const doctorSearch = document.getElementById('doctor-search');
        const specialtyFilter = document.getElementById('specialty-filter');
        const doctorCards = document.querySelectorAll('.doctor-card');
        const clearFiltersBtn = document.querySelector('.clear-filters-btn');
        
        function filterDoctors() {
            const searchTerm = doctorSearch.value.toLowerCase();
            const specialty = specialtyFilter.value;
            
            doctorCards.forEach(card => {
                const doctorName = card.querySelector('h4').textContent.toLowerCase();
                const doctorSpecialty = card.dataset.specialty.toLowerCase();
                
                const matchesSearch = doctorName.includes(searchTerm);
                const matchesSpecialty = specialty === 'all' || doctorSpecialty === specialty.toLowerCase();
                
                if (matchesSearch && matchesSpecialty) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        doctorSearch.addEventListener('input', filterDoctors);
        specialtyFilter.addEventListener('change', filterDoctors);
        
        clearFiltersBtn.addEventListener('click', function() {
            doctorSearch.value = '';
            specialtyFilter.value = 'all';
            doctorCards.forEach(card => {
                card.style.display = 'block';
            });
        });
        
        // Booking modal functionality
        const modal = document.getElementById('booking-modal');
        const bookBtns = document.querySelectorAll('.book-btn');
        const closeBtn = document.querySelector('.close');
        const doctorNameSpan = document.getElementById('doctor-name');
        const doctorIdInput = document.getElementById('doctor_id');
        
        bookBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const doctorId = this.dataset.doctorId;
                const doctorName = this.dataset.doctorName;
                
                doctorNameSpan.textContent = doctorName;
                doctorIdInput.value = doctorId;
                
                modal.style.display = 'block';
            });
        });
        
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>