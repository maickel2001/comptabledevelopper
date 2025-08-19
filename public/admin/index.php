<?php
require_once __DIR__ . '/../../src/lib/helpers.php';
require_once __DIR__ . '/../../src/lib/auth.php';
require_once __DIR__ . '/../../src/lib/db.php';

require_login();
require_admin();
$pdo = get_pdo();

$stats = [
  'products' => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
  'orders' => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
  'sales' => (float)$pdo->query('SELECT COALESCE(SUM(total_amount),0) FROM orders')->fetchColumn(),
];

ob_start();
?>
<section class="admin">
  <h1>Admin Dashboard</h1>
  <div class="grid-3">
    <div class="glass stat"><div class="label">Products</div><div class="value"><?= (int)$stats['products'] ?></div></div>
    <div class="glass stat"><div class="label">Orders</div><div class="value"><?= (int)$stats['orders'] ?></div></div>
    <div class="glass stat"><div class="label">Sales</div><div class="value"><?= esc(price_format($stats['sales'])) ?></div></div>
  </div>
  <div class="grid-2" style="margin-top:18px">
    <div class="glass" style="padding:12px">
      <h3>Top Products</h3>
      <div class="admin-table">
        <div class="thead"><div>Name</div><div>Popularity</div><div>Price</div></div>
        <?php foreach ($pdo->query('SELECT name, popularity, price FROM products ORDER BY popularity DESC LIMIT 5') as $tp): ?>
          <div class="trow" style="grid-template-columns:2fr 1fr 1fr">
            <div><?= esc($tp['name']) ?></div>
            <div><?= (int)$tp['popularity'] ?></div>
            <div><?= esc(price_format((float)$tp['price'])) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="glass" style="padding:12px">
      <h3>Sales (mock)</h3>
      <div style="height:160px; background:linear-gradient(180deg,#e9effb,#fff); border:1px solid var(--border); border-radius:12px"></div>
    </div>
  </div>
  <div class="links">
    <a class="btn" href="<?= esc(url('admin/products.php')) ?>">Manage Products</a>
    <a class="btn" href="<?= esc(url('admin/orders.php')) ?>">Manage Orders</a>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Admin — ' . APP_NAME;
include __DIR__ . '/../../templates/layout.php';
