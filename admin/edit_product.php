<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    redirect('/retail-ease-store/admin/products.php');
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    redirect('/retail-ease-store/admin/products.php');
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $status = sanitizeInput($_POST['status'] ?? 'active');

    if (empty($name) || empty($description) || empty($category) || $price === '' || $stock === '') {
        $error = "All fields are required.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a valid positive number.";
    } elseif (!is_numeric($stock) || $stock < 0) {
        $error = "Stock must be a valid number.";
    } elseif (!in_array($status, ['active', 'inactive'])) {
        $error = "Invalid product status.";
    } else {
        $update = $pdo->prepare("
            UPDATE products 
            SET name = ?, description = ?, category = ?, price = ?, stock = ?, status = ?, updated_by = ?
            WHERE id = ?
        ");

        $update->execute([
            $name,
            $description,
            $category,
            $price,
            $stock,
            $status,
            $_SESSION['user_id'],
            $id
        ]);

        logActivity($pdo, $_SESSION['user_id'], 'Product Updated', 'Admin updated product: ' . $name);

        $success = "Product updated successfully.";

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="form-box wide-form">
            <h2>Edit Product</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" onsubmit="return validateProductForm();">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" id="category" value="<?php echo htmlspecialchars($product['category']); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($product['stock']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="full-btn">Update Product</button>
            </form>

            <p class="form-link">
                <a href="products.php">← Back to Products</a>
            </p>
        </div>
    </div>
</section>

<script>
function validateProductForm() {
    let name = document.getElementById("name").value.trim();
    let description = document.getElementById("description").value.trim();
    let category = document.getElementById("category").value.trim();
    let price = document.getElementById("price").value.trim();
    let stock = document.getElementById("stock").value.trim();

    if (name === "" || description === "" || category === "" || price === "" || stock === "") {
        alert("Please fill all product fields.");
        return false;
    }

    if (parseFloat(price) <= 0) {
        alert("Price must be greater than 0.");
        return false;
    }

    if (parseInt(stock) < 0) {
        alert("Stock cannot be negative.");
        return false;
    }

    return true;
}
</script>

<?php include '../includes/footer.php'; ?>