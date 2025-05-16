<?php
session_start();

// Function to sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to redirect user
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to display error message
function display_error($message) {
    return "<div class='alert alert-danger'>$message</div>";
}

// Function to display success message
function display_success($message) {
    return "<div class='alert alert-success'>$message</div>";
}

// Function to validate email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?> 