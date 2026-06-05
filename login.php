<?php
$pageTitle = 'Login — Folio';
require_once 'config/app.php';
require_once 'classes/Auth.php';

$auth = new Auth();
if ($auth->check()) { header('Location: ' . BASE_URL . '/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $remember = isset($_POST['remember']);

    if ($auth->login($email, $password, $remember)) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
    $error = 'Invalid email or password.';
}

require_once 'includes/header.php';
?>
<div class="container auth-page">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Welcome back</h1>
            <p>Log in to manage your photos</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="form">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com"
                       autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required
                       placeholder="Your password" autocomplete="current-password">
            </div>
            <div class="form-check">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me for 30 days</label>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Login</button>
        </form>
        <p class="auth-switch">No account? <a href="register.php">Register here</a></p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
