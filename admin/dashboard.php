<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$totalAiLogs = $pdo->query("SELECT COUNT(*) FROM ai_logs")->fetchColumn();
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <?php echo $_SESSION['name']; ?>. You are logged in as Admin.</p>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3><?php echo $totalProducts; ?></h3>
                <p>Total Products</p>
            </div>

            <div class="dashboard-card">
                <h3><?php echo $totalOrders; ?></h3>
                <p>Total Orders</p>
            </div>

            <div class="dashboard-card">
                <h3><?php echo $totalUsers; ?></h3>
                <p>Customers</p>
            </div>

            <div class="dashboard-card">
                <h3><?php echo $pendingOrders; ?></h3>
                <p>Pending Orders</p>
            </div>

            <div class="dashboard-card">
                <h3><?php echo $totalAiLogs; ?></h3>
                <p>AI Logs</p>
            </div>
        </div>

        <div class="action-links">
            <a href="products.php" class="btn">Manage Products</a>
            <a href="orders.php" class="btn">Manage Orders</a>
            <a href="users.php" class="btn">View Users</a>
            <a href="activity_logs.php" class="btn">Activity Logs</a>
            <a href="ai_logs.php" class="btn">AI Logs</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>