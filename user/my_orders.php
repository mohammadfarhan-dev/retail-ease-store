<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] === 'admin') {
    redirect('/retail-ease-store/admin/dashboard.php');
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT orders.*, products.name AS product_name, products.category
    FROM orders
    INNER JOIN products ON orders.product_id = products.id
    WHERE orders.user_id = ?
    ORDER BY orders.created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <h2>My Orders</h2>
                <p>View your submitted product orders and their current status.</p>
            </div>
            <a href="products.php" class="btn">Browse Products</a>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['category']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo htmlspecialchars($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <form method="POST" action="cancel_order.php" onsubmit="return confirm('Cancel this pending order?');">
                                            <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" class="btn-danger-small">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="muted-text">No action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">You have not placed any orders yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="back-link">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>