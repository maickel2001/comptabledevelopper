<?php
require_once __DIR__ . '/../../src/lib/helpers.php';

header('Content-Type: application/json');
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$cart = &$_SESSION['cart'];

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
  $count = array_sum($cart);
  echo json_encode(['count' => (int)$count, 'cart' => $cart]);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$action = $input['action'] ?? '';
$id = isset($input['id']) ? (int)$input['id'] : 0;
$qty = isset($input['qty']) ? max(0, (int)$input['qty']) : 1;

switch ($action) {
  case 'add':
    $cart[$id] = ($cart[$id] ?? 0) + $qty; break;
  case 'set':
    if ($qty <= 0) unset($cart[$id]); else $cart[$id] = $qty; break;
  case 'remove':
    unset($cart[$id]); break;
}

echo json_encode(['ok' => true, 'count' => array_sum($cart), 'cart' => $cart]);
