<?php
require_once __DIR__ . '/Database.php';

class Photo {
    private PDO $db;
    private string $uploadDir;

    public function __construct() {
        $this->db        = Database::get();
        $this->uploadDir = __DIR__ . '/../uploads/';
    }

    public function upload(array $file, int $userId, string $title, string $desc, array $tags): bool|string {
        if ($file['error'] !== UPLOAD_ERR_OK) return 'Upload failed — please try again.';

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed)) return 'Only JPG, PNG, GIF, and WebP images are allowed.';
        if ($file['size'] > 8 * 1024 * 1024) return 'Image must be under 8 MB.';

        $ext        = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $stored     = uniqid('photo_', true) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $this->uploadDir . $stored)) return 'Could not save image.';

        $stmt = $this->db->prepare(
            'INSERT INTO photos (user_id, title, description, stored_name, original_name, mime_type, size_bytes)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, trim($title), trim($desc), $stored, $file['name'], $file['type'], $file['size']]);
        $photoId = (int)$this->db->lastInsertId();

        $this->syncTags($photoId, $tags);
        return true;
    }

    private function syncTags(int $photoId, array $tags): void {
        foreach ($tags as $name) {
            $name = strtolower(trim($name));
            if ($name === '') continue;
            $this->db->prepare('INSERT IGNORE INTO tags (name) VALUES (?)')->execute([$name]);
            $row = $this->db->prepare('SELECT id FROM tags WHERE name = ?');
            $row->execute([$name]);
            $tagId = $row->fetchColumn();
            $this->db->prepare('INSERT IGNORE INTO photo_tags (photo_id, tag_id) VALUES (?, ?)')->execute([$photoId, $tagId]);
        }
    }

    public function getAll(string $tag = '', int $limit = 60, int $offset = 0): array {
        if ($tag) {
            $stmt = $this->db->prepare(
                'SELECT p.*, u.name AS author,
                        GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ",") AS tags
                 FROM photos p
                 JOIN users u ON u.id = p.user_id
                 JOIN photo_tags pt ON pt.photo_id = p.id
                 JOIN tags t2 ON t2.id = pt.tag_id AND t2.name = ?
                 LEFT JOIN photo_tags pt2 ON pt2.photo_id = p.id
                 LEFT JOIN tags t ON t.id = pt2.tag_id
                 GROUP BY p.id ORDER BY p.uploaded_at DESC LIMIT ? OFFSET ?'
            );
            $stmt->execute([$tag, $limit, $offset]);
        } else {
            $stmt = $this->db->prepare(
                'SELECT p.*, u.name AS author,
                        GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ",") AS tags
                 FROM photos p
                 JOIN users u ON u.id = p.user_id
                 LEFT JOIN photo_tags pt ON pt.photo_id = p.id
                 LEFT JOIN tags t ON t.id = pt.tag_id
                 GROUP BY p.id ORDER BY p.uploaded_at DESC LIMIT ? OFFSET ?'
            );
            $stmt->execute([$limit, $offset]);
        }
        return $stmt->fetchAll();
    }

    public function getFeatured(): array {
        return $this->db->query(
            'SELECT p.*, u.name AS author FROM photos p
             JOIN users u ON u.id = p.user_id
             WHERE p.is_featured = 1 ORDER BY p.uploaded_at DESC LIMIT 6'
        )->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.name AS author, u.email AS author_email,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ",") AS tags
             FROM photos p
             JOIN users u ON u.id = p.user_id
             LEFT JOIN photo_tags pt ON pt.photo_id = p.id
             LEFT JOIN tags t ON t.id = pt.tag_id
             WHERE p.id = ?
             GROUP BY p.id'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT p.*, GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ",") AS tags
             FROM photos p
             LEFT JOIN photo_tags pt ON pt.photo_id = p.id
             LEFT JOIN tags t ON t.id = pt.tag_id
             WHERE p.user_id = ?
             GROUP BY p.id ORDER BY p.uploaded_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function allForAdmin(): array {
        return $this->db->query(
            'SELECT p.*, u.name AS author,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ",") AS tags
             FROM photos p
             JOIN users u ON u.id = p.user_id
             LEFT JOIN photo_tags pt ON pt.photo_id = p.id
             LEFT JOIN tags t ON t.id = pt.tag_id
             GROUP BY p.id ORDER BY p.uploaded_at DESC'
        )->fetchAll();
    }

    public function delete(int $photoId, int $userId = 0): bool {
        $where  = $userId ? 'id = ? AND user_id = ?' : 'id = ?';
        $params = $userId ? [$photoId, $userId] : [$photoId];
        $stmt   = $this->db->prepare("SELECT stored_name FROM photos WHERE $where");
        $stmt->execute($params);
        $row = $stmt->fetch();
        if (!$row) return false;

        @unlink($this->uploadDir . $row['stored_name']);
        return $this->db->prepare("DELETE FROM photos WHERE $where")->execute($params);
    }

    public function toggleFeatured(int $photoId): bool {
        return $this->db->prepare('UPDATE photos SET is_featured = NOT is_featured WHERE id = ?')->execute([$photoId]);
    }

    public function getAllTags(): array {
        return $this->db->query(
            'SELECT t.name, COUNT(pt.photo_id) AS cnt
             FROM tags t JOIN photo_tags pt ON pt.tag_id = t.id
             GROUP BY t.id ORDER BY cnt DESC LIMIT 20'
        )->fetchAll();
    }
}
