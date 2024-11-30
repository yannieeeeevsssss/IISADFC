<?php
// Start session (if needed for authentication or user management)
session_start();

// Include common configuration
require_once 'config/db.php';        // Database connection
require_once 'config/functions.php'; // Helper functions (optional, create if needed)

// Set response headers
header('Content-Type: application/json');

// Parse the request URI and HTTP method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Function to send a JSON response
function sendResponse($status, $message, $data = []) {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

// Routing logic
switch (true) {
    // === student table ===
    case strpos($requestUri, '/api/student') === 0:
        require_once 'routes/student.php';
        break;

    // === PAYMENTs table ===
    case strpos($requestUri, '/api/payments') === 0:
        require_once 'routes/payments.php';
        break;

    // === GRADE table ===
    case strpos($requestUri, '/api/grades') === 0:
        require_once 'routes/grades.php';
        break;

    // === courses table ===
    case strpos($requestUri, '/api/courses') === 0:
        require_once 'routes/courses.php';
        break;

    // === courses table ===
    case strpos($requestUri, '/api/subjects') === 0:
        require_once 'routes/subjects.php';
        break;

    // === sy table ===
    case strpos($requestUri, '/api/sy') === 0:
        require_once 'routes/sy.php';
        break;


    // === DEFAULT: INVALID ROUTE ===
    default:
        http_response_code(404);
        sendResponse('error', 'API endpoint not found');
        break;
}
?>
