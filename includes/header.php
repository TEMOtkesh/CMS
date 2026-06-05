<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/Auth.php';
$auth = new Auth();
$currentUser = $auth->currentUser();
$base = BASE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CMS') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="<?= $base ?>/index.php" class="logo">CMS<span>.</span></a>
        <nav class="main-nav" id="mainNav">
            <a href="<?= $base ?>/index.php">Home</a>
            <?php if ($auth->check()): ?>
                <a href="<?= $base ?>/dashboard.php">Dashboard</a>
                <?php if ($auth->isAdmin()): ?>
                    <a href="<?= $base ?>/admin.php">Admin</a>
                <?php endif; ?>
                <a href="<?= $base ?>/logout.php" class="nav-logout">Logout</a>
                <span class="nav-user"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <?php else: ?>
                <a href="<?= $base ?>/login.php">Login</a>
                <a href="<?= $base ?>/register.php" class="btn btn-primary btn-sm">Register</a>
            <?php endif; ?>
            <a href="<?= $base ?>/contact.php">Contact</a>
        </nav>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
<main class="site-main">
