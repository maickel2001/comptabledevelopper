<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/db.php';

$pdo = get_pdo();
$featured = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_featured = 1 ORDER BY p.created_at DESC LIMIT 12")->fetchAll();
$new = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC LIMIT 12")->fetchAll();

$title = APP_NAME . ' — Premium Electronics';

ob_start();
?>
<section class="hero">
  <div class="hero-inner">
    <h1 class="headline">Elegance in Every Circuit.</h1>
    <p class="sub">Smartphones, laptops, smartwatches and accessories — curated with taste.</p>
    <a class="btn btn-accent" href="<?= esc(url('store.php')) ?>">Shop Now</a>
    <div style="margin-top:10px"><a class="btn" href="<?= esc(url('track.php')) ?>">Track Order</a></div>
  </div>
</section>

<section class="categories">
  <h2>Categories</h2>
  <div class="cat-grid">
    <a class="cat-card glass" href="<?= esc(url('store.php', ['category' => 'smartphones'])) ?>">
      <div class="icon">📱</div>
      <div>Smartphones</div>
    </a>
    <a class="cat-card glass" href="<?= esc(url('store.php', ['category' => 'laptops'])) ?>">
      <div class="icon">💻</div>
      <div>Laptops</div>
    </a>
    <a class="cat-card glass" href="<?= esc(url('store.php', ['category' => 'smartwatches'])) ?>">
      <div class="icon">⌚</div>
      <div>Smartwatches</div>
    </a>
    <a class="cat-card glass" href="<?= esc(url('store.php', ['category' => 'accessories'])) ?>">
      <div class="icon">🎧</div>
      <div>Accessories</div>
    </a>
  </div>
</section>

<section class="slider">
  <h2>Featured</h2>
  <div class="carousel" id="featured-carousel">
    <?php foreach ($featured as $product): ?>
      <a class="product-card" href="<?= esc(url('product.php', ['id' => $product['id']])) ?>">
        <img src="<?= esc($product['image_url'] ?? asset_url('images/placeholder.svg')) ?>" alt="<?= esc($product['name']) ?>">
        <div class="product-info">
          <div class="name"><?= esc($product['name']) ?></div>
          <div class="price"><?= esc(price_format((float)$product['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="slider">
  <h2>New Arrivals</h2>
  <div class="carousel" id="new-carousel">
    <?php foreach ($new as $product): ?>
      <a class="product-card" href="<?= esc(url('product.php', ['id' => $product['id']])) ?>">
        <img src="<?= esc($product['image_url'] ?? asset_url('images/placeholder.svg')) ?>" alt="<?= esc($product['name']) ?>">
        <div class="product-info">
          <div class="name"><?= esc($product['name']) ?></div>
          <div class="price"><?= esc(price_format((float)$product['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="testimonials">
  <h2>What Customers Say</h2>
  <div class="reviews">
    <div class="review glass">“Stunning design, buttery-smooth shopping.” — Alex</div>
    <div class="review glass">“The product quality is top-tier. Love it.” — Jordan</div>
    <div class="review glass">“Checkout was simple and fast.” — Casey</div>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Home — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/db.php';

$pdo = get_pdo();
$featured = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_featured = 1 ORDER BY p.created_at DESC LIMIT 12")->fetchAll();
$new = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC LIMIT 12")->fetchAll();

$title = APP_NAME . ' — Premium Electronics';

ob_start();
?>
<section class="hero">
  <div class="hero-inner">
    <h1 class="headline">Elegance in Every Circuit.</h1>
    <p class="sub">Smartphones, laptops, smartwatches and accessories — curated with taste.</p>
    <a class="btn btn-accent" href="<?= esc(url('store.php')) ?>">Shop Now</a>
  </div>
</section>

<section class="categories">
  <h2>Categories</h2>
  <div class="cat-grid">
    <a class="cat-card glass" href="<?= esc(url('store.php', ['category' => 'smartphones'])) ?>">
      <div class="icon">📱</div>
      <div>Smartphones</div>
    </a>
    <a class="cat-card glass" href="<?= esc(url('store.php', ['category' => 'laptops'])) ?>">
      <div class="icon">💻</div>
      <div>Laptops</div>
    </a>
    <a class="cat-card glass" href="<?= esc(url('store.php', ['category' => 'smartwatches'])) ?>">
      <div class="icon">⌚</div>
      <div>Smartwatches</div>
    </a>
    <a class="cat-card glass" href="<?= esc(url('store.php', ['category' => 'accessories'])) ?>">
      <div class="icon">🎧</div>
      <div>Accessories</div>
    </a>
  </div>
</section>

<section class="slider">
  <h2>Featured</h2>
  <div class="carousel" id="featured-carousel">
    <?php foreach ($featured as $product): ?>
      <a class="product-card" href="<?= esc(url('product.php', ['id' => $product['id']])) ?>">
        <img src="<?= esc($product['image_url'] ?? asset_url('images/placeholder.webp')) ?>" alt="<?= esc($product['name']) ?>">
        <div class="product-info">
          <div class="name"><?= esc($product['name']) ?></div>
          <div class="price"><?= esc(price_format((float)$product['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="slider">
  <h2>New Arrivals</h2>
  <div class="carousel" id="new-carousel">
    <?php foreach ($new as $product): ?>
      <a class="product-card" href="<?= esc(url('product.php', ['id' => $product['id']])) ?>">
        <img src="<?= esc($product['image_url'] ?? asset_url('images/placeholder.webp')) ?>" alt="<?= esc($product['name']) ?>">
        <div class="product-info">
          <div class="name"><?= esc($product['name']) ?></div>
          <div class="price"><?= esc(price_format((float)$product['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="testimonials">
  <h2>What Customers Say</h2>
  <div class="reviews">
    <div class="review glass">“Stunning design, buttery-smooth shopping.” — Alex</div>
    <div class="review glass">“The product quality is top-tier. Love it.” — Jordan</div>
    <div class="review glass">“Checkout was simple and fast.” — Casey</div>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Home — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
