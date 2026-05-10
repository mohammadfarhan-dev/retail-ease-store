<?php include 'includes/header.php'; ?>

<section class="hero">
    <div class="container">
        <h1>Welcome to RetailEase Store</h1>
        <p>
            A small retail business web application where customers can browse products,
            place orders, and get support using a Smart Assistant.
        </p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn-main">Get Started</a>
        <?php else: ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin/dashboard.php" class="btn-main">Go to Admin Dashboard</a>
            <?php else: ?>
                <a href="user/products.php" class="btn-main">Browse Products</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Project Features</h2>

        <div class="cards">
            <div class="card">
                <h3>Product Catalogue</h3>
                <p>Customers can browse products, search by keyword, and filter products by category.</p>
            </div>

            <div class="card">
                <h3>Order Management</h3>
                <p>Customers can place orders while admins can approve, reject, or complete order requests.</p>
            </div>

            <div class="card">
                <h3>Smart Assistant</h3>
                <p>A safe rule-based assistant helps users understand how to use the system.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>