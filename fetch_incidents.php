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
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$severity = $_GET['severity'] ?? '';

$sql = "SELECT id, incident_code, incident_type, description, severity, status, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') as created_at FROM incidents";
$params = [];
$types = "";

if ($role == 'employee') {
    $sql .= " WHERE user_id = ?";
    $params[] = $user_id;
    $types .= "i";
}

// Add search filter
if (!empty($search)) {
    $sql .= (strpos($sql, 'WHERE') === false ? ' WHERE' : ' AND') . " (description LIKE ? OR incident_code LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// Add status filter
if (!empty($status)) {
    $sql .= (strpos($sql, 'WHERE') === false ? ' WHERE' : ' AND') . " status = ?";
    $params[] = $status;
    $types .= "s";
}

// Add severity filter
if (!empty($severity)) {
    $sql .= (strpos($sql, 'WHERE') === false ? ' WHERE' : ' AND') . " severity = ?";
    $params[] = $severity;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$incidents = [];
while ($row = $result->fetch_assoc()) {
    $incidents[] = $row;
}

echo json_encode(['status' => 'success', 'incidents' => $incidents]);
?>