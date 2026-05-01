<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'security') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../includes/db.php';
include '../includes/functions.php';

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
$severity = $input['severity'] ?? 'Medium';

$stmt = $conn->prepare("INSERT INTO alerts (message, severity, created_by) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $message, $severity, $_SESSION['user_id']);

if ($stmt->execute()) {
    logAction($conn, $_SESSION['user_id'], $_SESSION['user_email'], 'BROADCAST_ALERT', "Broadcast: $message");
    echo json_encode(['status' => 'success', 'message' => 'Alert broadcasted to all employees']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Broadcast failed']);
}
?>