<?php
$pageTitle = 'Admin — Folio';
require_once 'config/app.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';
require_once 'classes/Photo.php';
require_once 'classes/Logger.php';

$auth = new Auth();
$auth->requireMod();

$userModel  = new User();
$photoModel = new Photo();
$logger     = new Logger();
$msg = $msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $uid    = (int)($_POST['user_id']  ?? 0);
    $pid    = (int)($_POST['photo_id'] ?? 0);

    if ($action === 'update_role' && $uid) {
        $userModel->updateRole($uid, (int)$_POST['role_id']);
        $logger->write('ROLE_CHANGE', "user_id=$uid role_id={$_POST['role_id']} by admin #{$_SESSION['user_id']}");
        $msg = 'Role updated.'; $msgType = 'success';

    } elseif ($action === 'toggle_active' && $uid) {
        $userModel->toggleActive($uid);
        $logger->write('TOGGLE_ACTIVE', "user_id=$uid by admin #{$_SESSION['user_id']}");
        $msg = 'User status toggled.'; $msgType = 'success';

    } elseif ($action === 'delete_user' && $uid) {
        $userModel->delete($uid);
        $logger->write('DELETE_USER', "user_id=$uid by admin #{$_SESSION['user_id']}");
        $msg = 'User deleted.'; $msgType = 'success';

    } elseif ($action === 'delete_photo' && $pid) {
        $photoModel->delete($pid);
        $logger->write('DELETE_PHOTO', "photo_id=$pid by admin #{$_SESSION['user_id']}");
        $msg = 'Photo deleted.'; $msgType = 'success';

    } elseif ($action === 'toggle_featured' && $pid) {
        $photoModel->toggleFeatured($pid);
        $logger->write('TOGGLE_FEATURED', "photo_id=$pid by admin #{$_SESSION['user_id']}");
        $msg = 'Featured status updated.'; $msgType = 'success';

    } elseif ($action === 'clear_log') {
        $logger->clear();
        $msg = 'Log cleared.'; $msgType = 'success';
    }
}

$users   = $userModel->all();
$photos  = $photoModel->allForAdmin();
$logText = $logger->read();

require_once 'includes/header.php';
?>
<div class="container admin-page">
    <div class="dashboard-header">
        <h1>Admin panel</h1>
        <span class="role-badge role-admin">admin</span>
    </div>

    <?php if ($msg): ?><div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="tabs" id="adminTabs">
        <?php if ($auth->isAdmin()): ?>
        <button class="tab-btn active" data-tab="users">Users (<?= count($users) ?>)</button>
        <button class="tab-btn"        data-tab="photos">Photos (<?= count($photos) ?>)</button>
        <button class="tab-btn"        data-tab="logs">Activity log</button>
        <?php else: ?>
        <button class="tab-btn active" data-tab="photos">Photos (<?= count($photos) ?>)</button>
        <?php endif; ?>
    </div>

    <!-- Users tab (admin only) -->
    <div class="tab-content <?= $auth->isAdmin() ? '' : 'hidden' ?>" id="tab-users">
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr class="<?= !$u['is_active'] ? 'row-inactive' : '' ?>">
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <form method="POST" class="inline-form">
                        <input type="hidden" name="action" value="update_role">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <select name="role_id" onchange="this.form.submit()" aria-label="Role">
                            <option value="1" <?= $u['role_id']==1?'selected':'' ?>>admin</option>
                            <option value="2" <?= $u['role_id']==2?'selected':'' ?>>moderator</option>
                            <option value="3" <?= $u['role_id']==3?'selected':'' ?>>user</option>
                        </select>
                    </form>
                </td>
                <td><?= $u['is_active']
                    ? '<span class="badge-status badge-active">Active</span>'
                    : '<span class="badge-status badge-inactive">Disabled</span>' ?></td>
                <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td class="action-cell">
                    <form method="POST" class="inline-form">
                        <input type="hidden" name="action"  value="toggle_active">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <button class="btn btn-ghost btn-sm">Toggle</button>
                    </form>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <form method="POST" class="inline-form" onsubmit="return confirm('Delete this user and all their photos?')">
                        <input type="hidden" name="action"  value="delete_user">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Photos tab -->
    <div class="tab-content <?= $auth->isAdmin() ? 'hidden' : '' ?>" id="tab-photos">
        <table class="data-table">
            <thead>
                <tr><th>Preview</th><th>Title</th><th>By</th><th>Tags</th><th>Featured</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($photos as $p): ?>
            <tr>
                <td>
                    <a href="<?= BASE_URL ?>/photo.php?id=<?= $p['id'] ?>" target="_blank">
                        <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['stored_name']) ?>"
                             alt="" style="width:60px;height:45px;object-fit:cover;border-radius:4px;">
                    </a>
                </td>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td><?= htmlspecialchars($p['author']) ?></td>
                <td><?= htmlspecialchars($p['tags'] ?? '—') ?></td>
                <td><?= $p['is_featured'] ? '<span class="badge-status badge-active">Yes</span>' : '—' ?></td>
                <td><?= date('d M Y', strtotime($p['uploaded_at'])) ?></td>
                <td class="action-cell">
                    <form method="POST" class="inline-form">
                        <input type="hidden" name="action"   value="toggle_featured">
                        <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                        <button class="btn btn-ghost btn-sm">
                            <?= $p['is_featured'] ? 'Unfeature' : 'Feature' ?>
                        </button>
                    </form>
                    <form method="POST" class="inline-form" onsubmit="return confirm('Delete this photo?')">
                        <input type="hidden" name="action"   value="delete_photo">
                        <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Log tab (admin only) -->
    <div class="tab-content hidden" id="tab-logs">
        <div class="log-actions">
            <form method="POST" onsubmit="return confirm('Clear the activity log?')">
                <input type="hidden" name="action" value="clear_log">
                <button class="btn btn-danger btn-sm">Clear log</button>
            </form>
        </div>
        <pre class="log-viewer"><?= $logText ? htmlspecialchars($logText) : 'No activity logged yet.' ?></pre>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
