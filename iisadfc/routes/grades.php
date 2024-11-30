<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // Fetch all grades or grades by student ID or subject ID
        if (isset($_GET['student_id'])) {
            // Fetch grades for a specific student
            $student_id = $_GET['student_id'];
            $stmt = $conn->prepare("SELECT * FROM grades WHERE student_id = ?");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $grades = [];
            while ($row = $result->fetch_assoc()) {
                $grades[] = $row;
            }
            sendSuccess('Grades fetched successfully', $grades);
        } else if (isset($_GET['subject_id'])) {
            // Fetch grades for a specific subject
            $subject_id = $_GET['subject_id'];
            $stmt = $conn->prepare("SELECT * FROM grades WHERE subject_id = ?");
            $stmt->bind_param("i", $subject_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $grades = [];
            while ($row = $result->fetch_assoc()) {
                $grades[] = $row;
            }
            sendSuccess('Grades fetched successfully', $grades);
        } else {
            // Fetch all grades
            $result = $conn->query("SELECT * FROM grades");
            $grades = [];
            while ($row = $result->fetch_assoc()) {
                $grades[] = $row;
            }
            sendSuccess('Grades fetched successfully', $grades);
        }
        break;

    case 'POST':
        // Add a new grade (create operation)
        $data = json_decode(file_get_contents("php://input"), true);
        $student_id = $data['student_id'];
        $subject_id = $data['subject_id'];
        $grade = $data['grade'];

        if (!validateRequired($student_id) || !validateRequired($subject_id) || !validateRequired($grade)) {
            sendError("Student ID, Subject ID, and Grade are required.");
        }

        $stmt = $conn->prepare("INSERT INTO grades (student_id, subject_id, grade) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $student_id, $subject_id, $grade);
        if ($stmt->execute()) {
            sendSuccess("Grade added successfully");
        } else {
            sendError("Failed to add grade");
        }
        break;

    case 'PUT':
        // Update an existing grade (update operation)
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];
        $grade = $data['grade'];

        if (!validateRequired($id) || !validateRequired($grade)) {
            sendError("Grade ID and Grade are required.");
        }

        $stmt = $conn->prepare("UPDATE grades SET grade = ? WHERE id = ?");
        $stmt->bind_param("si", $grade, $id);
        if ($stmt->execute()) {
            sendSuccess("Grade updated successfully");
        } else {
            sendError("Failed to update grade");
        }
        break;

    case 'DELETE':
        // Delete a grade record
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];

        if (!validateRequired($id)) {
            sendError("Grade ID is required.");
        }

        $stmt = $conn->prepare("DELETE FROM grades WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            sendSuccess("Grade deleted successfully");
        } else {
            sendError("Failed to delete grade");
        }
        break;

    default:
        sendError("Invalid request method");
        break;
}
?>
