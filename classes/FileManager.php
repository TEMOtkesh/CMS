<?php
require_once __DIR__ . '/Database.php';

class FileManager {
    private PDO $db;
    private string $uploadDir;

    public function __construct() {
        $this->db        = Database::get();
        $this->uploadDir = __DIR__ . '/../uploads/';
    }

    public function upload(array $file, int $userId, array $tags = []): bool|string {
        if ($file['error'] !== UPLOAD_ERR_OK) return 'Upload error.';

        $allowed = ['image/jpeg','image/png','image/gif','application/pdf','text/plain'];
        if (!in_array($file['type'], $allowed)) return 'File type not allowed.';
        if ($file['size'] > 5 * 1024 * 1024) return 'File exceeds 5 MB limit.';

        $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
        $stored     = uniqid('file_', true) . '.' . $ext;
        $destination = $this->uploadDir . $stored;

        if (!move_uploaded_file($file['tmp_name'], $destination)) return 'Could not save file.';

        $stmt = $this->db->prepare(
            'INSERT INTO files (user_id, original_name, stored_name, mime_type, size_bytes)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $file['name'], $stored, $file['type'], $file['size']]);
        $fileId = (int)$this->db->lastInsertId();

        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if ($tagName === '') continue;
            $this->db->prepare('INSERT IGNORE INTO tags (name) VALUES (?)')->execute([$tagName]);
            $tag = $this->db->prepare('SELECT id FROM tags WHERE name = ?');
            $tag->execute([$tagName]);
            $tagId = $tag->fetchColumn();
            $this->db->prepare('INSERT IGNORE INTO file_tags (file_id, tag_id) VALUES (?, ?)')->execute([$fileId, $tagId]);
        }

        return true;
    }

    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT f.*, GROUP_CONCAT(t.name SEPARATOR ", ") AS tags
             FROM files f
             LEFT JOIN file_tags ft ON ft.file_id = f.id
             LEFT JOIN tags t ON t.id = ft.tag_id
             WHERE f.user_id = ?
             GROUP BY f.id ORDER BY f.uploaded_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function all(): array {
        return $this->db->query(
            'SELECT f.*, u.name AS uploader, u.email,
                    GROUP_CONCAT(t.name SEPARATOR ", ") AS tags
             FROM files f
             JOIN users u ON u.id = f.user_id
             LEFT JOIN file_tags ft ON ft.file_id = f.id
             LEFT JOIN tags t ON t.id = ft.tag_id
             GROUP BY f.id ORDER BY f.uploaded_at DESC'
        )->fetchAll();
    }

    public function delete(int $fileId, int $userId = 0): bool {
        $where = $userId ? 'id = ? AND user_id = ?' : 'id = ?';
        $params = $userId ? [$fileId, $userId] : [$fileId];
        $stmt = $this->db->prepare("SELECT stored_name FROM files WHERE $where");
        $stmt->execute($params);
        $row = $stmt->fetch();
        if (!$row) return false;

        @unlink($this->uploadDir . $row['stored_name']);
        $del = $this->db->prepare("DELETE FROM files WHERE $where");
        return $del->execute($params);
    }
}
