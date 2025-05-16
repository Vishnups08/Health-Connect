<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Check if user is logged in and is a patient
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once "config/database.php";

// Get user information
$user_id = $_SESSION['id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];

// Handle document upload
$upload_success = false;
$upload_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_document'])) {
    $document_title = mysqli_real_escape_string($conn, $_POST['document_title']);
    $document_type = mysqli_real_escape_string($conn, $_POST['document_type']);
    $document_date = mysqli_real_escape_string($conn, $_POST['document_date']);
    
    // File upload handling
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
        $max_size = 10 * 1024 * 1024; // 10MB
        
        $file_type = $_FILES['document_file']['type'];
        $file_size = $_FILES['document_file']['size'];
        
        if (!in_array($file_type, $allowed_types)) {
            $upload_error = "Error: Only PDF, JPG, and PNG files are allowed.";
        } elseif ($file_size > $max_size) {
            $upload_error = "Error: File size exceeds the 10MB limit.";
        } else {
            // Create uploads directory if it doesn't exist
            $upload_dir = "uploads/medical_documents/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('doc_') . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['document_file']['tmp_name'], $upload_path)) {
                // Insert document record into database
                $sql = "INSERT INTO medical_documents (patient_id, `Document Title`, `document type`, `Document Date`, `document_path`, upload_date) 
                        VALUES (?, ?, ?, ?, ?, NOW())";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "issss", $user_id, $document_title, $document_type, $document_date, $upload_path);
                
                if (mysqli_stmt_execute($stmt)) {
                    $upload_success = true;
                } else {
                    $upload_error = "Error: " . mysqli_error($conn) . " (" . mysqli_stmt_error($stmt) . ")";
                    // Add more detailed error logging
                    error_log("Database error in medical_documents.php: " . mysqli_error($conn) . " | " . mysqli_stmt_error($stmt));
                    
                    // Debug information
                    error_log("Debug info: patient_id=$user_id, title=$document_title, type=$document_type, date=$document_date, path=$upload_path");
                }
                
                mysqli_stmt_close($stmt);
            } else {
                $upload_error = "Error: Failed to upload file.";
            }
        }
    } else {
        $upload_error = "Error: Please select a file to upload.";
    }
}

// Fetch user's medical documents
$documents = [];
$sql = "SELECT * FROM medical_documents WHERE patient_id = ? ORDER BY upload_date DESC";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $documents[] = $row;
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthConnect - Medical Documents</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="patient_dashboard.css">
    <link rel="stylesheet" href="medical_documents.css">
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
                <a href="#">My Appointments</a>
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

    <main class="documents-container">
        <section class="documents-header">
            <h2>Medical Documents</h2>
            <p>Upload, view, and manage your medical records securely.</p>
            <?php if ($upload_success): ?>
                <div class="alert alert-success">
                    Document uploaded successfully!
                </div>
            <?php endif; ?>
            <?php if (!empty($upload_error)): ?>
                <div class="alert alert-error">
                    <?php echo $upload_error; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <section class="documents-tabs">
            <button class="tab-btn active" id="all-documents-btn">All Documents</button>
            <button class="tab-btn" id="upload-document-btn">Upload New Document</button>
        </section>
        
        <section class="documents-content">
            <div class="documents-panel active" id="all-documents-panel">
                <div class="records-header">
                    <h3>Your Medical Records</h3>
                    <p>View and manage all your uploaded medical documents</p>
                </div>
                
                <?php if (empty($documents)): ?>
                <div class="empty-documents">
                    <div class="empty-icon">
                        <i class="far fa-file-alt"></i>
                    </div>
                    <h4>No Documents Yet</h4>
                    <p>Upload your medical documents to keep them organized and accessible.</p>
                    <button class="primary-btn" id="upload-first-document-btn">Upload Your First Document</button>
                </div>
                <?php else: ?>
                <div class="documents-list">
                    <?php foreach ($documents as $document): ?>
                    <div class="document-card">
                        <div class="document-icon">
                            <?php if (strpos($document['document type'], 'lab') !== false): ?>
                                <i class="fas fa-flask"></i>
                            <?php elseif (strpos($document['document type'], 'prescription') !== false): ?>
                                <i class="fas fa-prescription"></i>
                            <?php elseif (strpos($document['document type'], 'imaging') !== false): ?>
                                <i class="fas fa-x-ray"></i>
                            <?php elseif (strpos($document['document type'], 'report') !== false): ?>
                                <i class="fas fa-file-medical-alt"></i>
                            <?php else: ?>
                                <i class="fas fa-file-medical"></i>
                            <?php endif; ?>
                        </div>
                        <div class="document-info">
                            <h4><?php echo htmlspecialchars($document['Document Title']); ?></h4>
                            <p class="document-type"><?php echo htmlspecialchars($document['document type']); ?></p>
                            <p class="document-date"><?php echo date('F j, Y', strtotime($document['Document Date'])); ?></p>
                        </div>
                        <div class="document-actions">
                            <a href="<?php echo htmlspecialchars($document['document_path']); ?>" target="_blank" class="view-btn"><i class="fas fa-eye"></i></a>
                            <a href="<?php echo htmlspecialchars($document['document_path']); ?>" download class="download-btn"><i class="fas fa-download"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="documents-panel" id="upload-document-panel">
                <div class="upload-form">
                    <h3>Upload New Document</h3>
                    <p>Supported formats: PDF, JPG, PNG (Max size: 10MB)</p>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="document-title">Document Title</label>
                            <input type="text" id="document-title" name="document_title" placeholder="e.g., Blood Test Results" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="document-type">Document Type</label>
                            <select id="document-type" name="document_type" required>
                                <option value="">Select document type</option>
                                <option value="lab-result">Lab Result</option>
                                <option value="prescription">Prescription</option>
                                <option value="medical-report">Medical Report</option>
                                <option value="imaging">Imaging (X-ray, MRI, etc.)</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="document-date">Document Date</label>
                            <input type="date" id="document-date" name="document_date" required>
                        </div>
                        
                        <div class="file-upload-container">
                            <div class="file-upload-area" id="file-upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Drag & drop your file here or</p>
                                <button type="button" class="browse-btn" id="browse-files-btn">Browse Files</button>
                                <input type="file" id="file-upload" name="document_file" hidden required>
                            </div>
                            <div class="selected-file" id="selected-file">
                                <p>No file selected</p>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="secondary-btn" id="cancel-upload-btn">Cancel</button>
                            <button type="submit" class="primary-btn" name="upload_document" id="submit-upload-btn">Upload Document</button>
                        </div>
                    </form>
                </div>
            </div>
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
                    <li><a href="#">Book Appointment</a></li>
                    <li><a href="#">Health Assistant</a></li>
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
        // Tab switching functionality
        const allDocsBtn = document.getElementById('all-documents-btn');
        const uploadDocBtn = document.getElementById('upload-document-btn');
        const allDocsPanel = document.getElementById('all-documents-panel');
        const uploadDocPanel = document.getElementById('upload-document-panel');
        const uploadFirstDocBtn = document.getElementById('upload-first-document-btn');
        
        allDocsBtn.addEventListener('click', function() {
            allDocsBtn.classList.add('active');
            uploadDocBtn.classList.remove('active');
            allDocsPanel.classList.add('active');
            uploadDocPanel.classList.remove('active');
        });
        
        uploadDocBtn.addEventListener('click', function() {
            uploadDocBtn.classList.add('active');
            allDocsBtn.classList.remove('active');
            uploadDocPanel.classList.add('active');
            allDocsPanel.classList.remove('active');
        });
        
        if (uploadFirstDocBtn) {
            uploadFirstDocBtn.addEventListener('click', function() {
                uploadDocBtn.click();
            });
        }
        
        // File upload functionality
        const fileUploadArea = document.getElementById('file-upload-area');
        const fileInput = document.getElementById('file-upload');
        const browseBtn = document.getElementById('browse-files-btn');
        const selectedFileDiv = document.getElementById('selected-file');
        const cancelUploadBtn = document.getElementById('cancel-upload-btn');
        
        browseBtn.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });
        
        fileUploadArea.addEventListener('dragleave', function() {
            fileUploadArea.classList.remove('dragover');
        });
        
        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateSelectedFile();
            }
        });
        
        fileInput.addEventListener('change', updateSelectedFile);
        
        function updateSelectedFile() {
            if (fileInput.files.length) {
                const fileName = fileInput.files[0].name;
                const fileSize = (fileInput.files[0].size / 1024).toFixed(2) + ' KB';
                selectedFileDiv.innerHTML = `
                    <div class="file-info">
                        <i class="fas fa-file-alt"></i>
                        <div>
                            <p class="file-name">${fileName}</p>
                            <p class="file-size">${fileSize}</p>
                        </div>
                    </div>
                `;
            } else {
                selectedFileDiv.innerHTML = '<p>No file selected</p>';
            }
        }
        
        cancelUploadBtn.addEventListener('click', function() {
            allDocsBtn.click();
        });
        
        // Profile dropdown
        const profileToggle = document.getElementById('profile-toggle');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        profileToggle.addEventListener('click', function() {
            profileDropdown.classList.toggle('show');
        });
        
        window.addEventListener('click', function(e) {
            if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    });
    </script>
</body>
</html>