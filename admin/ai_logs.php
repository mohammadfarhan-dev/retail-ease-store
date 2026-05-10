<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$stmt = $pdo->query("
    SELECT 
        ai_logs.*,
        users.name AS user_name,
        users.email AS user_email
    FROM ai_logs
    LEFT JOIN users ON ai_logs.user_id = users.id
    ORDER BY ai_logs.created_at DESC
");

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <h2>AI Assistant Logs</h2>
                <p>Review Smart Assistant usage and human-reviewed responses.</p>
            </div>
            <a href="dashboard.php" class="btn btn-light">Back to Dashboard</a>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Question</th>
                        <th>Assistant Response</th>
                        <th>Reviewed</th>
                        <th>Date</th>
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
                                    <?php else: ?>
                                        <span class="muted-text">Deleted user</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($log['question']); ?></td>
                                <td><?php echo htmlspecialchars($log['assistant_response']); ?></td>
                                <td>
                                    <?php if ($log['reviewed_by_user']): ?>
                                        <span class="badge badge-completed">Reviewed</span>
                                    <?php else: ?>
                                        <span class="badge badge-rejected">Not Reviewed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M Y, h:i A', strtotime($log['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">No Smart Assistant logs found yet.</td>
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