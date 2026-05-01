<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit();
}

include '../includes/db.php';
include '../includes/functions.php';

$employee_name = trim($_POST['employee_name']);
$employee_email = trim($_POST['employee_email']);
$department = trim($_POST['department']);
$incident_type = trim($_POST['incident_type']);
$severity = trim($_POST['severity']);
$description = trim($_POST['description']);
$user_id = $_SESSION['user_id'];

// AI suggestion (store what AI would have suggested)
$ai_suggested = aiSuggestIncidentType($description);

// Generate unique incident code
$incident_code = generateIncidentCode();

$stmt = $conn->prepare("INSERT INTO incidents (incident_code, user_id, employee_name, employee_email, department, incident_type, severity, description, status, ai_suggested_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Reported', ?)");
$stmt->bind_param("sisssssss", $incident_code, $user_id, $employee_name, $employee_email, $department, $incident_type, $severity, $description, $ai_suggested);

if ($stmt->execute()) {
    logAction($conn, $user_id, $employee_email, 'REPORT_INCIDENT', "Reported incident: $incident_code - Type: $incident_type");
    echo json_encode([
        'status' => 'success',
        'incident_code' => $incident_code,
        'message' => "Incident reported successfully! Tracking ID: $incident_code. (AI Suggested: $ai_suggested)"
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Submission failed. Please try again.']);
}
?>