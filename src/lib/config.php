<?php
declare(strict_types=1);

// Application configuration

// Timezone
date_default_timezone_set(getenv('APP_TZ') ?: 'UTC');

// Start session as early as possible
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration via environment variables or defaults
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'u634930929_qq');
define('DB_USER', getenv('DB_USER') ?: 'u634930929_qq');
define('DB_PASS', getenv('DB_PASS') ?: 'Ino1234');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));

// Application name
define('APP_NAME', 'Ola Store Electronics');

// Determine base paths
define('ROOT_PATH', realpath(__DIR__ . '/../../'));
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Base URL auto-detection (best effort). Override with APP_BASE_URL env.
function app_base_url(): string {
    $envBase = getenv('APP_BASE_URL');
    if ($envBase) return rtrim($envBase, '/');
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
               (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

// CSRF token utilities
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Environment helper
function env(string $key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

?>
