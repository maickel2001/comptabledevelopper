<?php
require_once __DIR__ . '/../src/lib/helpers.php';
require_once __DIR__ . '/../src/lib/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/mailer.php';

// optional: require_login(); allow guest checkout for now

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
  redirect('cart.php');
}

$pdo = get_pdo();
$cart = $_SESSION['cart'];
$ids = implode(',', array_map('intval', array_keys($cart)));
$rows = $pdo->query("SELECT id, name, price FROM products WHERE id IN ($ids)")->fetchAll();
$subtotal = 0.0;
foreach ($rows as $row) {
  $subtotal += (float)$row['price'] * (int)$cart[$row['id']];
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? null)) { $errors[] = 'Invalid session'; }
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $address = trim($_POST['address'] ?? '');
  $city = trim($_POST['city'] ?? '');
  $zip = trim($_POST['zip'] ?? '');
  $payment = trim($_POST['payment'] ?? 'card');
  if ($name === '' || $email === '' || $address === '' || $city === '' || $zip === '') $errors[] = 'Please fill all fields';

  if (!$errors) {
    $pdo->beginTransaction();
    try {
      $stmt = $pdo->prepare('INSERT INTO orders (user_id, name, email, address, city, zip, payment_method, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
      $userId = current_user()['id'] ?? null;
      $stmt->execute([$userId, $name, $email, $address, $city, $zip, $payment, $subtotal, 'processing']);
      $orderId = (int)$pdo->lastInsertId();

      $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
      foreach ($rows as $row) {
        $qty = (int)$cart[$row['id']];
        $itemStmt->execute([$orderId, (int)$row['id'], $qty, (float)$row['price']]);
        $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?')->execute([$qty, (int)$row['id']]);
      }

      $pdo->commit();
      // Send order confirmation email (best-effort)
      $summary = '<h2>Order #'.$orderId.'</h2><p>Thank you for your order, '.esc($name).'.</p><p>Total: '.esc(price_format($subtotal)).'</p>';
      @send_mail($email, 'Your order #'.$orderId.' confirmation', $summary);
      $_SESSION['cart'] = [];
      redirect('account.php', ['order' => $orderId]);
    } catch (Throwable $e) {
      $pdo->rollBack();
      $errors[] = 'Checkout failed. Please try again.';
    }
  }
}

ob_start();
?>
<section class="checkout">
  <h1>Checkout</h1>
  <?php if ($errors): ?>
    <div class="errors glass"><?php foreach ($errors as $e) echo '<div>'.esc($e).'</div>'; ?></div>
  <?php endif; ?>
  <div class="grid-2">
    <form method="post" class="form glass">
      <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
      <label>Name<input type="text" name="name" required></label>
      <label>Email<input type="email" name="email" required></label>
      <label>Address<input type="text" name="address" required></label>
      <label>City<input type="text" name="city" required></label>
      <label>ZIP<input type="text" name="zip" required></label>
      <label>Payment Method
        <select name="payment">
          <option value="card">Credit Card</option>
          <option value="paypal">PayPal</option>
          <option value="mobile">Mobile Money</option>
        </select>
      </label>
      <button class="btn btn-accent" type="submit">Place Order</button>
    </form>
    <div class="summary glass">
      <h3>Order Summary</h3>
      <div class="row"><span>Subtotal</span><span><?= esc(price_format($subtotal)) ?></span></div>
      <div class="row"><span>Shipping</span><span>Free</span></div>
      <div class="row total"><span>Total</span><span><?= esc(price_format($subtotal)) ?></span></div>
    </div>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Checkout — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';
