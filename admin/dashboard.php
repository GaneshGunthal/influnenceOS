<?php
/**
 * InfluenceOS – Admin Dashboard
 * Summary stats, recent activity, charts.
 * @package InfluenceOS\Admin
 */
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';
requireRole(['admin']);

$pageTitle = 'Admin Dashboard';
$currentPage = 'admin_dashboard';

// Stats
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$creatorCount = $pdo->query("SELECT COUNT(*) FROM creators")->fetchColumn();
$campaignCount = $pdo->query("SELECT COUNT(*) FROM campaigns")->fetchColumn();
$dealCount = $pdo->query("SELECT COUNT(*) FROM deals")->fetchColumn();

$totalRevenue = $pdo->query("SELECT COALESCE(SUM(actual_revenue), 0) FROM campaigns")->fetchColumn();
$totalDeals = $pdo->query("SELECT COALESCE(SUM(deal_amount), 0) FROM deals")->fetchColumn();
$pendingPayments = $pdo->query("SELECT COALESCE(SUM(deal_amount), 0) FROM deals WHERE payment_status IN ('pending','overdue')")->fetchColumn();

// Role distribution
$roles = $pdo->query("SELECT role, COUNT(*) as cnt FROM users GROUP BY role")->fetchAll();
$roleLabels = json_encode(array_map(fn($r) => ucfirst($r['role']), $roles));
$roleCounts = json_encode(array_map(fn($r) => (int)$r['cnt'], $roles));

// Recent activity (last 10)
$stmt = $pdo->query("SELECT al.*, u.name as user_name FROM activity_log al JOIN users u ON al.user_id = u.user_id ORDER BY al.logged_at DESC LIMIT 10");
$recentActivity = $stmt->fetchAll();

// Campaign status distribution
$statusDist = $pdo->query("SELECT status, COUNT(*) as cnt FROM campaigns GROUP BY status")->fetchAll();
$statusLabels = json_encode(array_map(fn($s) => ucfirst(str_replace('_', ' ', $s['status'])), $statusDist));
$statusCounts = json_encode(array_map(fn($s) => (int)$s['cnt'], $statusDist));

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-header">
    <h2>🛡️ Admin Dashboard</h2>
</div>

<div class="stat-cards">
    <div class="stat-card primary"><div class="stat-icon">👥</div><div class="stat-value"><?php echo $userCount; ?></div><div class="stat-label">Total Users</div></div>
    <div class="stat-card success"><div class="stat-icon">🎨</div><div class="stat-value"><?php echo $creatorCount; ?></div><div class="stat-label">Creators</div></div>
    <div class="stat-card info"><div class="stat-icon">📢</div><div class="stat-value"><?php echo $campaignCount; ?></div><div class="stat-label">Campaigns</div></div>
    <div class="stat-card warning"><div class="stat-icon">🤝</div><div class="stat-value"><?php echo $dealCount; ?></div><div class="stat-label">Deals</div></div>
</div>

<div class="stat-cards">
    <div class="stat-card primary"><div class="stat-icon">💵</div><div class="stat-value">₹<?php echo number_format($totalRevenue, 0); ?></div><div class="stat-label">Total Revenue</div></div>
    <div class="stat-card success"><div class="stat-icon">💰</div><div class="stat-value">₹<?php echo number_format($totalDeals, 0); ?></div><div class="stat-label">Deal Value</div></div>
    <div class="stat-card danger"><div class="stat-icon">⏳</div><div class="stat-value">₹<?php echo number_format($pendingPayments, 0); ?></div><div class="stat-label">Pending Payments</div></div>
</div>

<!-- Charts -->
<div class="charts-grid">
    <div class="card">
        <div class="card-header"><h3>👥 User Roles</h3></div>
        <div class="chart-container" style="height:300px;"><canvas id="rolesChart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><h3>📢 Campaign Status</h3></div>
        <div class="chart-container" style="height:300px;"><canvas id="campaignChart"></canvas></div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card">
    <div class="card-header">
        <h3>📋 Recent Activity</h3>
        <a href="/influenceos/admin/logs.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table-styled">
            <thead><tr><th>User</th><th>Action</th><th>Module</th><th>Time</th></tr></thead>
            <tbody>
                <?php foreach ($recentActivity as $a): ?>
                    <tr>
                        <td><strong><?php echo sanitize($a['user_name']); ?></strong></td>
                        <td><?php echo sanitize($a['action']); ?></td>
                        <td><span class="badge badge-pending"><?php echo sanitize($a['module']); ?></span></td>
                        <td><small class="text-gray"><?php echo date('d M Y H:i', strtotime($a['logged_at'])); ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Links -->
<div class="card">
    <div class="card-header"><h3>⚡ Quick Actions</h3></div>
    <div class="d-flex gap-10 flex-wrap p-10">
        <a href="/influenceos/admin/users.php" class="btn btn-primary">👥 Manage Users</a>
        <a href="/influenceos/admin/logs.php" class="btn btn-accent">📋 Activity Logs</a>
        <a href="/influenceos/modules/creators/list.php" class="btn btn-outline">🎨 Creators</a>
        <a href="/influenceos/modules/campaigns/list.php" class="btn btn-outline">📢 Campaigns</a>
        <a href="/influenceos/modules/finance/dashboard.php" class="btn btn-outline">💼 Finance</a>
        <a href="/influenceos/modules/roi/dashboard.php" class="btn btn-outline">📉 ROI</a>
    </div>
</div>

<?php
$extraJS = "<script>
new Chart(document.getElementById('rolesChart'), {
    type: 'doughnut',
    data: { labels: $roleLabels, datasets: [{ data: $roleCounts, backgroundColor: ['#1E3A5F','#2E6DA4','#28a745','#ffc107'] }] }
});
new Chart(document.getElementById('campaignChart'), {
    type: 'pie',
    data: { labels: $statusLabels, datasets: [{ data: $statusCounts, backgroundColor: ['#6c757d','#28a745','#17a2b8','#1E3A5F','#ffc107'] }] }
});
</script>";
?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
