<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    logActivity($pdo, $_SESSION['user_id'], 'Logout', $_SESSION['name'] . ' logged out.');
}

session_unset();
session_destroy();

redirect('/retail-ease-store/login.php');
?>