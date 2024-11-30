<?php

file_put_contents('headers.log', print_r(getallheaders(), true), FILE_APPEND);

require_once '../config/db.php';
require_once '../config/functions.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // Fetch all payments or a specific payment by ID
        if (isset($_GET['payment_id'])) {
            $payment_id = intval($_GET['payment_id']);
            $stmt = $conn->prepare("SELECT * FROM payments WHERE payment_id = ?");
            $stmt->bind_param("i", $payment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $payment = $result->fetch_assoc();

            if ($payment) {
                sendSuccess("Payment fetched successfully", $payment);
            } else {
                sendError("Payment not found");
            }
        } else {
            // Fetch all payments
            $result = $conn->query("SELECT * FROM payments");
            $payments = [];
            while ($row = $result->fetch_assoc()) {
                $payments[] = $row;
            }
            sendSuccess("Payments fetched successfully", $payments);
        }
        break;

    case 'POST':
        // Add a new payment
        $data = json_decode(file_get_contents("php://input"), true);
        $payment_code = sanitizeInput($data['payment_code']);
        $student_id = intval($data['student_id']);
        $amount_paid = floatval($data['amount_paid']);
        $payment_date = date("Y-m-d");
        $payment_method = isset($data['payment_method']) ? sanitizeInput($data['payment_method']) : null;
        $remarks = isset($data['remarks']) ? sanitizeInput($data['remarks']) : null;

        if (!validateRequired($payment_code) || !validateRequired($student_id) || !validateRequired($amount_paid)) {
            sendError("Payment code, student ID, and amount paid are required.");
        }

        $stmt = $conn->prepare("INSERT INTO payments (payment_code, student_id, payment_date, amount_paid, payment_method, remarks) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisiss", $payment_code, $student_id, $payment_date, $amount_paid, $payment_method, $remarks);

        if ($stmt->execute()) {
            sendSuccess("Payment added successfully");
        } else {
            sendError("Failed to add payment");
        }
        break;

    case 'PUT':
        // Update payment details
        parse_str(file_get_contents("php://input"), $data);
        $payment_id = intval($data['payment_id']);
        $amount_paid = floatval($data['amount_paid']);
        $payment_method = isset($data['payment_method']) ? sanitizeInput($data['payment_method']) : null;
        $remarks = isset($data['remarks']) ? sanitizeInput($data['remarks']) : null;

        if (!validateRequired($payment_id) || !validateRequired($amount_paid)) {
            sendError("Payment ID and amount are required.");
        }

        $stmt = $conn->prepare("UPDATE payments SET amount_paid = ?, payment_method = ?, remarks = ? WHERE payment_id = ?");
        $stmt->bind_param("dssi", $amount_paid, $payment_method, $remarks, $payment_id);

        if ($stmt->execute()) {
            sendSuccess("Payment updated successfully");
        } else {
            sendError("Failed to update payment");
        }
        break;

    case 'DELETE':
        // Delete a payment
        parse_str(file_get_contents("php://input"), $data);
        $payment_id = intval($data['payment_id']);

        if (!validateRequired($payment_id)) {
            sendError("Payment ID is required.");
        }

        $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
        $stmt->bind_param("i", $payment_id);

        if ($stmt->execute()) {
            sendSuccess("Payment deleted successfully");
        } else {
            sendError("Failed to delete payment");
        }
        break;

        case 'POST_VALIDATE':
            $input = json_decode(file_get_contents('php://input'), true); // Read JSON input
            $payment_code = $input['payment_code'] ?? '';
        
            if (empty($payment_code)) {
                sendError('Payment code is missing.');
            }
        
            // Validate payment code in the database
            $query = "SELECT * FROM payments WHERE payment_code = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$payment_code]);
            $payment = $stmt->fetch();
        
            if ($payment) {
                sendSuccess('Payment code is valid.', $payment);
            } else {
                sendError('Invalid payment code.');
            }
            break;
        

    default:
        sendError("Invalid request method");
        break;
}
?>
