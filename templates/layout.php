<?php
require_once __DIR__ . '/../src/lib/helpers.php';
$title = $title ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?></title>
    <meta name="description" content="Premium electronics in a minimalist, elegant store.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= esc(asset_url('css/styles.css')) ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main>
        <?= $content ?>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
    <script src="<?= esc(asset_url('js/app.js')) ?>" defer></script>
</body>
</html>
