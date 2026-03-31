<?php
/**
 * InfluenceOS – Activity Logs (Admin)
 * @package InfluenceOS\Admin
 */
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';
requireRole(['admin']);

$pageTitle = 'Activity Logs';
$currentPage = 'admin_logs';

// Filters
$filterUser = (int)($_GET['user_id'] ?? 0);
$filterModule = trim($_GET['module'] ?? '');
$filterDate = trim($_GET['date'] ?? '');

$where = [];
$params = [];

if ($filterUser) { $where[] = "al.user_id = ?"; $params[] = $filterUser; }
if ($filterModule) { $where[] = "al.module = ?"; $params[] = $filterModule; }
if ($filterDate) { $where[] = "DATE(al.logged_at) = ?"; $params[] = $filterDate; }

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Pagination
$perPage = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$total = $pdo->prepare("SELECT COUNT(*) FROM activity_log al $whereSQL");
$total->execute($params);
$totalCount = $total->fetchColumn();
$totalPages = max(1, ceil($totalCount / $perPage));

$stmt = $pdo->prepare("SELECT al.*, u.name as user_name, u.role as user_role 
                        FROM activity_log al 
                        JOIN users u ON al.user_id = u.user_id 
                        $whereSQL 
                        ORDER BY al.logged_at DESC 
                        LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Get modules for filter
$modules = $pdo->query("SELECT DISTINCT module FROM activity_log ORDER BY module")->fetchAll(PDO::FETCH_COLUMN);
$users = $pdo->query("SELECT user_id, name FROM users ORDER BY name")->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2>📋 Activity Logs</h2>
        <p>Track all system activity (<?php echo number_format($totalCount); ?> entries)</p>
    </div>
</div>

<form class="filters-bar" method="GET">
    <div class="form-group">
        <label>User</label>
        <select name="user_id" class="form-control">
            <option value="">All Users</option>
            <?php foreach ($users as $u): ?>
                <option value="<?php echo $u['user_id']; ?>" <?php echo $filterUser == $u['user_id'] ? 'selected' : ''; ?>><?php echo sanitize($u['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Module</label>
        <select name="module" class="form-control">
            <option value="">All Modules</option>
            <?php foreach ($modules as $m): ?>
                <option value="<?php echo $m; ?>" <?php echo $filterModule === $m ? 'selected' : ''; ?>><?php echo ucfirst($m); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Date</label>
        <input type="date" name="date" class="form-control" value="<?php echo sanitize($filterDate); ?>">
    </div>
    <button type="submit" class="btn btn-accent btn-sm">🔍 Filter</button>
    <a href="/influenceos/admin/logs.php" class="btn btn-outline btn-sm">Clear</a>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table-styled">
            <thead>
                <tr><th>Time</th><th>User</th><th>Role</th><th>Action</th><th>Module</th><th>IP</th></tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><small><?php echo date('d M Y H:i:s', strtotime($l['logged_at'])); ?></small></td>
                        <td><strong><?php echo sanitize($l['user_name']); ?></strong></td>
                        <td><span class="badge badge-pending"><?php echo ucfirst($l['user_role']); ?></span></td>
                        <td><?php echo sanitize($l['action']); ?></td>
                        <td><span class="badge badge-active"><?php echo sanitize($l['module']); ?></span></td>
                        <td><small class="text-gray"><?php echo sanitize($l['ip_address'] ?? '—'); ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&user_id=<?php echo $filterUser; ?>&module=<?php echo urlencode($filterModule); ?>&date=<?php echo urlencode($filterDate); ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <a href="?page=<?php echo $i; ?>&user_id=<?php echo $filterUser; ?>&module=<?php echo urlencode($filterModule); ?>&date=<?php echo urlencode($filterDate); ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>&user_id=<?php echo $filterUser; ?>&module=<?php echo urlencode($filterModule); ?>&date=<?php echo urlencode($filterDate); ?>">Next ›</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
