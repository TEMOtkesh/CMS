<?php
require_once 'config/app.php';
require_once 'classes/Auth.php';
$auth = new Auth();
$auth->logout();
header('Location: ' . BASE_URL . '/login.php');
exit;
