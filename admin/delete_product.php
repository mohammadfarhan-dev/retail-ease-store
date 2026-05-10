<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/retail-ease-store/admin/products.php');
}

$id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    redirect('/retail-ease-store/admin/products.php');
}

$stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    $delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $delete->execute([$id]);

    logActivity($pdo, $_SESSION['user_id'], 'Product Deleted', 'Admin deleted product: ' . $product['name']);
}

redirect('/retail-ease-store/admin/products.php');
?>