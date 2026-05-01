<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../includes/functions.php';

$input = json_decode(file_get_contents('php://input'), true);
$description = $input['description'] ?? '';

$suggested_type = aiSuggestIncidentType($description);
$confidence = ($suggested_type != 'Other') ? 'high' : 'low';

echo json_encode([
    'status' => 'success',
    'suggested_type' => $suggested_type,
    'confidence' => $confidence,
    'original_description' => substr($description, 0, 50)
]);
?>