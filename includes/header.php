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
    <script>
        /* Apply saved theme color before first paint — no flash */
        (function () {
            var hex = localStorage.getItem('folioTheme');
            if (!hex || !/^#[0-9a-f]{6}$/i.test(hex)) return;
            var r = parseInt(hex.slice(1,3),16),
                g = parseInt(hex.slice(3,5),16),
                b = parseInt(hex.slice(5,7),16);
            var root = document.documentElement;
            root.style.setProperty('--gold',       hex);
            root.style.setProperty('--gold-light', lighten(r,g,b,20));
            root.style.setProperty('--gold-dim',   'rgba('+r+','+g+','+b+',.15)');
            root.style.setProperty('--gold-glow',  'rgba('+r+','+g+','+b+',.3)');
            function lighten(r,g,b,pct){
                return '#'+[r,g,b].map(function(c){
                    return Math.min(255,Math.round(c+(255-c)*pct/100))
                        .toString(16).padStart(2,'0');
                }).join('');
            }
        })();
    </script>
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
                <?php elseif ($auth->isMod()): ?>
                    <a href="<?= $base ?>/admin.php">Panel</a>
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
