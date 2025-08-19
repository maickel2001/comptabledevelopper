<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/db.php';

$q = trim($_GET['q'] ?? '');
$pdo = get_pdo();
$results = [];
if ($q !== '') {
  $stmt = $pdo->prepare('SELECT id, name, price, image_url FROM products WHERE name LIKE ? ORDER BY popularity DESC LIMIT 20');
  $stmt->execute(["%$q%"]); $results = $stmt->fetchAll();
}

ob_start();
?>
<section class="search-page">
  <h1>Search</h1>
  <form method="get" class="form glass"><input name="q" value="<?= esc($q) ?>" placeholder="Search products"></form>
  <div class="product-grid">
    <?php foreach ($results as $p): ?>
      <a class="p-card glass" href="<?= esc(url('product.php', ['id' => $p['id']])) ?>">
        <img src="<?= esc($p['image_url'] ?? asset_url('images/placeholder.svg')) ?>" alt="<?= esc($p['name']) ?>">
        <div class="name"><?= esc($p['name']) ?></div>
        <div class="price"><?= esc(price_format((float)$p['price'])) ?></div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Search — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
