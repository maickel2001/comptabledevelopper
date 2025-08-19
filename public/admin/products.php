<?php
require_once __DIR__ . '/../../src/lib/helpers.php';
require_once __DIR__ . '/../../src/lib/auth.php';
require_once __DIR__ . '/../../src/lib/db.php';

require_login();
require_admin();
$pdo = get_pdo();

// Create / update product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? null)) { http_response_code(400); exit('Invalid CSRF'); }
  $id = (int)($_POST['id'] ?? 0);
  $name = trim($_POST['name'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $stock = (int)($_POST['stock'] ?? 0);
  $brand = trim($_POST['brand'] ?? '');
  $category_id = (int)($_POST['category_id'] ?? 0);
  $image_url = trim($_POST['image_url'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $is_featured = isset($_POST['is_featured']) ? 1 : 0;

  if ($id > 0) {
    $stmt = $pdo->prepare('UPDATE products SET name=?, price=?, stock=?, brand=?, category_id=?, image_url=?, description=?, is_featured=? WHERE id=?');
    $stmt->execute([$name, $price, $stock, $brand, $category_id, $image_url, $description, $is_featured, $id]);
  } else {
    $stmt = $pdo->prepare('INSERT INTO products (name, price, stock, brand, category_id, image_url, description, is_featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$name, $price, $stock, $brand, $category_id, $image_url, $description, $is_featured]);
  }
  redirect('admin/products.php');
}

// Delete
if (($_GET['action'] ?? '') === 'delete') {
  if (!verify_csrf_token($_GET['csrf'] ?? null)) { http_response_code(400); exit('Invalid CSRF'); }
  $id = (int)($_GET['id'] ?? 0);
  $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
  redirect('admin/products.php');
}

$products = $pdo->query('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC LIMIT 200')->fetchAll();
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

ob_start();
?>
<section class="admin">
  <h1>Products</h1>
  <div class="grid-2">
    <div class="glass">
      <h3>Create / Edit</h3>
      <form method="post" class="form">
        <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
        <input type="hidden" name="id" id="prod-id" value="">
        <label>Name<input type="text" name="name" id="prod-name" required></label>
        <label>Price<input type="number" step="0.01" name="price" id="prod-price" required></label>
        <label>Stock<input type="number" name="stock" id="prod-stock" required></label>
        <label>Brand<input type="text" name="brand" id="prod-brand"></label>
        <label>Category
          <select name="category_id" id="prod-category" required>
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Image URL<input type="url" name="image_url" id="prod-image"></label>
        <label>Description<textarea name="description" id="prod-desc" rows="4"></textarea></label>
        <label class="checkbox"><input type="checkbox" name="is_featured" id="prod-featured"> Featured</label>
        <button class="btn btn-accent" type="submit">Save</button>
      </form>
    </div>
    <div class="glass">
      <h3>All Products</h3>
      <div class="admin-table">
        <div class="thead"><div>Name</div><div>Price</div><div>Stock</div><div>Category</div><div>Featured</div><div>Actions</div></div>
        <?php foreach ($products as $p): ?>
          <div class="trow">
            <div><?= esc($p['name']) ?></div>
            <div><?= esc(price_format((float)$p['price'])) ?></div>
            <div><?= (int)$p['stock'] ?></div>
            <div><?= esc($p['category_name'] ?? '') ?></div>
            <div><?= $p['is_featured'] ? 'Yes' : 'No' ?></div>
            <div>
              <button class="btn small edit" data-json='<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>Edit</button>
              <a class="btn small danger" href="<?= esc(url('admin/products.php', ['action'=>'delete','id'=>$p['id'],'csrf'=>csrf_token()])) ?>" onclick="return confirm('Delete this product?')">Delete</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<script>
document.addEventListener('click', e => {
  const btn = e.target.closest('.edit');
  if (!btn) return;
  const p = JSON.parse(btn.getAttribute('data-json'));
  for (const [k,v] of Object.entries({id:'prod-id', name:'prod-name', price:'prod-price', stock:'prod-stock', brand:'prod-brand', image_url:'prod-image', description:'prod-desc'})) {
    const el = document.getElementById(v); if (el) el.value = p[k] ?? '';
  }
  const cat = document.getElementById('prod-category'); if (cat) cat.value = p.category_id;
  const feat = document.getElementById('prod-featured'); if (feat) feat.checked = !!Number(p.is_featured);
  window.scrollTo({top: 0, behavior: 'smooth'});
});
</script>
<?php
$content = ob_get_clean();
$title = 'Admin Products — ' . APP_NAME;
include __DIR__ . '/../../templates/layout.php';
