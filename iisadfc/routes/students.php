<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // Fetch all students
        $result = $conn->query("SELECT * FROM students");
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        sendSuccess('Students fetched successfully', $students);
        break;

    case 'POST':
        // Add a new student
        $data = json_decode(file_get_contents("php://input"), true);
        $name = sanitizeInput($data['name']);
        $email = sanitizeInput($data['email']);
        
        if (!validateRequired($name) || !validateRequired($email)) {
            sendError("Name and Email are required.");
        }

        $stmt = $conn->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        if ($stmt->execute()) {
            sendSuccess("Student added successfully");
        } else {
            sendError("Failed to add student");
        }
        break;

    case 'PUT':
        // Update student details
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];
        $name = sanitizeInput($data['name']);
        $email = sanitizeInput($data['email']);

        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        if ($stmt->execute()) {
            sendSuccess("Student updated successfully");
        } else {
            sendError("Failed to update student");
        }
        break;

    case 'DELETE':
        // Delete student
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            sendSuccess("Student deleted successfully");
        } else {
            sendError("Failed to delete student");
        }
        break;

    default:
        sendError("Invalid request method");
        break;
}
?>
