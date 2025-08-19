<?php
require_once __DIR__ . '/../src/lib/helpers.php';

ob_start();
?>
<section class="page glass" style="padding:18px">
  <h1>Privacy Policy</h1>
  <p>We value your privacy. We only collect data necessary to process your orders and improve the experience. We never sell your data. You can request deletion of your personal data at any time.</p>
  <p>For any questions, contact us at hello@olastore.dev.</p>
</section>
<?php
$content = ob_get_clean();
$title = 'Privacy Policy — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';