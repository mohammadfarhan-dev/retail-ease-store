<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header("Location: " . $path);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/retail-ease-store/login.php');
    }
}

function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        redirect('/retail-ease-store/login.php');
    }
}

function logActivity($pdo, $userId, $action, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $action, $details]);
    } catch (PDOException $e) {
        // Silent fail so activity logging does not break the main app
    }
}
?>