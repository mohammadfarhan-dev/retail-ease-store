<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] === 'admin') {
    redirect('/retail-ease-store/admin/dashboard.php');
}

$search = sanitizeInput($_GET['search'] ?? '');
$category = sanitizeInput($_GET['category'] ?? '');
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$where = ["status = 'active'"];
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

$whereSql = "WHERE " . implode(" AND ", $where);

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products $whereSql");
$countStmt->execute($params);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

$stmt = $pdo->prepare("SELECT * FROM products $whereSql ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catStmt = $pdo->query("SELECT DISTINCT category FROM products WHERE status = 'active' ORDER BY category ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <h2>Product Catalogue</h2>
                <p>Browse available products, search by keyword, filter by category, and place an order.</p>
            </div>
        </div>

        <form method="GET" class="filter-form customer-filter">
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

            <button type="submit">Search</button>
            <a href="products.php" class="btn btn-light">Reset</a>
        </form>

        <?php if (count($products) > 0): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <span>🛍️</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-card-body">
                            <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($product['description'], 0, 105)); ?>...</p>

                            <div class="product-meta">
                                <strong>$<?php echo number_format($product['price'], 2); ?></strong>
                                <span><?php echo $product['stock']; ?> in stock</span>
                            </div>

                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn full-btn">View & Order</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-card">
                <h3>No products found</h3>
                <p>Try changing your search or filter options.</p>
            </div>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a
                        class="<?php echo $i === $page ? 'active-page' : ''; ?>"
                        href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>">
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

<?php include '../includes/footer.php'; ?>