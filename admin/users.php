<?php
/**
 * InfluenceOS – User Management (Admin)
 * @package InfluenceOS\Admin
 */
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';
requireRole(['admin']);

$pageTitle = 'User Management';
$currentPage = 'admin_users';
$success = '';

// Handle toggle active
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_user'])) {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $userId = (int)$_POST['user_id'];
        $newState = (int)$_POST['new_state'];
        if ($userId !== $_SESSION['user_id']) { // Can't deactivate self
            $pdo->prepare("UPDATE users SET is_active = ? WHERE user_id = ?")->execute([$newState, $userId]);
            logActivity($pdo, $_SESSION['user_id'], ($newState ? 'Activated' : 'Deactivated') . " user #$userId", 'admin');
            $success = 'User status updated.';
        }
    }
}

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $userId = (int)$_POST['user_id'];
        $newRole = $_POST['new_role'];
        if (in_array($newRole, ['admin','agency','creator','brand']) && $userId !== $_SESSION['user_id']) {
            $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?")->execute([$newRole, $userId]);
            logActivity($pdo, $_SESSION['user_id'], "Changed user #$userId role to $newRole", 'admin');
            $success = 'User role updated.';
        }
    }
}

// Fetch users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2>👥 User Management</h2>
        <p>Manage user accounts, roles, and access</p>
    </div>
    <a href="/influenceos/auth/register.php" class="btn btn-primary">➕ Add User</a>
</div>

<?php if ($success): ?><div class="alert alert-success"><?php echo sanitize($success); ?></div><?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table-styled">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['user_id']; ?></td>
                        <td><strong><?php echo sanitize($u['name']); ?></strong></td>
                        <td><?php echo sanitize($u['email']); ?></td>
                        <td>
                            <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                    <select name="new_role" class="form-control" style="width:auto;padding:4px;display:inline;" onchange="this.form.submit()">
                                        <?php foreach (['admin','agency','creator','brand'] as $r): ?>
                                            <option value="<?php echo $r; ?>" <?php echo $u['role'] === $r ? 'selected' : ''; ?>><?php echo ucfirst($r); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="change_role" value="1">
                                </form>
                            <?php else: ?>
                                <span class="badge badge-active"><?php echo ucfirst($u['role']); ?></span> <small>(you)</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge badge-active">Active</span>
                            <?php else: ?>
                                <span class="badge badge-overdue">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><small><?php echo date('d M Y', strtotime($u['created_at'])); ?></small></td>
                        <td>
                            <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                    <input type="hidden" name="new_state" value="<?php echo $u['is_active'] ? 0 : 1; ?>">
                                    <button type="submit" name="toggle_user" class="btn btn-sm <?php echo $u['is_active'] ? 'btn-danger' : 'btn-success'; ?>" onclick="return confirm('<?php echo $u['is_active'] ? 'Deactivate' : 'Activate'; ?> this user?')">
                                        <?php echo $u['is_active'] ? '🔒 Deactivate' : '🔓 Activate'; ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
