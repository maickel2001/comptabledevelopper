<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/db.php';

$pdo = get_pdo();

$q = trim($_GET['q'] ?? '');
$categorySlug = trim($_GET['category'] ?? '');
$brand = trim($_GET['brand'] ?? '');
$sort = $_GET['sort'] ?? 'popular';
$minPrice = isset($_GET['min']) ? (float)$_GET['min'] : null;
$maxPrice = isset($_GET['max']) ? (float)$_GET['max'] : null;

$where = [];
$params = [];
if ($q !== '') { $where[] = '(p.name LIKE ? OR p.description LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($categorySlug !== '') { $where[] = 'c.slug = ?'; $params[] = $categorySlug; }
if ($brand !== '') { $where[] = 'p.brand = ?'; $params[] = $brand; }
if ($minPrice !== null) { $where[] = 'p.price >= ?'; $params[] = $minPrice; }
if ($maxPrice !== null) { $where[] = 'p.price <= ?'; $params[] = $maxPrice; }
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$orderSql = 'p.popularity DESC, p.created_at DESC';
if ($sort === 'price_asc') $orderSql = 'p.price ASC';
if ($sort === 'price_desc') $orderSql = 'p.price DESC';
if ($sort === 'new') $orderSql = 'p.created_at DESC';

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id $whereSql ORDER BY $orderSql LIMIT 60");
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$brands = $pdo->query('SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand <> "" ORDER BY brand')->fetchAll();

ob_start();
?>
<section class="store">
  <div class="filters glass">
    <form method="get" class="filters-form">
      <div class="row">
        <input type="search" name="q" placeholder="Search" value="<?= esc($q) ?>">
        <select name="category">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= esc($cat['slug']) ?>" <?= $categorySlug === $cat['slug'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <select name="brand">
          <option value="">All Brands</option>
          <?php foreach ($brands as $b): $bn = $b['brand']; ?>
            <option value="<?= esc($bn) ?>" <?= $brand === $bn ? 'selected' : '' ?>><?= esc($bn) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="number" step="0.01" name="min" placeholder="Min $" value="<?= esc((string)($minPrice ?? '')) ?>">
        <input type="number" step="0.01" name="max" placeholder="Max $" value="<?= esc((string)($maxPrice ?? '')) ?>">
        <select name="sort">
          <option value="popular" <?= $sort==='popular'?'selected':'' ?>>Popularity</option>
          <option value="new" <?= $sort==='new'?'selected':'' ?>>Newest</option>
          <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price: Low to High</option>
          <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High to Low</option>
        </select>
        <button class="btn btn-accent" type="submit">Apply</button>
      </div>
    </form>
  </div>

  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <div class="p-card glass">
        <a href="<?= esc(url('product.php', ['id' => $product['id']])) ?>">
          <img src="<?= esc($product['image_url'] ?? asset_url('images/placeholder.svg')) ?>" alt="<?= esc($product['name']) ?>">
          <div class="name"><?= esc($product['name']) ?></div>
          <div class="meta">
            <span class="category"><?= esc($product['category_name'] ?? '') ?></span>
            <span class="price"><?= esc(price_format((float)$product['price'])) ?></span>
          </div>
        </a>
        <button class="btn add-to-cart" data-id="<?= (int)$product['id'] ?>">Add to Cart</button>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Store — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
