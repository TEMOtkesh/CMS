<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/Auth.php';
$auth        = new Auth();
$currentUser = $auth->currentUser();
$base        = BASE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Folio') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= $base ?>/index.php" class="logo">Folio<em>.</em></a>
        <nav class="main-nav" id="mainNav">
            <a href="<?= $base ?>/index.php">Gallery</a>
            <?php if ($auth->check()): ?>
                <a href="<?= $base ?>/dashboard.php">My Photos</a>
                <?php if ($auth->isAdmin()): ?>
                    <a href="<?= $base ?>/admin.php">Admin</a>
                <?php endif; ?>
                <a href="<?= $base ?>/contact.php">Contact</a>
                <span class="nav-divider"></span>
                <span class="nav-user"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="<?= $base ?>/logout.php" class="nav-logout">Logout</a>
            <?php else: ?>
                <a href="<?= $base ?>/contact.php">Contact</a>
                <span class="nav-divider"></span>
                <a href="<?= $base ?>/login.php">Login</a>
                <a href="<?= $base ?>/register.php" class="btn btn-primary btn-sm">Register</a>
            <?php endif; ?>
        </nav>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
<main class="site-main">
