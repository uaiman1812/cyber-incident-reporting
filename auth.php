<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        header('Location: dashboard.php');
        exit();
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

function getCurrentUserName() {
    return $_SESSION['full_name'] ?? 'User';
}

function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? '';
}
?>