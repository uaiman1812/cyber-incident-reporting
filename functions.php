<?php
// AI Suggestion Engine (Keyword-based)
function aiSuggestIncidentType($description) {
    $desc = strtolower($description);
    
    $patterns = [
        'Phishing' => ['link', 'click', 'email', 'password', 'login', 'account', 'bank', 'otp', 'verify', 'urgent', 'fake', 'spoof'],
        'Malware' => ['virus', 'malware', 'ransomware', 'trojan', 'worm', 'spyware', 'encrypt', 'popup', 'attachment', 'infected', 'bitcoin'],
        'Unauthorized Access' => ['unauthorized', 'unknown', 'unrecognized', 'strange', 'suspicious', 'login attempt', 'failed login', 'brute force', 'hacked', 'breach'],
        'Data Leak' => ['leak', 'exposed', 'stolen', 'confidential', 'sensitive', 'data breach', 'shared externally'],
        'DoS Attack' => ['slow', 'unresponsive', 'timeout', 'flood', 'downtime', 'network spike']
    ];
    
    foreach ($patterns as $type => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($desc, $keyword) !== false) {
                return $type;
            }
        }
    }
    return 'Other';
}

// Generate unique incident code
function generateIncidentCode() {
    return 'INC-' . date('Ymd') . '-' . rand(1000, 9999);
}

// Get status badge class
function getStatusBadgeClass($status) {
    $classes = [
        'Reported' => 'badge-warning',
        'Under Review' => 'badge-info',
        'Investigating' => 'badge-primary',
        'Resolved' => 'badge-success',
        'False Alarm' => 'badge-secondary'
    ];
    return $classes[$status] ?? 'badge-secondary';
}

// Get severity badge class
function getSeverityBadgeClass($severity) {
    $classes = [
        'Low' => 'badge-low',
        'Medium' => 'badge-medium',
        'High' => 'badge-high',
        'Critical' => 'badge-critical'
    ];
    return $classes[$severity] ?? 'badge-medium';
}

// Log user action
function logAction($conn, $user_id, $user_email, $action, $details) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, user_email, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $user_email, $action, $details, $ip);
    return $stmt->execute();
}
?>