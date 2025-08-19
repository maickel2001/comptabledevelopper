<?php
require_once __DIR__ . '/../src/lib/helpers.php';
http_response_code(500);
ob_start();
?>
<section class="page" style="text-align:center">
  <h1>Unexpected error</h1>
  <p>Something went wrong. Please try again later.</p>
  <a class="btn btn-accent" href="<?= esc(url('index.php')) ?>">Back to Home</a>
</section>
<?php
$content = ob_get_clean();
$title = 'Error — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';

