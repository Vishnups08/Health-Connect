<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect to appropriate dashboard
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if($_SESSION["role"] === "patient") {
        header("location: patient_dashboard.php");
    } else {
        header("location: doctor_dashboard.php");
    }
    exit;
}

// Include database connection file
require_once "config/database.php";

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";
$role = isset($_POST["role"]) ? $_POST["role"] : "patient";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get role from form
    $role = $_POST["role"];
    
    // Check if email is empty
    if(empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, email, password, role, first_name, last_name FROM users WHERE email = ? AND role = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_email, $param_role);
            
            // Set parameters
            $param_email = $email;
            $param_role = $role;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1) {                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password, $user_role, $first_name, $last_name);
                    if(mysqli_stmt_fetch($stmt)) {
                        if(password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            $_SESSION["role"] = $user_role;
                            $_SESSION["first_name"] = $first_name;
                            $_SESSION["last_name"] = $last_name;
                            
                            // Redirect user to appropriate dashboard
                            if($user_role === "patient") {
                                header("location: patient_dashboard.php");
                            } else {
                                header("location: doctor_dashboard.php");
                            }
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    // Email doesn't exist, display a generic error message
                    $login_err = "Invalid email or password.";
                }
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
    <title>HealthConnect - Login</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="login.css">
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

    <main class="login-container">
        <div class="login-form-container">
            <div class="user-type-selector">
                <a href="#" class="user-type <?php echo ($role == 'patient') ? 'active' : ''; ?>" id="patient-tab">
                    <i class="fas fa-user"></i> Patient
                </a>
                <a href="#" class="user-type <?php echo ($role == 'doctor') ? 'active' : ''; ?>" id="doctor-tab">
                    <i class="fas fa-user-md"></i> Doctor
                </a>
            </div>

            <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>

            <form id="login-form" class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h2>Login to HealthConnect</h2>
                <p class="form-subtitle">Enter your credentials to access your <?php echo $role; ?> account</p>
                <input type="hidden" name="role" value="<?php echo $role; ?>" id="role-input">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>" required>
                    <span class="error"><?php echo $email_err; ?></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="error"><?php echo $password_err; ?></span>
                    <div class="forgot-password">
                        <a href="forgot_password.php">Forgot password?</a>
                    </div>
                </div>

                <button type="submit" class="login-submit-btn">Login</button>

                <p class="signup-link">Don't have an account? <a href="signup.php">Create an account</a></p>
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
        const patientTab = document.getElementById('patient-tab');
        const doctorTab = document.getElementById('doctor-tab');
        const roleInput = document.getElementById('role-input');
        const formSubtitle = document.querySelector('.form-subtitle');

        patientTab.addEventListener('click', function(e) {
            e.preventDefault();
            patientTab.classList.add('active');
            doctorTab.classList.remove('active');
            roleInput.value = 'patient';
            formSubtitle.textContent = 'Enter your credentials to access your patient account';
        });

        doctorTab.addEventListener('click', function(e) {
            e.preventDefault();
            doctorTab.classList.add('active');
            patientTab.classList.remove('active');
            roleInput.value = 'doctor';
            formSubtitle.textContent = 'Enter your credentials to access your doctor account';
        });
    });
    </script>
</body>
</html>