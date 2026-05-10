<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RetailEase Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/retail-ease-store/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container nav-container">
        <a href="/retail-ease-store/index.php" class="logo" aria-label="RetailEase Store Home">
            <img src="/retail-ease-store/assets/images/retailease-logo.png" alt="RetailEase Store Logo">
        </a>

        <button class="nav-toggle" id="navToggle" type="button" aria-label="Open navigation menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="nav-links" id="navMenu">
            <a href="/retail-ease-store/index.php">Home</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/retail-ease-store/admin/dashboard.php">Admin Dashboard</a>
                <?php else: ?>
                    <a href="/retail-ease-store/user/dashboard.php">My Dashboard</a>
                    <a href="/retail-ease-store/user/products.php">Products</a>
                    <a href="/retail-ease-store/user/my_orders.php">My Orders</a>
                    <a href="/retail-ease-store/user/smart_assistant.php">Smart Assistant</a>
                <?php endif; ?>

                <a href="/retail-ease-store/logout.php" class="btn-small">Logout</a>
            <?php else: ?>
                <a href="/retail-ease-store/login.php">Login</a>
                <a href="/retail-ease-store/register.php" class="btn-small">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>