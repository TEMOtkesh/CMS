<?php
$pageTitle = 'Home — CMS';
require_once 'includes/header.php';
?>
<section class="hero">
    <div class="container">
        <h1 class="hero-title">Welcome to <span>CMS</span></h1>
        <p class="hero-sub">A simple, clean content management system. Upload files, manage users, and stay in control.</p>
        <?php if (!$auth->check()): ?>
            <div class="hero-actions">
                <a href="/register.php" class="btn btn-primary">Get Started</a>
                <a href="/login.php" class="btn btn-outline">Login</a>
            </div>
        <?php else: ?>
            <a href="/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2>Features</h2>
        <div class="cards">
            <div class="card">
                <div class="card-icon">&#128196;</div>
                <h3>File Uploads</h3>
                <p>Upload images, PDFs, and text files. Tag and organize them your way.</p>
            </div>
            <div class="card">
                <div class="card-icon">&#128100;</div>
                <h3>User Roles</h3>
                <p>Admin, Moderator, and User roles with different levels of access.</p>
            </div>
            <div class="card">
                <div class="card-icon">&#128274;</div>
                <h3>Secure Auth</h3>
                <p>Email-based registration with bcrypt password hashing and sessions.</p>
            </div>
        </div>
    </div>
</section>

<section class="color-demo">
    <div class="container">
        <h2>Color Palette</h2>
        <p>Pick a color to preview it in HEX, RGBA, and HSL formats.</p>
        <div class="color-tool">
            <input type="color" id="colorPicker" value="#4f46e5">
            <div class="color-values">
                <span class="badge" id="hexVal">#4f46e5</span>
                <span class="badge" id="rgbaVal">rgba(79,70,229,1)</span>
                <span class="badge" id="hslVal">hsl(243,75%,59%)</span>
            </div>
            <div class="color-preview" id="colorPreview"></div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
