<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // Fetch all school years
        $result = $conn->query("SELECT * FROM school_year");
        $schoolYears = [];
        while ($row = $result->fetch_assoc()) {
            $schoolYears[] = $row;
        }
        sendSuccess('School years fetched successfully', $schoolYears);
        break;

    case 'POST':
        // Add a new school year
        $data = json_decode(file_get_contents("php://input"), true);
        $school_year = sanitizeInput($data['school_year']);
        $start_date = sanitizeInput($data['start_date']);
        $end_date = sanitizeInput($data['end_date']);
        
        if (!validateRequired($school_year) || !validateRequired($start_date) || !validateRequired($end_date)) {
            sendError("All fields are required.");
        }

        $stmt = $conn->prepare("INSERT INTO school_year (school_year, start_date, end_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $school_year, $start_date, $end_date);
        if ($stmt->execute()) {
            sendSuccess("School year added successfully");
        } else {
            sendError("Failed to add school year");
        }
        break;

    default:
        sendError("Invalid request method");
        break;
}
?>
