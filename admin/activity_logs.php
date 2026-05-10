<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$search = sanitizeInput($_GET['search'] ?? '');
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(activity_logs.action LIKE ? OR activity_logs.details LIKE ? OR users.name LIKE ? OR users.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSql = "";
if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

$countStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM activity_logs 
    LEFT JOIN users ON activity_logs.user_id = users.id
    $whereSql
");
$countStmt->execute($params);
$totalLogs = $countStmt->fetchColumn();
$totalPages = ceil($totalLogs / $limit);

$stmt = $pdo->prepare("
    SELECT 
        activity_logs.*,
        users.name AS user_name,
        users.email AS user_email,
        users.role AS user_role
    FROM activity_logs
    LEFT JOIN users ON activity_logs.user_id = users.id
    $whereSql
    ORDER BY activity_logs.created_at DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <h2>Activity Logs</h2>
                <p>Review important system actions such as logins, product updates, orders, and Smart Assistant usage.</p>
            </div>
            <a href="dashboard.php" class="btn btn-light">Back to Dashboard</a>
        </div>

        <form method="GET" class="filter-form activity-filter">
            <input 
                type="text" 
                name="search" 
                placeholder="Search logs by user, email, action, or details..." 
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">Search</button>
            <a href="activity_logs.php" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Date/Time</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($logs) > 0): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>#<?php echo $log['id']; ?></td>
                                <td>
                                    <?php if (!empty($log['user_name'])): ?>
                                        <strong><?php echo htmlspecialchars($log['user_name']); ?></strong>
                                        <br>
                                        <small><?php echo htmlspecialchars($log['user_email']); ?></small>
                                        <br>
                                        <?php if ($log['user_role'] === 'admin'): ?>
                                            <span class="badge badge-admin">Admin</span>
                                        <?php else: ?>
                                            <span class="badge badge-user">Customer</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="muted-text">Deleted or unknown user</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($log['action']); ?></strong>
                                </td>
                                <td>
                                    <?php echo !empty($log['details']) ? htmlspecialchars($log['details']) : '<span class="muted-text">No details</span>'; ?>
                                </td>
                                <td><?php echo date('d M Y, h:i A', strtotime($log['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">No activity logs found.</td>
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
                        href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
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