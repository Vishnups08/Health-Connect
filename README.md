# HealthConnect - Healthcare Management System

![HealthConnect](https://img.shields.io/badge/HealthConnect-1.0-8a6bff)
![PHP](https://img.shields.io/badge/PHP-8.2.4-blue)
![MySQL](https://img.shields.io/badge/MySQL-10.4.28-orange)

HealthConnect is a comprehensive web-based healthcare management system designed to connect patients with healthcare professionals. It provides a secure platform for patients to manage their medical documents, book appointments, and interact with an AI health assistant.

## 🌟 Features

### For Patients
- **Patient Dashboard**: View upcoming appointments and manage your healthcare journey in one place
- **Medical Documents**: Upload, view, and download medical records securely with document categorization
- **Appointment Booking**: Schedule appointments with healthcare professionals based on specialty and availability
- **Health Assistant**: Chat with an AI health assistant for quick guidance and answers to health-related questions

### For Healthcare Professionals
- **Doctor Dashboard**: Manage patient appointments and view patient information
- **Patient Management**: Access a list of all patients and their medical history
- **Appointment Management**: View and manage upcoming appointments

## 🛠️ Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 8.2.4
- **Database**: MySQL 10.4.28 (MariaDB)
- **Server**: Apache (XAMPP)
- **Libraries**: Font Awesome 6.0.0-beta3

## 🔧 System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server
- Web browser with JavaScript enabled

## 📋 Setup

1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd health
   ```

2. **Database Configuration**:

- Create a MySQL database named health_system
- Import the provided SQL schema from health_system.sql
- Update the database connection settings in config/database.php

3. **Web Server Configuration**:

- Ensure your web server (e.g., Apache) is running
-Place the project files in your web server's document root (e.g., /Applications/XAMPP/xamppfiles/htdocs/health)


🚀 Usage
Patient Portal
- Login: Use your patient credentials to log in
- Dashboard: View your upcoming appointments and access various services
- Medical Documents: Upload and manage your medical records with document categorization
- Book Appointments: Schedule new appointments with healthcare professionals based on specialty
- Health Assistant: Get quick answers to health-related questions
Doctor Portal
- Login: Use your doctor credentials to log in
- Dashboard: View upcoming appointments and patient statistics
- Patient Management: Access and manage patient information
🔒 Security
- All user passwords are securely hashed using PHP's password_hash function
- Ensure proper permissions are set for the uploads directory to prevent unauthorized access
- Use secure passwords and keep your session information private
- Input validation is implemented to prevent SQL injection and XSS attacks
  
📁 Project Structure

/health
├── config/           # Configuration files
├── includes/         # PHP helper functions
├── uploads/          # User uploaded files
│   └── medical_documents/  # Patient medical documents
├── *.php             # PHP application files
├── *.css             # CSS stylesheets
├── *.js              # JavaScript files
└── health_system.sql # Database schema

📞 Contact
For any questions or support, please contact psvishnu888@gmail.com.
