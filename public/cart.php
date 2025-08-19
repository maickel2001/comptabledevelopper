<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/db.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$cart = $_SESSION['cart']; // [productId => quantity]

$pdo = get_pdo();
$items = [];
$subtotal = 0.0;
if ($cart) {
  $ids = implode(',', array_map('intval', array_keys($cart)));
  $rows = $pdo->query("SELECT id, name, price, image_url FROM products WHERE id IN ($ids)")->fetchAll();
  foreach ($rows as $row) {
    $qty = (int)($cart[$row['id']] ?? 0);
    $line = $qty * (float)$row['price'];
    $subtotal += $line;
    $items[] = ['product' => $row, 'qty' => $qty, 'line' => $line];
  }
}

ob_start();
?>
<section class="cart">
  <h1>Your Cart</h1>
  <div class="cart-table glass">
    <div class="cart-head">
      <div>Product</div><div>Qty</div><div>Price</div><div>Total</div>
    </div>
    <?php foreach ($items as $it): $p = $it['product']; ?>
      <div class="cart-row">
        <div class="p">
          <img src="<?= esc($p['image_url'] ?? asset_url('images/placeholder.svg')) ?>" alt="<?= esc($p['name']) ?>">
          <span><?= esc($p['name']) ?></span>
        </div>
        <div class="q">
          <input class="qty" type="number" min="0" value="<?= (int)$it['qty'] ?>" data-id="<?= (int)$p['id'] ?>">
        </div>
        <div class="price"><?= esc(price_format((float)$p['price'])) ?></div>
        <div class="line"><?= esc(price_format((float)$it['line'])) ?></div>
      </div>
    <?php endforeach; ?>
    <?php if (!$items): ?>
      <div class="empty">Your cart is empty.</div>
    <?php endif; ?>
  </div>
  <div class="summary glass">
    <div class="row"><span>Subtotal</span><span id="subtotal" data-value="<?= number_format($subtotal,2,'.','') ?>"><?= esc(price_format($subtotal)) ?></span></div>
    <a class="btn btn-accent" href="<?= esc(url('checkout.php')) ?>">Checkout</a>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Cart — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
