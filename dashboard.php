<?php
$pageTitle = 'Dashboard — CMS';
require_once 'classes/Auth.php';
require_once 'classes/FileManager.php';

$auth = new Auth();
$auth->requireLogin();

$user        = $auth->currentUser();
$fileManager = new FileManager();
$message     = '';
$msgType     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete_file') {
        $fid = (int)($_POST['file_id'] ?? 0);
        if ($fileManager->delete($fid, (int)$user['id'])) {
            $message = 'File deleted.';
            $msgType = 'success';
        } else {
            $message = 'Could not delete file.';
            $msgType = 'error';
        }
    } elseif (isset($_FILES['upload'])) {
        $tags   = array_map('trim', explode(',', $_POST['tags'] ?? ''));
        $result = $fileManager->upload($_FILES['upload'], (int)$user['id'], $tags);
        if ($result === true) {
            $message = 'File uploaded successfully!';
            $msgType = 'success';
        } else {
            $message = $result;
            $msgType = 'error';
        }
    }
}

$files = $fileManager->getByUser((int)$user['id']);
require_once 'includes/header.php';
?>
<div class="container dashboard">
    <div class="dashboard-header">
        <h1>Welcome, <?= htmlspecialchars($user['name']) ?></h1>
        <span class="role-badge role-<?= htmlspecialchars($user['role_name']) ?>"><?= htmlspecialchars($user['role_name']) ?></span>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <section class="upload-panel panel">
            <h2>Upload a File</h2>
            <form method="POST" enctype="multipart/form-data" class="form" id="uploadForm">
                <div class="form-group">
                    <label for="upload">Choose file</label>
                    <input type="file" id="upload" name="upload" required
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.txt">
                    <small>Allowed: JPG, PNG, GIF, PDF, TXT — max 5 MB</small>
                </div>
                <div class="form-group">
                    <label for="tags">Tags <small>(comma-separated)</small></label>
                    <input type="text" id="tags" name="tags" placeholder="photo, report, 2024">
                </div>
                <div class="progress-bar-wrap" id="progressWrap" style="display:none">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </section>

        <section class="files-panel panel">
            <h2>My Files <span class="count">(<?= count($files) ?>)</span></h2>
            <?php if (empty($files)): ?>
                <p class="empty-state">No files yet. Upload one!</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr><th>Name</th><th>Type</th><th>Tags</th><th>Date</th><th></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($files as $f): ?>
                        <tr>
                            <td><a href="/uploads/<?= htmlspecialchars($f['stored_name']) ?>" target="_blank"><?= htmlspecialchars($f['original_name']) ?></a></td>
                            <td><?= htmlspecialchars($f['mime_type']) ?></td>
                            <td><?= htmlspecialchars($f['tags'] ?? '—') ?></td>
                            <td><?= date('d M Y', strtotime($f['uploaded_at'])) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Delete this file?')">
                                    <input type="hidden" name="action" value="delete_file">
                                    <input type="hidden" name="file_id" value="<?= $f['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
