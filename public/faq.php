<?php
require_once __DIR__ . '/../src/lib/helpers.php';

ob_start();
?>
<section class="page glass" style="padding:18px">
  <h1>FAQ</h1>
  <h3>What payment methods do you accept?</h3>
  <p>We accept credit cards, PayPal, and Mobile Money.</p>
  <h3>How long is shipping?</h3>
  <p>Most orders ship within 24-48 hours and arrive in 2-5 business days.</p>
  <h3>What is your return policy?</h3>
  <p>30-day hassle-free returns. See Shipping & Returns for details.</p>
</section>
<?php
$content = ob_get_clean();
$title = 'FAQ — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';