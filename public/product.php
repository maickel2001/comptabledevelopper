<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/db.php';

$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ? LIMIT 1');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { http_response_code(404); echo 'Product not found'; exit; }

$rel = $pdo->prepare('SELECT id, name, price, image_url FROM products WHERE category_id = ? AND id <> ? ORDER BY RAND() LIMIT 8');
$rel->execute([(int)$product['category_id'], $id]);
$related = $rel->fetchAll();

// Fetch reviews
$revStmt = $pdo->prepare('SELECT r.*, u.name as user_name FROM reviews r LEFT JOIN users u ON u.id = r.user_id WHERE r.product_id = ? ORDER BY r.created_at DESC LIMIT 20');
$revStmt->execute([$id]);
$reviews = $revStmt->fetchAll();

ob_start();
?>
<section class="product-detail">
  <div class="gallery">
    <img class="main-img" style="transition:transform .3s ease" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'" src="<?= esc($product['image_url'] ?? asset_url('images/placeholder.svg')) ?>" alt="<?= esc($product['name']) ?>">
  </div>
  <div class="info glass">
    <h1><?= esc($product['name']) ?></h1>
    <div class="price"><?= esc(price_format((float)$product['price'])) ?></div>
    <div class="stock <?= $product['stock'] > 0 ? 'in' : 'out' ?>"><?= $product['stock'] > 0 ? 'In stock' : 'Out of stock' ?></div>
    <p class="desc"><?= esc($product['description'] ?? '') ?></p>
    <?php if (!empty($product['specs'])): ?>
      <div class="specs">
        <h3>Technical Specs</h3>
        <pre><?= esc($product['specs']) ?></pre>
      </div>
    <?php endif; ?>
    <button class="btn btn-accent add-to-cart" data-id="<?= (int)$product['id'] ?>">Add to Cart</button>
  </div>
</section>

<section class="reviews">
  <h2>Customer Reviews</h2>
  <div class="reviews-list">
    <?php if (!$reviews): ?>
      <div class="glass" style="padding:12px">No reviews yet.</div>
    <?php else: foreach ($reviews as $r): ?>
      <div class="glass" style="padding:12px; margin-bottom:10px">
        <div><strong><?= esc($r['user_name'] ?: 'Anonymous') ?></strong> — <?= (int)$r['rating'] ?>/5</div>
        <div style="opacity:.9; margin-top:6px"><?= nl2br(esc($r['comment'] ?? '')) ?></div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</section>

<section class="related">
  <h2>Related products</h2>
  <div class="carousel">
    <?php foreach ($related as $p): ?>
      <a class="product-card" href="<?= esc(url('product.php', ['id' => $p['id']])) ?>">
        <img src="<?= esc($p['image_url'] ?? asset_url('images/placeholder.svg')) ?>" alt="<?= esc($p['name']) ?>">
        <div class="product-info">
          <div class="name"><?= esc($p['name']) ?></div>
          <div class="price"><?= esc(price_format((float)$p['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = esc($product['name']) . ' — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
