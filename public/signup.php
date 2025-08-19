<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? null)) { $errors[] = 'Invalid session'; }
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $password2 = $_POST['password2'] ?? '';
  if ($name === '' || $email === '' || $password === '') $errors[] = 'Fill all fields';
  if ($password !== $password2) $errors[] = 'Passwords do not match';
  if (!$errors) {
    $res = register_user($name, $email, $password);
    if ($res['ok']) {
      login($email, $password);
      redirect('account.php');
    } else { $errors[] = $res['error']; }
  }
}

ob_start();
?>
<section class="auth">
  <div class="form glass">
    <h1>Create account</h1>
    <?php if ($errors): ?><div class="errors"><?php foreach ($errors as $e) echo '<div>'.esc($e).'</div>'; ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
      <label>Name<input type="text" name="name" required></label>
      <label>Email<input type="email" name="email" required></label>
      <label>Password<input type="password" name="password" required></label>
      <label>Confirm Password<input type="password" name="password2" required></label>
      <button class="btn btn-accent" type="submit">Create</button>
    </form>
    <p>Already have an account? <a href="<?= esc(url('login.php')) ?>">Sign in</a></p>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Sign up — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
