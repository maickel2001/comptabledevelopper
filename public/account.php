<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/db.php';

require_login();
$pdo = get_pdo();
$user = current_user();

$orders = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$orders->execute([$user['id']]);
$orders = $orders->fetchAll();

ob_start();
?>
<section class="account">
  <h1>Welcome, <?= esc($user['name'] ?: $user['email']) ?></h1>
  <div class="grid-2">
    <div class="glass">
      <h3>Profile</h3>
      <div>Email: <?= esc($user['email']) ?></div>
      <div>Name: <?= esc($user['name']) ?></div>
    </div>
    <div class="glass">
      <h3>Orders</h3>
      <?php if (!$orders): ?>
        <div>No orders yet.</div>
      <?php else: ?>
        <div class="orders">
          <?php foreach ($orders as $o): ?>
            <div class="order">
              <div>#<?= (int)$o['id'] ?> — <?= esc($o['status']) ?></div>
              <div><?= esc(date('M j, Y', strtotime($o['created_at']))) ?></div>
              <div><?= esc(price_format((float)$o['total_amount'])) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Account — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
