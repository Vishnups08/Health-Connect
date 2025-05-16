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

// Fetch recent messages if needed
$recent_messages = [];
$has_messages = false;

// You can uncomment and modify this section if you want to load previous messages from the database
/*
$sql = "SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = 0) OR (sender_id = 0 AND receiver_id = ?) 
        ORDER BY created_at DESC LIMIT 10";

if($stmt = mysqli_prepare($conn, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
    
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            $has_messages = true;
            while($row = mysqli_fetch_assoc($result)) {
                $recent_messages[] = $row;
            }
        }
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
}
*/

// Handle new message submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    $message_text = trim($_POST["message"]);
    
    if(!empty($message_text)) {
        // Insert message into database
        // Using 0 as receiver_id to represent the AI assistant
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, 0, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "is", $user_id, $message_text);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // Redirect to prevent form resubmission
            header("location: health_assistant.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthConnect - Health Assistant</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="patient_dashboard.css">
    <link rel="stylesheet" href="health_assistant.css">
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

    <main class="chat-container">
        <div class="chat-header">
            <h2>Health Assistant</h2>
            <p>Chat with our AI assistant for quick health guidance. For emergencies, please call emergency services.</p>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <div class="message assistant">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-text">Hello <?php echo htmlspecialchars($first_name); ?>! I'm your health assistant. How can I help you today?</div>
                    <div class="message-time"><?php echo date('h:i A'); ?></div>
                </div>
            </div>
            
            <?php if($has_messages): ?>
                <?php foreach($recent_messages as $msg): ?>
                    <?php if($msg['sender_id'] == $user_id): ?>
                        <div class="message user">
                            <div class="message-content">
                                <div class="message-text"><?php echo htmlspecialchars($msg['message']); ?></div>
                                <div class="message-time"><?php echo date('h:i A', strtotime($msg['created_at'])); ?></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="message assistant">
                            <div class="message-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="message-content">
                                <div class="message-text"><?php echo htmlspecialchars($msg['message']); ?></div>
                                <div class="message-time"><?php echo date('h:i A', strtotime($msg['created_at'])); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- User messages will be added here dynamically -->
        </div>
        
        <div class="chat-input">
            <form method="POST" action="" id="chat-form">
                <input type="text" id="user-message" name="message" placeholder="Type your health question..." required />
                <button type="submit" id="send-message">
                    <i class="fas fa-paper-plane"></i>
                </button>
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
        
        // Chat functionality
        const chatForm = document.getElementById('chat-form');
        const userMessageInput = document.getElementById('user-message');
        const chatMessages = document.getElementById('chat-messages');
        
        // This is for client-side immediate feedback before form submission
        chatForm.addEventListener('submit', function(e) {
            // Don't prevent default - we want the form to submit to process on server
            // But we can add the message to the UI immediately for better UX
            
            const messageText = userMessageInput.value.trim();
            if (messageText) {
                // Add user message to chat
                const currentTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                const userMessageHTML = `
                    <div class="message user">
                        <div class="message-content">
                            <div class="message-text">${messageText}</div>
                            <div class="message-time">${currentTime}</div>
                        </div>
                    </div>
                `;
                
                chatMessages.insertAdjacentHTML('beforeend', userMessageHTML);
                
                // Scroll to bottom of chat
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
    });
    </script>
</body>
</html>