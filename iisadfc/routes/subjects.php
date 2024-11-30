<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // Fetch all subjects
        $result = $conn->query("SELECT * FROM subjects");
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
        sendSuccess('Subjects fetched successfully', $subjects);
        break;

    case 'POST':
        // Add a new subject
        $data = json_decode(file_get_contents("php://input"), true);
        $subject_name = sanitizeInput($data['subject_name']);
        
        if (!validateRequired($subject_name)) {
            sendError("Subject name is required.");
        }

        $stmt = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
        $stmt->bind_param("s", $subject_name);
        if ($stmt->execute()) {
            sendSuccess("Subject added successfully");
        } else {
            sendError("Failed to add subject");
        }
        break;

    default:
        sendError("Invalid request method");
        break;
}
?>
