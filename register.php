<?php
$pageTitle = 'Register — CMS';
require_once 'config/app.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

$auth = new Auth();
if ($auth->check()) { header('Location: ' . BASE_URL . '/dashboard.php'); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$name || !$email || !$password) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $userModel = new User();
        if ($userModel->register($name, $email, $password)) {
            $success = 'Account created! <a href="login.php">Login now &rarr;</a>';
        } else {
            $error = 'That email is already registered.';
        }
    }
}

require_once 'includes/header.php';
?>
<div class="container auth-page">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Create account</h1>
            <p>Join us — it only takes a minute</p>
        </div>
        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        <form method="POST" action="register.php" class="form" id="registerForm" novalidate>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required minlength="2"
                       placeholder="Your name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       autocomplete="name">
                <span class="field-error"></span>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required
                       placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       autocomplete="email">
                <span class="field-error"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8"
                       placeholder="Min. 8 characters" autocomplete="new-password">
                <span class="field-error"></span>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm" required
                       placeholder="Repeat password" autocomplete="new-password">
                <span class="field-error"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
