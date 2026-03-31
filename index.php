<?php
/**
 * InfluenceOS – Landing / Login Page
 * 
 * Main entry point. Shows login form, handles error and success messages.
 *
 * @package InfluenceOS
 */

session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /influenceos/dashboard.php');
    exit;
}

require_once __DIR__ . '/auth/guard.php';

$csrfToken = generateCSRFToken();

// Get messages
$error = '';
$msg = '';
if (isset($_GET['error'])) {
    $errorMap = [
        'login_required' => 'Please login to access that page.',
        'session_expired' => 'Your session has expired. Please login again.',
        'access_denied' => 'You do not have permission to access that page.',
        'invalid_token' => 'Invalid form submission. Please try again.',
    ];
    $error = $errorMap[$_GET['error']] ?? 'An error occurred.';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $msg = 'You have been logged out successfully.';
}

// Get login errors from session
$loginErrors = $_SESSION['login_errors'] ?? [];
$loginEmail = $_SESSION['login_email'] ?? '';
unset($_SESSION['login_errors'], $_SESSION['login_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfluenceOS – Creator & Agency Intelligence Management</title>
    <link rel="stylesheet" href="/influenceos/assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <span class="logo-icon">⚡</span>
                    <h1>InfluenceOS</h1>
                </div>
                <p>Creator & Agency Intelligence Management System</p>
            </div>

            <?php if ($msg): ?>
                <div class="alert alert-success"><?php echo sanitize($msg); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo sanitize($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($loginErrors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($loginErrors as $err): ?>
                        <div><?php echo sanitize($err); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/influenceos/auth/login.php" id="loginForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo sanitize($loginEmail); ?>"
                           required placeholder="Enter your email">
                    <span class="error-msg" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           required placeholder="Enter your password">
                    <span class="error-msg" id="passwordError"></span>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="/influenceos/auth/register.php">Register here</a></p>
            </div>

            <div class="auth-demo-info">
                <p><strong>Demo Credentials:</strong></p>
                <table class="demo-table">
                    <tr><td>Admin</td><td>admin@influenceos.com</td></tr>
                    <tr><td>Agency</td><td>riya@agency.com</td></tr>
                    <tr><td>Creator</td><td>ankit@creator.com</td></tr>
                    <tr><td>Brand</td><td>brand@techbrand.com</td></tr>
                </table>
                <p class="demo-pass">Password: <code>Password@123</code></p>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        let valid = true;
        const email = document.getElementById('email');
        const password = document.getElementById('password');

        document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');

        if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email.';
            valid = false;
        }

        if (!password.value) {
            document.getElementById('passwordError').textContent = 'Password is required.';
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
    </script>
</body>
</html>
