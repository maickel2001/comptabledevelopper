<?php
require_once __DIR__ . '/../../src/lib/helpers.php';
require_once __DIR__ . '/../../src/lib/auth.php';
require_once __DIR__ . '/../../src/lib/db.php';

require_login();
require_admin();
$pdo = get_pdo();

// Create / update category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? null)) { http_response_code(400); exit('Invalid CSRF'); }
  $id = (int)($_POST['id'] ?? 0);
  $name = trim($_POST['name'] ?? '');
  $slug = trim($_POST['slug'] ?? '');
  if ($id > 0) {
    $stmt = $pdo->prepare('UPDATE categories SET name=?, slug=? WHERE id=?');
    $stmt->execute([$name, $slug, $id]);
  } else {
    $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
    $stmt->execute([$name, $slug]);
  }
  redirect('admin/categories.php');
}

// Delete
if (($_GET['action'] ?? '') === 'delete') {
  if (!verify_csrf_token($_GET['csrf'] ?? null)) { http_response_code(400); exit('Invalid CSRF'); }
  $id = (int)($_GET['id'] ?? 0);
  $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
  redirect('admin/categories.php');
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

ob_start();
?>
<section class="admin">
  <h1>Categories</h1>
  <div class="grid-2">
    <div class="glass">
      <h3>Create / Edit</h3>
      <form method="post" class="form">
        <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
        <input type="hidden" name="id" id="cat-id" value="">
        <label>Name<input type="text" name="name" id="cat-name" required></label>
        <label>Slug<input type="text" name="slug" id="cat-slug" required></label>
        <button class="btn btn-accent" type="submit">Save</button>
      </form>
    </div>
    <div class="glass">
      <h3>All Categories</h3>
      <div class="admin-table">
        <div class="thead"><div>Name</div><div>Slug</div><div>Actions</div></div>
        <?php foreach ($categories as $c): ?>
          <div class="trow" style="grid-template-columns:2fr 2fr 1fr">
            <div><?= esc($c['name']) ?></div>
            <div><?= esc($c['slug']) ?></div>
            <div>
              <button class="btn small edit" data-json='<?= json_encode($c, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>'>Edit</button>
              <a class="btn small danger" href="<?= esc(url('admin/categories.php', ['action'=>'delete','id'=>$c['id'],'csrf'=>csrf_token()])) ?>" onclick="return confirm('Delete this category?')">Delete</a>
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
  const c = JSON.parse(btn.getAttribute('data-json'));
  document.getElementById('cat-id').value = c.id;
  document.getElementById('cat-name').value = c.name;
  document.getElementById('cat-slug').value = c.slug;
  window.scrollTo({top:0, behavior:'smooth'});
});
</script>
<?php
$content = ob_get_clean();
$title = 'Admin Categories — ' . APP_NAME;
include __DIR__ . '/../../templates/layout.php';

