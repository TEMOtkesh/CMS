<?php
require_once __DIR__ . '/User.php';

class Auth {
    private User $userModel;
    private const COOKIE_NAME   = 'cms_remember';
    private const COOKIE_SECRET = 'ppg_s3cr3t_k3y_2024';
    private const COOKIE_DAYS   = 30;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->userModel = new User();
        if (!$this->check()) $this->tryLoginFromCookie();
    }

    public function login(string $email, string $password, bool $remember = false): bool {
        $user = $this->userModel->findByEmail($email);
        if (!$user || !$user['is_active']) return false;
        if (!password_verify($password, $user['password_hash'])) return false;

        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role_name'] ?? 'user';

        if ($remember) {
            $token = hash('sha256', $user['id'] . $user['email'] . self::COOKIE_SECRET);
            $value = base64_encode($user['id'] . '|' . $token);
            setcookie(self::COOKIE_NAME, $value, time() + self::COOKIE_DAYS * 86400, '/', '', false, true);
        }
        return true;
    }

    private function tryLoginFromCookie(): void {
        if (empty($_COOKIE[self::COOKIE_NAME])) return;
        $decoded = base64_decode($_COOKIE[self::COOKIE_NAME]);
        if (!$decoded || !str_contains($decoded, '|')) return;

        [$id, $token] = explode('|', $decoded, 2);
        $user = $this->userModel->findById((int)$id);
        if (!$user || !$user['is_active']) return;

        $expected = hash('sha256', $user['id'] . $user['email'] . self::COOKIE_SECRET);
        if (!hash_equals($expected, $token)) return;

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role_name'] ?? 'user';
    }

    public function logout(): void {
        session_unset();
        session_destroy();
        setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
    }

    public function check(): bool    { return isset($_SESSION['user_id']); }
    public function isAdmin(): bool  { return ($_SESSION['user_role'] ?? '') === 'admin'; }
    public function isMod(): bool    { return in_array($_SESSION['user_role'] ?? '', ['admin', 'moderator']); }

    public function requireLogin(): void {
        if (!$this->check()) {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }

    public function requireAdmin(): void {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }
    }

    public function currentUser(): array|false {
        if (!$this->check()) return false;
        return $this->userModel->findById((int)$_SESSION['user_id']);
    }
}
