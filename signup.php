<?php
// Include database connection
require_once "config/database.php";

// Define variables and initialize with empty values
$email = $password = $confirm_password = $first_name = $last_name = $role = $specialty = "";
$email_err = $password_err = $confirm_password_err = $name_err = $specialty_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Determine which form was submitted
    $role = isset($_POST["role"]) ? $_POST["role"] : "";
    
    // Validate email
    if(empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate name
    if(empty(trim($_POST["first_name"])) || empty(trim($_POST["last_name"]))) {
        $name_err = "Please enter your full name.";
    } else {
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);
    }
    
    // Validate specialty if doctor
    if($role == "doctor" && empty(trim($_POST["specialty"]))) {
        $specialty_err = "Please select a specialty.";
    } else if($role == "doctor") {
        $specialty = trim($_POST["specialty"]);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($name_err) && ($role == "patient" || empty($specialty_err))) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (email, password, role, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_email, $param_password, $param_role, $param_first_name, $param_last_name);
            
            // Set parameters
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_role = $role;
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                // If doctor, store specialty in a separate table or update this record
                if($role == "doctor" && !empty($specialty)) {
                    // Here you would add code to store the doctor's specialty
                    // This would require adding a specialty field to the users table
                    // or creating a separate doctors table with a foreign key to users
                }
                
                // Redirect to login page
                header("location: login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthConnect - Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="signup.css">
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

    <main class="signup-container">
        <div class="signup-form-container">
            <div class="user-type-selector">
                <a href="#" class="user-type <?php echo (empty($role) || $role == 'patient') ? 'active' : ''; ?>" id="patient-tab">
                    <i class="fas fa-user"></i> Patient
                </a>
                <a href="#" class="user-type <?php echo ($role == 'doctor') ? 'active' : ''; ?>" id="doctor-tab">
                    <i class="fas fa-user-md"></i> Doctor
                </a>
            </div>

            <!-- Patient Registration Form -->
            <form id="patient-form" class="signup-form <?php echo ($role == 'doctor') ? 'hidden' : ''; ?>" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h2>Create your HealthConnect account</h2>
                <p class="form-subtitle">Register as a patient to consult with doctors</p>
                <input type="hidden" name="role" value="patient">
                
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" value="<?php echo $first_name; ?>" required>
                    <span class="error"><?php echo $name_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" value="<?php echo $last_name; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>" required>
                    <span class="error"><?php echo $email_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                    <span class="error"><?php echo $password_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    <span class="error"><?php echo $confirm_password_err; ?></span>
                </div>

                <button type="submit" class="create-account-btn">Create Account</button>

                <p class="login-link">Already have an account? <a href="login.php">Log in</a></p>
            </form>

            <!-- Doctor Registration Form -->
            <form id="doctor-form" class="signup-form <?php echo (empty($role) || $role == 'patient') ? 'hidden' : ''; ?>" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h2>Create your HealthConnect account</h2>
                <p class="form-subtitle">Register as a doctor to help patients</p>
                <input type="hidden" name="role" value="doctor">

                <div class="form-group">
                    <label for="doctor_first_name">First Name</label>
                    <input type="text" id="doctor_first_name" name="first_name" placeholder="Enter your first name" value="<?php echo $first_name; ?>" required>
                    <span class="error"><?php echo $name_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="doctor_last_name">Last Name</label>
                    <input type="text" id="doctor_last_name" name="last_name" placeholder="Enter your last name" value="<?php echo $last_name; ?>" required>
                </div>

                <div class="form-group">
                    <label for="doctor_email">Email</label>
                    <input type="email" id="doctor_email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>" required>
                    <span class="error"><?php echo $email_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="specialty">Specialty</label>
                    <select id="specialty" name="specialty" required>
                        <option value="" disabled <?php echo empty($specialty) ? 'selected' : ''; ?>>Select Specialty</option>
                        <option value="cardiology" <?php echo ($specialty == 'cardiology') ? 'selected' : ''; ?>>Cardiology</option>
                        <option value="dermatology" <?php echo ($specialty == 'dermatology') ? 'selected' : ''; ?>>Dermatology</option>
                        <option value="neurology" <?php echo ($specialty == 'neurology') ? 'selected' : ''; ?>>Neurology</option>
                        <option value="orthopedics" <?php echo ($specialty == 'orthopedics') ? 'selected' : ''; ?>>Orthopedics</option>
                        <option value="pediatrics" <?php echo ($specialty == 'pediatrics') ? 'selected' : ''; ?>>Pediatrics</option>
                        <option value="psychiatry" <?php echo ($specialty == 'psychiatry') ? 'selected' : ''; ?>>Psychiatry</option>
                        <option value="general" <?php echo ($specialty == 'general') ? 'selected' : ''; ?>>General Medicine</option>
                    </select>
                    <span class="error"><?php echo $specialty_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="doctor_password">Password</label>
                    <input type="password" id="doctor_password" name="password" placeholder="Create a password" required>
                    <span class="error"><?php echo $password_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="doctor_confirm_password">Confirm Password</label>
                    <input type="password" id="doctor_confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    <span class="error"><?php echo $confirm_password_err; ?></span>
                </div>

                <button type="submit" class="create-account-btn">Create Account</button>

                <p class="login-link">Already have an account? <a href="login.php">Log in</a></p>
            </form>
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
                    <li><a href="#">Home</a></li>
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
    });
    </script>
</body>
</html>
