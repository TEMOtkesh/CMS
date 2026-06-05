<?php
require_once 'config/app.php';
require_once 'classes/Auth.php';
require_once 'classes/Photo.php';
require_once 'classes/Logger.php';

$auth = new Auth();
$auth->requireMod();

$photoModel = new Photo();
$logger     = new Logger();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $pid    = (int)($_POST['photo_id'] ?? 0);

    if ($action === 'toggle_featured' && $pid) {
        $photoModel->toggleFeatured($pid);
        $logger->write('TOGGLE_FEATURED', "photo_id=$pid by #{$_SESSION['user_id']} ({$_SESSION['user_role']})");
    }

    $redirect = $_POST['redirect'] ?? '';
    if ($redirect === 'photo') {
        header('Location: ' . BASE_URL . '/photo.php?id=' . $pid);
    } else {
        header('Location: ' . BASE_URL . '/admin.php');
    }
    exit;
}

header('Location: ' . BASE_URL . '/admin.php');
exit;
