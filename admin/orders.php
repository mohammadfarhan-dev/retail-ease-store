<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) && is_numeric($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
    $newStatus = sanitizeInput($_POST['status'] ?? '');

    if ($orderId <= 0 || !in_array($newStatus, ['pending', 'approved', 'rejected', 'completed'])) {
        $error = "Invalid order update request.";
    } else {
        $orderStmt = $pdo->prepare("
            SELECT orders.*, products.name AS product_name, products.stock
            FROM orders
            INNER JOIN products ON orders.product_id = products.id
            WHERE orders.id = ?
        ");
        $orderStmt->execute([$orderId]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $error = "Order not found.";
        } else {
            $oldStatus = $order['status'];

            $stockAllocatedStatuses = ['approved', 'completed'];
            $oldAllocated = in_array($oldStatus, $stockAllocatedStatuses);
            $newAllocated = in_array($newStatus, $stockAllocatedStatuses);

            try {
                $pdo->beginTransaction();

                if (!$oldAllocated && $newAllocated) {
                    if ($order['stock'] < $order['quantity']) {
                        throw new Exception("Not enough stock available to approve or complete this order.");
                    }

                    $stockUpdate = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    $stockUpdate->execute([$order['quantity'], $order['product_id']]);
                }

                if ($oldAllocated && !$newAllocated) {
                    $stockRestore = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                    $stockRestore->execute([$order['quantity'], $order['product_id']]);
                }

                $update = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $update->execute([$newStatus, $orderId]);

                logActivity(
                    $pdo,
                    $_SESSION['user_id'],
                    'Order Status Updated',
                    'Admin changed order #' . $orderId . ' from ' . $oldStatus . ' to ' . $newStatus
                );

                $pdo->commit();
                $success = "Order status updated successfully.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = $e->getMessage();
            }
        }
    }
}

$statusFilter = sanitizeInput($_GET['status'] ?? '');

$where = "";
$params = [];

if (!empty($statusFilter) && in_array($statusFilter, ['pending', 'approved', 'rejected', 'completed'])) {
    $where = "WHERE orders.status = ?";
    $params[] = $statusFilter;
}

$stmt = $pdo->prepare("
    SELECT 
        orders.*,
        users.name AS customer_name,
        users.email AS customer_email,
        products.name AS product_name,
        products.category
    FROM orders
    INNER JOIN users ON orders.user_id = users.id
    INNER JOIN products ON orders.product_id = products.id
    $where
    ORDER BY orders.created_at DESC
");
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <h2>Manage Orders</h2>
                <p>View customer orders and update order status.</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="GET" class="filter-form order-filter">
            <select name="status">
                <option value="">All Orders</option>
                <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>

            <button type="submit">Filter</button>
            <a href="orders.php" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Current Status</th>
                        <th>Update Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                    <br>
                                    <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['product_name']); ?></strong>
                                    <br>
                                    <small><?php echo htmlspecialchars($order['category']); ?></small>
                                </td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo htmlspecialchars($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" class="status-form">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

                                        <select name="status">
                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo $order['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo $order['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        </select>

                                        <button type="submit" class="btn-small-action">Update</button>
                                    </form>
                                </td>
                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">No orders found.</td>
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