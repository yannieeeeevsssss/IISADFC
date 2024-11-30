<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // Fetch all courses
        $result = $conn->query("SELECT * FROM courses");
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        sendSuccess('Courses fetched successfully', $courses);
        break;

    case 'POST':
        // Add a new course
        $data = json_decode(file_get_contents("php://input"), true);
        $course_name = sanitizeInput($data['course_name']);
        
        if (!validateRequired($course_name)) {
            sendError("Course name is required.");
        }

        $stmt = $conn->prepare("INSERT INTO courses (course_name) VALUES (?)");
        $stmt->bind_param("s", $course_name);
        if ($stmt->execute()) {
            sendSuccess("Course added successfully");
        } else {
            sendError("Failed to add course");
        }
        break;

    default:
        sendError("Invalid request method");
        break;
}
?>
