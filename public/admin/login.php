<?php
require_once __DIR__ . '/../../src/lib/helpers.php';
require_once __DIR__ . '/../../src/lib/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? null)) { $errors[] = 'Invalid session'; }
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  if (!$errors) {
    if (login($email, $password) && is_admin()) {
      redirect('index.php');
    } else {
      $errors[] = 'Admin credentials required';
    }
  }
}

ob_start();
?>
<section class="auth">
  <div class="form glass">
    <h1>Admin Sign in</h1>
    <?php if ($errors): ?><div class="errors"><?php foreach ($errors as $e) echo '<div>'.esc($e).'</div>'; ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
      <label>Email<input type="email" name="email" required></label>
      <label>Password<input type="password" name="password" required></label>
      <button class="btn btn-accent" type="submit">Sign in</button>
    </form>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Admin Login — ' . APP_NAME;
include __DIR__ . '/../../templates/layout.php';
