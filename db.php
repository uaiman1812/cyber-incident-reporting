<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "cyber_incident_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database Connection Failed: " . $conn->connect_error
    ]));
}

// Set charset
$conn->set_charset("utf8mb4");
?>