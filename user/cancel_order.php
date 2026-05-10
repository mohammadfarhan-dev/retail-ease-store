<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] === 'admin') {
    redirect('/retail-ease-store/admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/retail-ease-store/user/my_orders.php');
}

$orderId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;

if ($orderId <= 0) {
    redirect('/retail-ease-store/user/my_orders.php');
}

$stmt = $pdo->prepare("
    SELECT orders.*, products.name AS product_name
    FROM orders
    INNER JOIN products ON orders.product_id = products.id
    WHERE orders.id = ? AND orders.user_id = ? AND orders.status = 'pending'
");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    $delete = $pdo->prepare("DELETE FROM orders WHERE id = ? AND user_id = ? AND status = 'pending'");
    $delete->execute([$orderId, $_SESSION['user_id']]);

    logActivity(
        $pdo,
        $_SESSION['user_id'],
        'Order Cancelled',
        $_SESSION['name'] . ' cancelled order for ' . $order['product_name']
    );
}

redirect('/retail-ease-store/user/my_orders.php');
?>