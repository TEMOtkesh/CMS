<?php
$pageTitle = 'Login — CMS';
require_once 'config/app.php';
require_once 'classes/Auth.php';

$auth = new Auth();
if ($auth->check()) { header('Location: ' . BASE_URL . '/dashboard.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($auth->login($email, $password)) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials or account is disabled.';
    }
}

require_once 'includes/header.php';
?>
<div class="container auth-page">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Welcome back</h1>
            <p>Log in to your account</p>
        </div>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="login.php" class="form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required
                       placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required
                       placeholder="Your password" autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Login</button>
        </form>
        <p class="auth-switch">No account yet? <a href="register.php">Create one</a></p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
