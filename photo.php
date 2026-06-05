<?php
$pageTitle = 'Photo — Folio';
require_once 'config/app.php';
require_once 'classes/Photo.php';

$photoModel = new Photo();
$id         = (int)($_GET['id'] ?? 0);
$photo      = $id ? $photoModel->getById($id) : false;

if (!$photo) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$pageTitle = htmlspecialchars($photo['title']) . ' — Folio';
$tags      = $photo['tags'] ? explode(',', $photo['tags']) : [];

require_once 'includes/header.php';
?>

<div class="container photo-detail-page">
    <a href="<?= BASE_URL ?>/index.php" class="back-link">&larr; Back to gallery</a>

    <div class="photo-detail-grid">
        <div class="photo-detail-image">
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($photo['stored_name']) ?>"
                 alt="<?= htmlspecialchars($photo['title']) ?>">
        </div>

        <aside class="photo-detail-meta">
            <?php if ($photo['is_featured']): ?>
                <span class="featured-badge featured-badge--lg">Featured</span>
            <?php endif; ?>

            <h1 class="photo-detail-title"><?= htmlspecialchars($photo['title']) ?></h1>

            <?php if ($photo['description']): ?>
                <p class="photo-detail-desc"><?= nl2br(htmlspecialchars($photo['description'])) ?></p>
            <?php endif; ?>

            <div class="meta-row">
                <span class="meta-label">Photographer</span>
                <span class="meta-value"><?= htmlspecialchars($photo['author']) ?></span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Uploaded</span>
                <span class="meta-value"><?= date('d F Y', strtotime($photo['uploaded_at'])) ?></span>
            </div>
            <div class="meta-row">
                <span class="meta-label">File size</span>
                <span class="meta-value"><?= round($photo['size_bytes'] / 1024) ?> KB</span>
            </div>

            <?php if (!empty($tags)): ?>
            <div class="meta-tags">
                <?php foreach ($tags as $tag): ?>
                    <a href="<?= BASE_URL ?>/index.php?tag=<?= urlencode(trim($tag)) ?>" class="tag-pill">
                        <?= htmlspecialchars(trim($tag)) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($auth->check()): ?>
                <?php if ($auth->isAdmin() || ($currentUser && $currentUser['id'] == $photo['user_id'])): ?>
                <div class="photo-actions">
                    <form method="POST" action="<?= BASE_URL ?>/dashboard.php"
                          onsubmit="return confirm('Delete this photo permanently?')">
                        <input type="hidden" name="action"   value="delete_photo">
                        <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                        <input type="hidden" name="redirect" value="gallery">
                        <button class="btn btn-danger btn-sm">Delete photo</button>
                    </form>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
