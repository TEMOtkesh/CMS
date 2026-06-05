<?php
$pageTitle = 'Folio — Photo Portfolio Gallery';
require_once 'config/app.php';
require_once 'classes/Photo.php';

$photoModel = new Photo();
$activeTag  = trim($_GET['tag'] ?? '');
$photos     = $photoModel->getAll($activeTag);
$featured   = $photoModel->getFeatured();
$allTags    = $photoModel->getAllTags();

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-left">
            <span class="hero-tag">Photo Portfolio Gallery</span>
            <h1 class="hero-title">
                A place for<br>
                <span class="hl">photographers</span><br>
                to share work
            </h1>
            <p class="hero-sub">
                Browse the community gallery freely. Create an account to upload your own photos, organise them with tags, and build your portfolio.
            </p>
            <?php if (!$auth->check()): ?>
                <div class="hero-actions">
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary">Start uploading</a>
                    <a href="#gallery" class="btn btn-outline">Browse gallery</a>
                </div>
            <?php else: ?>
                <div class="hero-actions">
                    <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-primary">Upload a photo</a>
                    <a href="#gallery" class="btn btn-outline">Browse gallery</a>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($featured)): ?>
        <div class="hero-right">
            <?php foreach (array_slice($featured, 0, 3) as $f): ?>
            <a href="<?= BASE_URL ?>/photo.php?id=<?= $f['id'] ?>" class="hero-thumb">
                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($f['stored_name']) ?>"
                     alt="<?= htmlspecialchars($f['title']) ?>">
                <span class="hero-thumb-label"><?= htmlspecialchars($f['title']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="hero-right hero-right--stats">
            <div class="hero-stat"><span class="hero-stat-num"><?= count($photos) ?></span><div class="hero-stat-label"><strong>Photos</strong>in the gallery</div></div>
            <div class="hero-stat"><span class="hero-stat-num"><?= count($allTags) ?></span><div class="hero-stat-label"><strong>Tags</strong>to explore</div></div>
            <div class="hero-stat"><span class="hero-stat-num">3</span><div class="hero-stat-label"><strong>Roles</strong>admin, mod, user</div></div>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="gallery-section" id="gallery">
    <div class="container">
        <div class="gallery-header">
            <div class="section-header">
                <span class="section-num">01</span>
                <h2 class="section-title"><?= $activeTag ? 'Tag: ' . htmlspecialchars($activeTag) : 'All photos' ?></h2>
                <span class="section-divider"></span>
            </div>
            <?php if (!empty($allTags)): ?>
            <div class="tag-filter">
                <a href="<?= BASE_URL ?>/index.php" class="tag-pill <?= $activeTag === '' ? 'active' : '' ?>">All</a>
                <?php foreach ($allTags as $t): ?>
                    <a href="<?= BASE_URL ?>/index.php?tag=<?= urlencode($t['name']) ?>"
                       class="tag-pill <?= $activeTag === $t['name'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($t['name']) ?>
                        <span class="tag-count"><?= $t['cnt'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (empty($photos)): ?>
            <div class="empty-state">
                <p><?= $activeTag ? 'No photos with this tag yet.' : 'No photos yet — be the first to upload!' ?></p>
                <?php if (!$auth->check()): ?>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary" style="margin-top:1rem">Create account</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-primary" style="margin-top:1rem">Upload a photo</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="masonry-grid" id="masonryGrid">
                <?php foreach ($photos as $p): ?>
                <a href="<?= BASE_URL ?>/photo.php?id=<?= $p['id'] ?>" class="photo-card" data-id="<?= $p['id'] ?>">
                    <div class="photo-card-img">
                        <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['stored_name']) ?>"
                             alt="<?= htmlspecialchars($p['title']) ?>"
                             loading="lazy">
                        <?php if ($p['is_featured']): ?><span class="featured-badge">Featured</span><?php endif; ?>
                    </div>
                    <div class="photo-card-info">
                        <span class="photo-card-title"><?= htmlspecialchars($p['title']) ?></span>
                        <span class="photo-card-author"><?= htmlspecialchars($p['author']) ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="color-section">
    <div class="container">
        <div class="section-header">
            <span class="section-num">02</span>
            <h2 class="section-title">Color picker</h2>
            <span class="section-divider"></span>
        </div>
        <p class="color-desc">Pick a color to set the site accent theme. Your choice is saved and applied on every page. Values shown in HEX, RGBA, and HSL — click to copy.</p>
        <div class="color-tool">
            <input type="color" id="colorPicker" value="#c9a227">
            <div class="color-outputs">
                <div class="color-values">
                    <span class="badge" id="hexVal">#C9A227</span>
                    <span class="badge" id="rgbaVal">rgba(201,162,39,1)</span>
                    <span class="badge" id="hslVal">hsl(43,67%,47%)</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div class="color-preview" id="colorPreview"></div>
                    <button id="resetTheme" class="btn btn-ghost btn-sm">Reset default</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
