<?php
$pageTitle = 'Home — CMS';
require_once 'config/app.php';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-left">
            <span class="hero-tag">Content Management System</span>
            <h1 class="hero-title">
                Manage your<br>
                content, <span class="hl">your way</span>
            </h1>
            <p class="hero-sub">
                Upload files, organise them with tags, control who sees what.
                Three user roles, one clean interface.
            </p>
            <?php if (!$auth->check()): ?>
                <div class="hero-actions">
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary">Create account</a>
                    <a href="<?= BASE_URL ?>/login.php"    class="btn btn-outline">Login</a>
                </div>
            <?php else: ?>
                <div class="hero-actions">
                    <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="hero-right">
            <div class="hero-stat">
                <span class="hero-stat-num">5</span>
                <div class="hero-stat-label">
                    <strong>Pages</strong>
                    Home, register, login, dashboard, contact
                </div>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-num">3</span>
                <div class="hero-stat-label">
                    <strong>User roles</strong>
                    Admin, moderator, user
                </div>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-num">4</span>
                <div class="hero-stat-label">
                    <strong>Database tables</strong>
                    With 1:N and N:N relations
                </div>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <div class="section-header">
            <span class="section-num">01</span>
            <h2 class="section-title">What it does</h2>
            <span class="section-divider"></span>
        </div>
        <div class="feature-list">
            <div class="feature-item">
                <span class="feature-num">01</span>
                <div class="feature-body">
                    <h3>File uploads with tagging</h3>
                    <p>Upload images, PDFs, and text files. Attach comma-separated tags to each file — stored in a separate table with a many-to-many relationship.</p>
                </div>
            </div>
            <div class="feature-item">
                <span class="feature-num">02</span>
                <div class="feature-body">
                    <h3>Role-based access control</h3>
                    <p>Three tiers: admin sees and controls everything, moderator has elevated access, regular users manage only their own files.</p>
                </div>
            </div>
            <div class="feature-item">
                <span class="feature-num">03</span>
                <div class="feature-body">
                    <h3>Email-tied registration</h3>
                    <p>Accounts are bound to a unique email address. Passwords are hashed with bcrypt. Sessions handle authentication after login.</p>
                </div>
            </div>
            <div class="feature-item">
                <span class="feature-num">04</span>
                <div class="feature-body">
                    <h3>Admin activity log</h3>
                    <p>Every admin action — role change, user deletion, file removal — is written to a log file. Admins can read and clear it from the panel.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="color-section">
    <div class="container">
        <div class="section-header">
            <span class="section-num">02</span>
            <h2 class="section-title">Color picker</h2>
            <span class="section-divider"></span>
        </div>
        <p style="color:var(--text-2);font-size:.875rem;margin-bottom:0;">Pick any color — see it in HEX, RGBA, and HSL. Click a value to copy it.</p>
        <div class="color-tool">
            <input type="color" id="colorPicker" value="#c9a227">
            <div>
                <div class="color-values">
                    <span class="badge" id="hexVal">#C9A227</span>
                    <span class="badge" id="rgbaVal">rgba(201,162,39,1)</span>
                    <span class="badge" id="hslVal">hsl(43,67%,47%)</span>
                </div>
            </div>
            <div class="color-preview" id="colorPreview"></div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
