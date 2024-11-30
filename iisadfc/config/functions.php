<?php
// functions.php

// Function to sanitize user input to prevent SQL injection
function sanitizeInput($data) {
    global $conn; // Access the database connection from db.php
    $data = trim($data); // Remove extra spaces
    $data = stripslashes($data); // Remove backslashes
    $data = htmlspecialchars($data); // Convert special characters to HTML entities
    return $conn->real_escape_string($data); // Escape special characters for SQL queries
}

// Function to validate if a required field is not empty
function validateRequired($field) {
    return !empty($field);
}

// Function to send a standardized error message
function sendError($message) {
    echo json_encode(["status" => "error", "message" => $message]);
    exit();
}

// Function to send a standardized success message
function sendSuccess($message, $data = []) {
    echo json_encode(["status" => "success", "message" => $message, "data" => $data]);
    exit();
}
?>
