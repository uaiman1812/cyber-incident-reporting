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
$incident_id = $input['incident_id'] ?? 0;
$status = $input['status'] ?? '';

$stmt = $conn->prepare("UPDATE incidents SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $incident_id);

if ($stmt->execute()) {
    logAction($conn, $_SESSION['user_id'], $_SESSION['user_email'], 'UPDATE_STATUS', "Updated incident ID $incident_id to $status");
    echo json_encode(['status' => 'success', 'message' => 'Status updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}
?>