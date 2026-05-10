<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] === 'admin') {
    redirect('/retail-ease-store/admin/dashboard.php');
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    redirect('/retail-ease-store/user/products.php');
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    redirect('/retail-ease-store/user/products.php');
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? (int) $_POST['quantity'] : 0;
    $orderNote = sanitizeInput($_POST['order_note'] ?? '');

    if ($quantity <= 0) {
        $error = "Quantity must be at least 1.";
    } elseif ($quantity > $product['stock']) {
        $error = "Requested quantity is more than available stock.";
    } else {
        $totalPrice = $quantity * $product['price'];

        $insert = $pdo->prepare("
            INSERT INTO orders (user_id, product_id, quantity, total_price, status, order_note)
            VALUES (?, ?, ?, ?, 'pending', ?)
        ");

        $insert->execute([
            $_SESSION['user_id'],
            $product['id'],
            $quantity,
            $totalPrice,
            $orderNote
        ]);

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            'Order Created',
            $_SESSION['name'] . ' placed an order for ' . $product['name']
        );

        $success = "Order placed successfully. Your order is now pending admin approval.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="details-card">
            <div class="product-detail-image">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <span>🛍️</span>
                <?php endif; ?>
            </div>

            <div class="details-content">
                <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>

                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                <div class="details-meta">
                    <div>
                        <strong>Price</strong>
                        <span>$<?php echo number_format($product['price'], 2); ?></span>
                    </div>

                    <div>
                        <strong>Available Stock</strong>
                        <span><?php echo $product['stock']; ?></span>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" class="order-form" onsubmit="return validateOrderForm();">
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $product['stock']; ?>" value="1">
                    </div>

                    <div class="form-group">
                        <label>Order Note Optional</label>
                        <textarea name="order_note" rows="3" placeholder="Add any note for the admin..."></textarea>
                    </div>

                    <button type="submit" class="full-btn">Place Order</button>
                </form>

                <p class="form-link">
                    <a href="products.php">← Back to Products</a>
                </p>
            </div>
        </div>
    </div>
</section>

<script>
function validateOrderForm() {
    let quantity = document.getElementById("quantity").value.trim();

    if (quantity === "" || parseInt(quantity) <= 0) {
        alert("Please enter a valid quantity.");
        return false;
    }

    return true;
}
</script>

<?php include '../includes/footer.php'; ?>