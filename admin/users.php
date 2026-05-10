<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$search = sanitizeInput($_GET['search'] ?? '');
$role = sanitizeInput($_GET['role'] ?? '');
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 8;
$offset = ($page - 1) * $limit;

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role) && in_array($role, ['admin', 'user'])) {
    $where[] = "role = ?";
    $params[] = $role;
}

$whereSql = "";
if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM users $whereSql");
$countStmt->execute($params);
$totalUsers = $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

$stmt = $pdo->prepare("
    SELECT id, name, email, role, created_at, updated_at 
    FROM users 
    $whereSql 
    ORDER BY created_at DESC 
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <h2>Users</h2>
                <p>View registered customers and admin accounts.</p>
            </div>
            <a href="dashboard.php" class="btn btn-light">Back to Dashboard</a>
        </div>

        <form method="GET" class="filter-form users-filter">
            <input 
                type="text" 
                name="search" 
                placeholder="Search by name or email..." 
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <select name="role">
                <option value="">All Roles</option>
                <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>Customer</option>
            </select>

            <button type="submit">Filter</button>
            <a href="users.php" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered Date</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">Customer</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M Y, h:i A', strtotime($user['created_at'])); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($user['updated_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">No users found.</td>
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
                        href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role); ?>">
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