<?php
require_once __DIR__ . '/../classes/Auth.php';
$auth = new Auth();
$currentUser = $auth->currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CMS') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="/index.php" class="logo">CMS<span>.</span></a>
        <nav class="main-nav">
            <a href="/index.php">Home</a>
            <?php if ($auth->check()): ?>
                <a href="/dashboard.php">Dashboard</a>
                <?php if ($auth->isAdmin()): ?>
                    <a href="/admin.php">Admin</a>
                <?php endif; ?>
                <a href="/logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
            <?php else: ?>
                <a href="/login.php">Login</a>
                <a href="/register.php">Register</a>
            <?php endif; ?>
            <a href="/contact.php">Contact</a>
        </nav>
        <button class="nav-toggle" aria-label="Toggle menu">&#9776;</button>
    </div>
</header>
<main class="site-main">
