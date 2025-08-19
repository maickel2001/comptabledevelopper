<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function esc(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '', array $query = []): string {
    $base = app_base_url();
    $path = '/' . ltrim($path, '/');
    if (!empty($query)) {
        $path .= '?' . http_build_query($query);
    }
    return $base . $path;
}

function asset_url(string $path): string {
    return url('assets/' . ltrim($path, '/'));
}

function price_format(float $amount): string {
    return '$' . number_format($amount, 2);
}

function render(string $template, array $data = []): void {
    extract($data, EXTR_SKIP);
    $templateFile = ROOT_PATH . '/templates/' . ltrim($template, '/');
    if (!file_exists($templateFile)) {
        http_response_code(500);
        echo 'Template not found.';
        return;
    }
    ob_start();
    include $templateFile;
    $content = ob_get_clean();
    include ROOT_PATH . '/templates/layout.php';
}

function redirect(string $path, array $query = []): void {
    header('Location: ' . url($path, $query));
    exit;
}

?>
