<?php
require_once __DIR__ . '/../../src/lib/helpers.php';
require_once __DIR__ . '/../../src/lib/auth.php';
$user = current_user();
?>
<header class="site-header glass">
  <div class="container header-inner">
    <a href="<?= esc(url('index.php')) ?>" class="logo">Ola Store</a>
    <nav class="nav">
      <a href="<?= esc(url('store.php')) ?>">Store</a>
      <a href="<?= esc(url('contact.php')) ?>">Contact</a>
      <form class="search" action="<?= esc(url('search.php')) ?>" method="get">
        <input type="search" name="q" placeholder="Search products" autocomplete="off" id="search-input">
      </form>
    </nav>
    <div class="actions">
      <a href="<?= esc(url('cart.php')) ?>" class="cart-icon" aria-label="Cart">
        <span id="cart-count" class="badge">0</span>
      </a>
      <?php if ($user): ?>
        <div class="dropdown">
          <button class="avatar"><?= esc(substr($user['name'] ?? $user['email'], 0, 1)) ?></button>
          <div class="menu">
            <a href="<?= esc(url('account.php')) ?>">Account</a>
            <?php if (is_admin()): ?>
              <a href="<?= esc(url('admin/index.php')) ?>">Admin</a>
            <?php endif; ?>
            <a href="<?= esc(url('logout.php')) ?>">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= esc(url('login.php')) ?>" class="btn">Sign in</a>
      <?php endif; ?>
    </div>
  </div>
</header>
