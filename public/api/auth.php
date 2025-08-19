<?php
require_once __DIR__ . '/../../src/lib/auth.php';
require_once __DIR__ . '/../../src/lib/helpers.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$action = $input['action'] ?? '';

if ($method === 'POST' && $action === 'login') {
  if (!verify_csrf_token($input['csrf'] ?? null)) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid CSRF']); exit; }
  $email = trim($input['email'] ?? '');
  $password = $input['password'] ?? '';
  $ok = login($email, $password);
  echo json_encode(['ok' => $ok]);
  exit;
}

if ($method === 'POST' && $action === 'logout') {
  logout(); echo json_encode(['ok' => true]); exit;
}

echo json_encode(['ok' => false, 'error' => 'Unsupported action']);
