<?php
require_once __DIR__ . '/Database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::get();
    }

    public function register(string $name, string $email, string $password): bool {
        if ($this->findByEmail($email)) return false;

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
        );
        return $stmt->execute([$name, $email, $hash]);
    }

    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.name AS role_name FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function all(): array {
        return $this->db->query(
            'SELECT u.id, u.name, u.email, u.created_at, u.is_active, r.name AS role_name, r.id AS role_id
             FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC'
        )->fetchAll();
    }

    public function updateRole(int $userId, int $roleId): bool {
        $stmt = $this->db->prepare('UPDATE users SET role_id = ? WHERE id = ?');
        return $stmt->execute([$roleId, $userId]);
    }

    public function toggleActive(int $userId): bool {
        $stmt = $this->db->prepare('UPDATE users SET is_active = NOT is_active WHERE id = ?');
        return $stmt->execute([$userId]);
    }

    public function delete(int $userId): bool {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$userId]);
    }
}
