<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/mailer.php';

$sent = false; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? null)) { $errors[] = 'Invalid session'; }
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $message = trim($_POST['message'] ?? '');
  if ($name === '' || $email === '' || $message === '') $errors[] = 'All fields required';
  if (!$errors) {
    $html = '<p>Contact from: '.esc($name).' ('.esc($email).')</p><p>'.nl2br(esc($message)).'</p>';
    $sent = send_mail(getenv('CONTACT_TO') ?: 'hello@olastore.dev', 'Contact Form', $html);
  }
}

ob_start();
?>
<section class="contact">
  <h1>Contact Us</h1>
  <?php if ($sent): ?><div class="notice glass">Thanks! We will reply soon.</div><?php endif; ?>
  <?php if ($errors): ?><div class="errors glass"><?php foreach ($errors as $e) echo '<div>'.esc($e).'</div>'; ?></div><?php endif; ?>
  <form method="post" class="form glass">
    <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
    <label>Name<input type="text" name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Message<textarea name="message" rows="6" required></textarea></label>
    <button class="btn btn-accent" type="submit">Send</button>
  </form>
</section>
<?php
$content = ob_get_clean();
$title = 'Contact — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
