<?php
$pageTitle = 'Home — CMS';
require_once 'config/app.php';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <span class="hero-eyebrow">Web Programming Project</span>
        <h1 class="hero-title">A clean <span class="grad">Content</span><br>Management System</h1>
        <p class="hero-sub">Upload files, manage users, and control access — all from one simple dashboard.</p>
        <?php if (!$auth->check()): ?>
            <div class="hero-actions">
                <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary">Get Started &rarr;</a>
                <a href="<?= BASE_URL ?>/login.php"    class="btn btn-outline">Login</a>
            </div>
        <?php else: ?>
            <div class="hero-actions">
                <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-primary">Go to Dashboard &rarr;</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="features">
    <div class="container">
        <p class="section-label">What's included</p>
        <h2 class="section-title">Everything you need</h2>
        <div class="cards">
            <div class="card">
                <div class="card-icon">&#128196;</div>
                <h3>File Uploads</h3>
                <p>Upload images, PDFs, and text files. Tag and organize everything your way.</p>
            </div>
            <div class="card">
                <div class="card-icon">&#128100;</div>
                <h3>User Roles</h3>
                <p>Admin, Moderator, and User roles — each with their own level of access.</p>
            </div>
            <div class="card">
                <div class="card-icon">&#128274;</div>
                <h3>Secure Auth</h3>
                <p>Email-based registration, bcrypt password hashing, and session management.</p>
            </div>
            <div class="card">
                <div class="card-icon">&#128203;</div>
                <h3>Activity Logs</h3>
                <p>Admins can track every action — role changes, deletions, and more.</p>
            </div>
        </div>
    </div>
</section>

<section class="color-section">
    <div class="container">
        <p class="section-label">CSS3 Color Tool</p>
        <h2 class="section-title">Color Picker</h2>
        <p style="text-align:center;color:var(--text-2);margin-top:-.5rem;margin-bottom:0;font-size:.9rem;">Pick any color to see its HEX, RGBA, and HSL values. Click a badge to copy.</p>
        <div class="color-tool">
            <input type="color" id="colorPicker" value="#6366f1">
            <div class="color-values">
                <span class="badge" id="hexVal">#6366F1</span>
                <span class="badge" id="rgbaVal">rgba(99,102,241,1)</span>
                <span class="badge" id="hslVal">hsl(239,84%,67%)</span>
            </div>
            <div class="color-preview" id="colorPreview"></div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
