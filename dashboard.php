<?php
/**
 * InfluenceOS – Dashboard Router
 * 
 * Redirects logged-in users to their role-specific dashboard.
 *
 * @package InfluenceOS
 */

require_once __DIR__ . '/auth/guard.php';
requireLogin();

$role = $_SESSION['role'] ?? '';

switch ($role) {
    case 'admin':
        header('Location: /influenceos/admin/dashboard.php');
        break;
    case 'agency':
        header('Location: /influenceos/modules/campaigns/list.php');
        break;
    case 'creator':
        header('Location: /influenceos/modules/creators/view.php?own=1');
        break;
    case 'brand':
        header('Location: /influenceos/modules/campaigns/list.php?view=brand');
        break;
    default:
        header('Location: /influenceos/index.php?error=access_denied');
        break;
}
exit;
