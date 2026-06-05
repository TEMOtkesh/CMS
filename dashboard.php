<?php
$pageTitle = 'My Photos — Folio';
require_once 'config/app.php';
require_once 'classes/Auth.php';
require_once 'classes/Photo.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->currentUser();
if (!$user) {
    $auth->logout();
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
$photoModel = new Photo();
$msg = $msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload' && isset($_FILES['photo'])) {
        $tags   = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
        $result = $photoModel->upload(
            $_FILES['photo'],
            (int)$user['id'],
            trim($_POST['title'] ?? ''),
            trim($_POST['description'] ?? ''),
            $tags
        );
        $msg     = $result === true ? 'Photo uploaded successfully!' : $result;
        $msgType = $result === true ? 'success' : 'error';

    } elseif ($action === 'delete_photo') {
        $pid    = (int)($_POST['photo_id'] ?? 0);
        $uid    = $auth->isAdmin() ? 0 : (int)$user['id'];
        $ok     = $photoModel->delete($pid, $uid);
        $msg     = $ok ? 'Photo deleted.' : 'Could not delete that photo.';
        $msgType = $ok ? 'success' : 'error';

        if (!empty($_POST['redirect']) && $_POST['redirect'] === 'gallery') {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }
    }
}

$photos = $photoModel->getByUser((int)$user['id']);
require_once 'includes/header.php';
?>

<div class="container dashboard-page">
    <div class="dashboard-header">
        <h1><?= htmlspecialchars($user['name']) ?></h1>
        <span class="role-badge role-<?= htmlspecialchars($user['role_name']) ?>"><?= htmlspecialchars($user['role_name']) ?></span>
    </div>

    <?php if ($msg): ?><div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="dashboard-grid">
        <section class="panel upload-panel">
            <h2>Upload a photo</h2>
            <form method="POST" action="dashboard.php" enctype="multipart/form-data" class="form" id="uploadForm">
                <input type="hidden" name="action" value="upload">
                <div class="form-group">
                    <label for="photo">Image file</label>
                    <input type="file" id="photo" name="photo" required
                           accept=".jpg,.jpeg,.png,.gif,.webp">
                    <small>JPG, PNG, GIF, WebP &mdash; max 8 MB</small>
                </div>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required maxlength="200"
                           placeholder="Give your photo a title">
                </div>
                <div class="form-group">
                    <label for="description">Description <small>(optional)</small></label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Tell us about this photo..."></textarea>
                </div>
                <div class="form-group">
                    <label for="tags">Tags <small>(comma-separated)</small></label>
                    <input type="text" id="tags" name="tags" placeholder="landscape, travel, black &amp; white">
                </div>
                <div class="progress-bar-wrap" id="progressWrap" style="display:none">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
                <button type="submit" class="btn btn-primary">Upload photo</button>
            </form>
        </section>

        <section class="panel">
            <h2>My photos <span class="count">(<?= count($photos) ?>)</span></h2>
            <?php if (empty($photos)): ?>
                <div class="empty-state"><p>You haven&apos;t uploaded anything yet.</p></div>
            <?php else: ?>
                <div class="dashboard-grid-photos">
                    <?php foreach ($photos as $p): ?>
                    <div class="dashboard-photo-card">
                        <a href="<?= BASE_URL ?>/photo.php?id=<?= $p['id'] ?>">
                            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['stored_name']) ?>"
                                 alt="<?= htmlspecialchars($p['title']) ?>"
                                 loading="lazy">
                        </a>
                        <div class="dashboard-photo-info">
                            <span class="dashboard-photo-title"><?= htmlspecialchars($p['title']) ?></span>
                            <?php if ($p['tags']): ?>
                                <span class="dashboard-photo-tags"><?= htmlspecialchars($p['tags']) ?></span>
                            <?php endif; ?>
                            <form method="POST" action="dashboard.php"
                                  onsubmit="return confirm('Delete this photo?')">
                                <input type="hidden" name="action"   value="delete_photo">
                                <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
