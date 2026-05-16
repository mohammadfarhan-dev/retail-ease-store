<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] === 'admin') {
    redirect('/retail-ease-store/admin/dashboard.php');
}
// Responsive customer dashboard layout
$userId = $_SESSION['user_id'];

$totalOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$totalOrders->execute([$userId]);
$totalOrders = $totalOrders->fetchColumn();

$pendingOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'pending'");
$pendingOrders->execute([$userId]);
$pendingOrders = $pendingOrders->fetchColumn();

$approvedOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'approved'");
$approvedOrders->execute([$userId]);
$approvedOrders = $approvedOrders->fetchColumn();
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <h2>My Dashboard</h2>
        <p>Welcome, <?php echo $_SESSION['name']; ?>. You are logged in as Customer.</p>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3><?php echo $totalOrders; ?></h3>
                <p>My Orders</p>
            </div>

            <div class="dashboard-card">
                <h3><?php echo $pendingOrders; ?></h3>
                <p>Pending Orders</p>
            </div>

            <div class="dashboard-card">
                <h3><?php echo $approvedOrders; ?></h3>
                <p>Approved Orders</p>
            </div>
        </div>

        <div class="action-links">
            <a href="products.php" class="btn">Browse Products</a>
            <a href="my_orders.php" class="btn">View My Orders</a>
            <a href="smart_assistant.php" class="btn">Smart Assistant</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>