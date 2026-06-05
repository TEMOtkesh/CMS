<?php
$pageTitle = 'Admin Panel — CMS';
require_once 'classes/Auth.php';
require_once 'classes/User.php';
require_once 'classes/FileManager.php';
require_once 'classes/Logger.php';

$auth    = new Auth();
$auth->requireAdmin();

$userModel   = new User();
$fileManager = new FileManager();
$logger      = new Logger();
$message     = '';
$msgType     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $uid    = (int)($_POST['user_id'] ?? 0);

    if ($action === 'update_role' && $uid) {
        $roleId = (int)$_POST['role_id'];
        $userModel->updateRole($uid, $roleId);
        $logger->write('ROLE_CHANGE', "user_id=$uid new_role_id=$roleId by admin #{$_SESSION['user_id']}");
        $message = 'Role updated.'; $msgType = 'success';
    } elseif ($action === 'toggle_active' && $uid) {
        $userModel->toggleActive($uid);
        $logger->write('TOGGLE_ACTIVE', "user_id=$uid by admin #{$_SESSION['user_id']}");
        $message = 'User status toggled.'; $msgType = 'success';
    } elseif ($action === 'delete_user' && $uid) {
        $userModel->delete($uid);
        $logger->write('DELETE_USER', "user_id=$uid by admin #{$_SESSION['user_id']}");
        $message = 'User deleted.'; $msgType = 'success';
    } elseif ($action === 'delete_file') {
        $fid = (int)$_POST['file_id'];
        $fileManager->delete($fid);
        $logger->write('DELETE_FILE', "file_id=$fid by admin #{$_SESSION['user_id']}");
        $message = 'File deleted.'; $msgType = 'success';
    } elseif ($action === 'clear_log') {
        $logger->clear();
        $message = 'Log cleared.'; $msgType = 'success';
    }
}

$users   = $userModel->all();
$files   = $fileManager->all();
$logText = $logger->read();

require_once 'includes/header.php';
?>
<div class="container admin">
    <h1>Admin Panel</h1>

    <?php if ($message): ?>
        <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="tabs" id="adminTabs">
        <button class="tab-btn active" data-tab="users">Users (<?= count($users) ?>)</button>
        <button class="tab-btn" data-tab="files">Files (<?= count($files) ?>)</button>
        <button class="tab-btn" data-tab="logs">Activity Log</button>
    </div>

    <div class="tab-content" id="tab-users">
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
                            <select name="role_id" onchange="this.form.submit()">
                                <option value="1" <?= $u['role_id']==1?'selected':'' ?>>admin</option>
                                <option value="2" <?= $u['role_id']==2?'selected':'' ?>>moderator</option>
                                <option value="3" <?= $u['role_id']==3?'selected':'' ?>>user</option>
                            </select>
                        </form>
                    </td>
                    <td><?= $u['is_active'] ? '<span class="badge-active">Active</span>' : '<span class="badge-inactive">Disabled</span>' ?></td>
                    <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td class="action-cell">
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="action" value="toggle_active">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button class="btn btn-sm btn-outline">Toggle</button>
                        </form>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <form method="POST" class="inline-form" onsubmit="return confirm('Delete user?')">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="tab-content hidden" id="tab-files">
        <table class="data-table">
            <thead>
                <tr><th>File</th><th>Uploader</th><th>Type</th><th>Tags</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($files as $f): ?>
                <tr>
                    <td><a href="/uploads/<?= htmlspecialchars($f['stored_name']) ?>" target="_blank"><?= htmlspecialchars($f['original_name']) ?></a></td>
                    <td><?= htmlspecialchars($f['uploader']) ?></td>
                    <td><?= htmlspecialchars($f['mime_type']) ?></td>
                    <td><?= htmlspecialchars($f['tags'] ?? '—') ?></td>
                    <td><?= date('d M Y', strtotime($f['uploaded_at'])) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete file?')">
                            <input type="hidden" name="action" value="delete_file">
                            <input type="hidden" name="file_id" value="<?= $f['id'] ?>">
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="tab-content hidden" id="tab-logs">
        <div class="log-actions">
            <form method="POST" onsubmit="return confirm('Clear the log?')">
                <input type="hidden" name="action" value="clear_log">
                <button class="btn btn-danger">Clear Log</button>
            </form>
        </div>
        <pre class="log-viewer"><?= $logText ? htmlspecialchars($logText) : 'No log entries yet.' ?></pre>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
