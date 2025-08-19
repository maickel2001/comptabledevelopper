<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/db.php';

$pdo = get_pdo();
$order = null; $items = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'], $_GET['email'])) {
  $id = (int)$_GET['id'];
  $email = trim($_GET['email']);
  $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND email = ? LIMIT 1');
  $stmt->execute([$id, $email]);
  $order = $stmt->fetch();
  if ($order) {
    $it = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE order_id = ?');
    $it->execute([$order['id']]);
    $items = $it->fetchAll();
  }
}

ob_start();
?>
<section class="track">
  <h1>Track your order</h1>
  <form method="get" class="form glass">
    <label>Order ID<input type="number" name="id" value="<?= esc($_GET['id'] ?? '') ?>" required></label>
    <label>Email<input type="email" name="email" value="<?= esc($_GET['email'] ?? '') ?>" required></label>
    <button class="btn btn-accent" type="submit">Track</button>
  </form>
  <?php if ($order): ?>
    <div class="glass" style="margin-top:12px; padding:12px">
      <div><strong>Order:</strong> #<?= (int)$order['id'] ?></div>
      <div><strong>Status:</strong> <?= esc($order['status']) ?></div>
      <div><strong>Total:</strong> <?= esc(price_format((float)$order['total_amount'])) ?></div>
      <div><strong>Date:</strong> <?= esc(date('M j, Y', strtotime($order['created_at']))) ?></div>
      <h3 style="margin-top:10px">Items</h3>
      <ul>
        <?php foreach ($items as $it): ?>
          <li><?= (int)$it['quantity'] ?> × <?= esc($it['name']) ?> — <?= esc(price_format((float)$it['unit_price'])) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php elseif (isset($_GET['id'])): ?>
    <div class="glass" style="margin-top:12px; padding:12px">No matching order found.</div>
  <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
$title = 'Track Order — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';

