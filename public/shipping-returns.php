<?php
require_once __DIR__ . '/../src/lib/helpers.php';

ob_start();
?>
<section class="page glass" style="padding:18px">
  <h1>Shipping & Returns</h1>
  <h3>Shipping</h3>
  <p>Orders ship within 24-48 hours. Delivery times vary by location (2-5 business days typical).</p>
  <h3>Returns</h3>
  <p>30-day returns on unused items in original packaging. Start a return by contacting support.</p>
</section>
<?php
$content = ob_get_clean();
$title = 'Shipping & Returns — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';