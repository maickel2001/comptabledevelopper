<?php
require_once __DIR__ . '/../../src/lib/helpers.php';
require_once __DIR__ . '/../../src/lib/auth.php';
require_once __DIR__ . '/../../src/lib/db.php';
require_once __DIR__ . '/../../src/lib/mailer.php';

require_login();
require_admin();
$pdo = get_pdo();

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? null)) { http_response_code(400); exit('Invalid CSRF'); }
  $id = (int)($_POST['id'] ?? 0);
  $status = trim($_POST['status'] ?? 'processing');
  $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status, $id]);
  // Email customer about status change (best-effort)
  $ord = $pdo->prepare('SELECT email, name, total_amount FROM orders WHERE id=?');
  $ord->execute([$id]);
  if ($o = $ord->fetch()) {
    $html = '<p>Hi '.esc($o['name']).',</p><p>Your order #'.(int)$id.' status is now <strong>'.esc($status).'</strong>.</p><p>Total: '.esc(price_format((float)$o['total_amount'])).'</p>';
    @send_mail($o['email'], 'Order #'.$id.' status updated', $html);
  }
  redirect('admin/orders.php');
}

$orders = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 200')->fetchAll();

ob_start();
?>
<section class="admin">
  <h1>Orders</h1>
  <div class="glass admin-table">
    <div class="thead"><div>#</div><div>Customer</div><div>Email</div><div>Total</div><div>Status</div><div>Date</div><div>Actions</div></div>
    <?php foreach ($orders as $o): ?>
      <div class="trow">
        <div>#<?= (int)$o['id'] ?></div>
        <div><?= esc($o['name']) ?></div>
        <div><?= esc($o['email']) ?></div>
        <div><?= esc(price_format((float)$o['total_amount'])) ?></div>
        <div><?= esc($o['status']) ?></div>
        <div><?= esc(date('M j, Y', strtotime($o['created_at']))) ?></div>
        <div>
          <form method="post" class="inline">
            <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
            <select name="status">
              <?php foreach (['processing','paid','shipped','delivered','cancelled'] as $s): ?>
                <option value="<?= esc($s) ?>" <?= $o['status']===$s?'selected':'' ?>><?= esc(ucfirst($s)) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn small" type="submit">Update</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Admin Orders — ' . APP_NAME;
include __DIR__ . '/../../templates/layout.php';
