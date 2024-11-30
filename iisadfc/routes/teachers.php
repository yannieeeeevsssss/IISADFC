<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // Fetch all teachers
        $result = $conn->query("SELECT * FROM teachers");
        $teachers = [];
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
        sendSuccess('Teachers fetched successfully', $teachers);
        break;

    case 'POST':
        // Add a new teacher
        $data = json_decode(file_get_contents("php://input"), true);
        $name = sanitizeInput($data['name']);
        $email = sanitizeInput($data['email']);
        
        if (!validateRequired($name) || !validateRequired($email)) {
            sendError("Name and Email are required.");
        }

        $stmt = $conn->prepare("INSERT INTO teachers (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        if ($stmt->execute()) {
            sendSuccess("Teacher added successfully");
        } else {
            sendError("Failed to add teacher");
        }
        break;

    default:
        sendError("Invalid request method");
        break;
}
?>
