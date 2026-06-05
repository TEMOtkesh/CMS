<?php
$pageTitle = 'Contact — Folio';
require_once 'config/app.php';
$sent = false; $errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name)                                      $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))  $errors[] = 'A valid email is required.';
    if (!$subject)                                   $errors[] = 'Subject is required.';
    if (strlen($message) < 10)                       $errors[] = 'Message must be at least 10 characters.';

    if (empty($errors)) $sent = true;
}

require_once 'includes/header.php';
?>
<div class="container contact-page">
    <div class="contact-grid">
        <div class="contact-info">
            <h1>Get in touch</h1>
            <p>Have a question, a collaboration idea, or want to join the network? Send us a message.</p>
            <ul class="contact-details">
                <li><strong>Email</strong> hello@folio.dev</li>
                <li><strong>Location</strong> Tbilisi, Georgia</li>
            </ul>
        </div>
        <div class="contact-form-wrap">
            <?php if ($sent): ?>
                <div class="alert alert-success">Message sent! We&apos;ll get back to you soon.</div>
            <?php endif; ?>
            <?php foreach ($errors as $e): ?>
                <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
            <form method="POST" action="contact.php" class="form" id="contactForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Your name</label>
                        <input type="text" id="name" name="name" required
                               placeholder="Jane Smith" autocomplete="name"
                               maxlength="100" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" id="email" name="email" required
                               placeholder="you@example.com" autocomplete="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required
                           placeholder="What is this about?" maxlength="200"
                           value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required
                              minlength="10" placeholder="Your message..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send message</button>
            </form>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
