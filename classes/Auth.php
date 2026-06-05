<?php
require_once __DIR__ . '/User.php';

class Auth {
    private User $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->userModel = new User();
    }

    public function login(string $email, string $password): bool {
        $user = $this->userModel->findByEmail($email);
        if (!$user || !$user['is_active']) return false;
        if (!password_verify($password, $user['password_hash'])) return false;

        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role_name'] ?? 'user';
        return true;
    }

    public function logout(): void {
        session_unset();
        session_destroy();
    }

    public function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(): bool {
        return ($_SESSION['user_role'] ?? '') === 'admin';
    }

    public function isModerator(): bool {
        return in_array($_SESSION['user_role'] ?? '', ['admin', 'moderator']);
    }

    public function requireLogin(): void {
        if (!$this->check()) {
            header('Location: /login.php');
            exit;
        }
    }

    public function requireAdmin(): void {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header('Location: /index.php');
            exit;
        }
    }

    public function currentUser(): array|false {
        if (!$this->check()) return false;
        return $this->userModel->findById((int)$_SESSION['user_id']);
    }
}
