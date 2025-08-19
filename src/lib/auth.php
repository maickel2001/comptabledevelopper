<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
    return current_user() !== null;
}

function is_admin(): bool {
    $user = current_user();
    return $user && !empty($user['is_admin']);
}

function require_login(): void {
    if (!is_logged_in()) {
        redirect('login.php', ['next' => $_SERVER['REQUEST_URI'] ?? '/']);
    }
}

function require_admin(): void {
    if (!is_admin()) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

function find_user_by_email(string $email): ?array {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function login(string $email, string $password): bool {
    $user = find_user_by_email($email);
    if (!$user) return false;
    if (!password_verify($password, $user['password_hash'])) return false;
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'is_admin' => (bool)$user['is_admin'],
    ];
    return true;
}

function register_user(string $name, string $email, string $password): array {
    $pdo = get_pdo();
    if (find_user_by_email($email)) {
        return ['ok' => false, 'error' => 'Email already registered'];
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, is_admin, created_at) VALUES (?, ?, ?, 0, NOW())');
    $stmt->execute([$name, $email, $hash]);
    return ['ok' => true];
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

?>
