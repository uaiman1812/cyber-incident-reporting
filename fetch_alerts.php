<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Get unread alerts
$stmt = $conn->prepare("
    SELECT a.id, a.message, a.severity, DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s') as created_at 
    FROM alerts a
    LEFT JOIN alert_reads ar ON a.id = ar.alert_id AND ar.user_id = ?
    WHERE ar.is_read IS NULL OR ar.is_read = 0
    ORDER BY a.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$alerts = [];
while ($row = $result->fetch_assoc()) {
    $alerts[] = $row;
    // Mark as read automatically when fetched
    $markStmt = $conn->prepare("INSERT INTO alert_reads (alert_id, user_id, is_read, read_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE is_read = 1, read_at = NOW()");
    $markStmt->bind_param("ii", $row['id'], $user_id);
    $markStmt->execute();
}

echo json_encode(['status' => 'success', 'alerts' => $alerts, 'unread_count' => count($alerts)]);
?>