<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitizeInput($_POST['action'] ?? '');

    if ($action === 'add') {
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $category = sanitizeInput($_POST['category'] ?? '');
        $price = $_POST['price'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $status = sanitizeInput($_POST['status'] ?? 'active');
        $image = sanitizeInput($_POST['image'] ?? '');

        if (empty($name) || empty($description) || empty($category) || $price === '' || $stock === '') {
            $error = "All product fields are required.";
        } elseif (!is_numeric($price) || $price <= 0) {
            $error = "Price must be a valid positive number.";
        } elseif (!is_numeric($stock) || $stock < 0) {
            $error = "Stock must be a valid number.";
        } elseif (!in_array($status, ['active', 'inactive'])) {
            $error = "Invalid product status.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO products 
                (name, description, category, price, stock, image, status, created_by, updated_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $description,
                $category,
                $price,
                $stock,
                $image,
                $status,
                $_SESSION['user_id'],
                $_SESSION['user_id']
            ]);

            logActivity($pdo, $_SESSION['user_id'], 'Product Created', 'Admin created product: ' . $name);
            $success = "Product added successfully.";
        }
    }

    if ($action === 'edit') {
        $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $category = sanitizeInput($_POST['category'] ?? '');
        $price = $_POST['price'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $status = sanitizeInput($_POST['status'] ?? 'active');
        $image = sanitizeInput($_POST['image'] ?? '');

        if ($id <= 0) {
            $error = "Invalid product selected.";
        } elseif (empty($name) || empty($description) || empty($category) || $price === '' || $stock === '') {
            $error = "All product fields are required.";
        } elseif (!is_numeric($price) || $price <= 0) {
            $error = "Price must be a valid positive number.";
        } elseif (!is_numeric($stock) || $stock < 0) {
            $error = "Stock must be a valid number.";
        } elseif (!in_array($status, ['active', 'inactive'])) {
            $error = "Invalid product status.";
        } else {
            $stmt = $pdo->prepare("
                UPDATE products 
                SET name = ?, description = ?, category = ?, price = ?, stock = ?, image = ?, status = ?, updated_by = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $description,
                $category,
                $price,
                $stock,
                $image,
                $status,
                $_SESSION['user_id'],
                $id
            ]);

            logActivity($pdo, $_SESSION['user_id'], 'Product Updated', 'Admin updated product: ' . $name);
            $success = "Product updated successfully.";
        }
    }

    if ($action === 'delete') {
        $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $delete->execute([$id]);

                logActivity($pdo, $_SESSION['user_id'], 'Product Deleted', 'Admin deleted product: ' . $product['name']);
                $success = "Product deleted successfully.";
            }
        }
    }
}

$search = sanitizeInput($_GET['search'] ?? '');
$category = sanitizeInput($_GET['category'] ?? '');
$status = sanitizeInput($_GET['status'] ?? '');
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $where[] = "category = ?";
    $params[] = $category;
}

if (!empty($status)) {
    $where[] = "status = ?";
    $params[] = $status;
}

$whereSql = "";
if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products $whereSql");
$countStmt->execute($params);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

$stmt = $pdo->prepare("SELECT * FROM products $whereSql ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catStmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <h2>Manage Products</h2>
                <p>Add, edit, delete, search, filter, and manage product records.</p>
            </div>
            <button type="button" onclick="openModal('addProductModal')">+ Add New Product</button>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">

            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                        <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="">All Status</option>
                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>

            <button type="submit">Filter</button>
            <a href="products.php" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>#<?php echo $product['id']; ?></td>

                                <td>
                                    <div class="admin-product-cell">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="admin-product-thumb" alt="Product image">
                                        <?php else: ?>
                                            <div class="admin-product-placeholder">🛍️</div>
                                        <?php endif; ?>

                                        <div>
                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            <br>
                                            <small><?php echo htmlspecialchars(substr($product['description'], 0, 70)); ?>...</small>
                                        </div>
                                    </div>
                                </td>

                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <span class="badge <?php echo $product['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo ucfirst($product['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($product['created_at'])); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <button 
                                            type="button"
                                            class="btn-small-action"
                                            onclick="openEditModal(this)"
                                            data-id="<?php echo $product['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>"
                                            data-description="<?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?>"
                                            data-category="<?php echo htmlspecialchars($product['category'], ENT_QUOTES); ?>"
                                            data-price="<?php echo htmlspecialchars($product['price'], ENT_QUOTES); ?>"
                                            data-stock="<?php echo htmlspecialchars($product['stock'], ENT_QUOTES); ?>"
                                            data-status="<?php echo htmlspecialchars($product['status'], ENT_QUOTES); ?>"
                                            data-image="<?php echo htmlspecialchars($product['image'] ?? '', ENT_QUOTES); ?>"
                                        >
                                            Edit
                                        </button>

                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn-danger-small">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a 
                        class="<?php echo $i === $page ? 'active-page' : ''; ?>"
                        href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </div>
</section>

<!-- ADD PRODUCT MODAL -->
<div class="modal" id="addProductModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Product</h3>
            <button type="button" class="modal-close" onclick="closeModal('addProductModal')">×</button>
        </div>

        <form method="POST" onsubmit="return validateProductModal('add');">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" id="add_name" placeholder="Enter product name">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="add_description" rows="4" placeholder="Enter product description"></textarea>
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="add_category" placeholder="Example: Electronics">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" id="add_price" placeholder="Example: 25.99">
                </div>

                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" id="add_stock" placeholder="Example: 20">
                </div>
            </div>

            <div class="form-group">
                <label>Product Image URL</label>
                <input type="url" name="image" id="add_image" placeholder="Paste product image URL">
                <div class="image-help">Use image URL for now. This keeps hosting simple and professional.</div>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="add_status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" class="full-btn">Save Product</button>
        </form>
    </div>
</div>

<!-- EDIT PRODUCT MODAL -->
<div class="modal" id="editProductModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Product</h3>
            <button type="button" class="modal-close" onclick="closeModal('editProductModal')">×</button>
        </div>

        <form method="POST" onsubmit="return validateProductModal('edit');">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" id="edit_name">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="edit_description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="edit_category">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" id="edit_price">
                </div>

                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" id="edit_stock">
                </div>
            </div>

            <div class="form-group">
                <label>Product Image URL</label>
                <input type="url" name="image" id="edit_image">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" class="full-btn">Update Product</button>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('open');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

function openEditModal(button) {
    document.getElementById('edit_id').value = button.dataset.id;
    document.getElementById('edit_name').value = button.dataset.name;
    document.getElementById('edit_description').value = button.dataset.description;
    document.getElementById('edit_category').value = button.dataset.category;
    document.getElementById('edit_price').value = button.dataset.price;
    document.getElementById('edit_stock').value = button.dataset.stock;
    document.getElementById('edit_status').value = button.dataset.status;
    document.getElementById('edit_image').value = button.dataset.image;

    openModal('editProductModal');
}

function validateProductModal(type) {
    let name = document.getElementById(type + '_name').value.trim();
    let description = document.getElementById(type + '_description').value.trim();
    let category = document.getElementById(type + '_category').value.trim();
    let price = document.getElementById(type + '_price').value.trim();
    let stock = document.getElementById(type + '_stock').value.trim();

    if (name === "" || description === "" || category === "" || price === "" || stock === "") {
        alert("Please fill all required product fields.");
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.classList.remove('open');
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>