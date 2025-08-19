<?php
require_once __DIR__ . '/../../src/lib/helpers.php';
require_once __DIR__ . '/../../src/lib/auth.php';
require_once __DIR__ . '/../../src/lib/db.php';

require_login();
require_admin();
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? null)) { http_response_code(400); exit('Invalid CSRF'); }
  $id = (int)($_POST['id'] ?? 0);
  $name = trim($_POST['name'] ?? '');
  $code = strtoupper(trim($_POST['code'] ?? ''));
  $type = $_POST['type'] === 'percent' ? 'percent' : 'fixed';
  $value = (float)($_POST['value'] ?? 0);
  $active = isset($_POST['active']) ? 1 : 0;
  $starts = $_POST['starts_at'] ?: null;
  $ends = $_POST['ends_at'] ?: null;
  $categoryId = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
  $productId = $_POST['product_id'] !== '' ? (int)$_POST['product_id'] : null;

  if ($id > 0) {
    $stmt = $pdo->prepare('UPDATE promotions SET name=?, code=?, discount_type=?, discount_value=?, active=?, starts_at=?, ends_at=?, apply_category_id=?, apply_product_id=? WHERE id=?');
    $stmt->execute([$name, $code, $type, $value, $active, $starts, $ends, $categoryId, $productId, $id]);
  } else {
    $stmt = $pdo->prepare('INSERT INTO promotions (name, code, discount_type, discount_value, active, starts_at, ends_at, apply_category_id, apply_product_id, created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())');
    $stmt->execute([$name, $code, $type, $value, $active, $starts, $ends, $categoryId, $productId]);
  }
  redirect('admin/promotions.php');
}

if (($_GET['action'] ?? '') === 'delete') {
  if (!verify_csrf_token($_GET['csrf'] ?? null)) { http_response_code(400); exit('Invalid CSRF'); }
  $id = (int)($_GET['id'] ?? 0);
  $pdo->prepare('DELETE FROM promotions WHERE id=?')->execute([$id]);
  redirect('admin/promotions.php');
}

$promos = $pdo->query('SELECT * FROM promotions ORDER BY created_at DESC')->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$products = $pdo->query('SELECT id, name FROM products ORDER BY created_at DESC LIMIT 200')->fetchAll();

ob_start();
?>
<section class="admin">
  <h1>Promotions</h1>
  <div class="grid-2">
    <div class="glass">
      <h3>Create / Edit</h3>
      <form method="post" class="form">
        <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
        <input type="hidden" name="id" id="promo-id">
        <label>Name<input type="text" name="name" id="promo-name" required></label>
        <label>Code<input type="text" name="code" id="promo-code" required></label>
        <label>Type
          <select name="type" id="promo-type">
            <option value="percent">Percent %</option>
            <option value="fixed">Fixed $</option>
          </select>
        </label>
        <label>Value<input type="number" step="0.01" name="value" id="promo-value" required></label>
        <label class="checkbox"><input type="checkbox" name="active" id="promo-active"> Active</label>
        <label>Starts At<input type="datetime-local" name="starts_at" id="promo-starts"></label>
        <label>Ends At<input type="datetime-local" name="ends_at" id="promo-ends"></label>
        <label>Apply to Category
          <select name="category_id" id="promo-category">
            <option value="">—</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Apply to Product
          <select name="product_id" id="promo-product">
            <option value="">—</option>
            <?php foreach ($products as $p): ?>
              <option value="<?= (int)$p['id'] ?>"><?= esc($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <button class="btn btn-accent" type="submit">Save</button>
      </form>
    </div>
    <div class="glass">
      <h3>All Promotions</h3>
      <div class="admin-table">
        <div class="thead"><div>Name</div><div>Code</div><div>Type</div><div>Value</div><div>Active</div><div>Actions</div></div>
        <?php foreach ($promos as $pr): ?>
          <div class="trow" style="grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr">
            <div><?= esc($pr['name']) ?></div>
            <div><?= esc($pr['code']) ?></div>
            <div><?= esc($pr['discount_type']) ?></div>
            <div><?= esc($pr['discount_value']) ?></div>
            <div><?= $pr['active'] ? 'Yes' : 'No' ?></div>
            <div>
              <button class="btn small edit" data-json='<?= json_encode($pr, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>'>Edit</button>
              <a class="btn small danger" href="<?= esc(url('admin/promotions.php', ['action'=>'delete','id'=>$pr['id'],'csrf'=>csrf_token()])) ?>" onclick="return confirm('Delete this promo?')">Delete</a>
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
  const map = {id:'promo-id', name:'promo-name', code:'promo-code', discount_type:'promo-type', discount_value:'promo-value', starts_at:'promo-starts', ends_at:'promo-ends', apply_category_id:'promo-category', apply_product_id:'promo-product'};
  for (const [k, id] of Object.entries(map)){
    const el = document.getElementById(id); if (!el) continue;
    if (k === 'discount_type') el.value = p[k];
    else if (k === 'discount_value') el.value = p[k];
    else el.value = p[k] ?? '';
  }
  const active = document.getElementById('promo-active'); if (active) active.checked = !!Number(p.active);
  window.scrollTo({top:0, behavior:'smooth'});
});
</script>
<?php
$content = ob_get_clean();
$title = 'Admin Promotions — ' . APP_NAME;
include __DIR__ . '/../../templates/layout.php';

