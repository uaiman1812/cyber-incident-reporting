<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../includes/db.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if ($role == 'employee') {
    $stmt = $conn->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Reported' THEN 1 ELSE 0 END) as reported, SUM(CASE WHEN status = 'Investigating' THEN 1 ELSE 0 END) as investigating, SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved FROM incidents WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Reported' THEN 1 ELSE 0 END) as reported, SUM(CASE WHEN status = 'Investigating' THEN 1 ELSE 0 END) as investigating, SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved FROM incidents");
}

$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

echo json_encode(['status' => 'success', ...$stats]);
?>