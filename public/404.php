<?php
require_once __DIR__ . '/../src/lib/helpers.php';
http_response_code(404);
ob_start();
?>
<section class="page" style="text-align:center">
  <h1>Page not found</h1>
  <p>The page you’re looking for doesn’t exist.</p>
  <a class="btn btn-accent" href="<?= esc(url('index.php')) ?>">Back to Home</a>
  </section>
<?php
$content = ob_get_clean();
$title = '404 — ' . APP_NAME;
include __DIR__ . '/../templates/layout.php';

