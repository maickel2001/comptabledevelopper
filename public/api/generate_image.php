<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/lib/helpers.php';
require_once __DIR__ . '/../../src/lib/auth.php';
require_once __DIR__ . '/../../src/lib/db.php';

header('Content-Type: application/json');

if (!is_admin()) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Forbidden']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
if (!verify_csrf_token($input['csrf'] ?? null)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Invalid CSRF']);
  exit;
}

$productId = (int)($input['product_id'] ?? 0);
if ($productId <= 0) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Missing product_id']);
  exit;
}

$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ? LIMIT 1');
$stmt->execute([$productId]);
$product = $stmt->fetch();
if (!$product) {
  http_response_code(404);
  echo json_encode(['ok' => false, 'error' => 'Product not found']);
  exit;
}

$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'OPENAI_API_KEY not configured']);
  exit;
}

$basePrompt = 'Ultra-clean Apple-style product render on white background with liquid glass aesthetic, soft reflection and subtle shadow, studio lighting, high-resolution, minimal, elegant, no text or watermark.';
$userPrompt = trim((string)($input['prompt'] ?? ''));
$name = $product['name'];
$category = $product['category_name'] ?? '';
$brand = $product['brand'] ?? '';
$fullPrompt = trim("$brand $name $category. $basePrompt " . ($userPrompt !== '' ? (" Additional details: " . $userPrompt) : ''));

$payload = json_encode([
  'model' => 'gpt-image-1',
  'prompt' => $fullPrompt,
  'size' => '1024x1024',
  'n' => 1,
  'response_format' => 'b64_json'
]);

$ch = curl_init('https://api.openai.com/v1/images/generations');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
  ],
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_TIMEOUT => 60,
]);
$res = curl_exec($ch);
$err = curl_error($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($res === false || $status >= 400) {
  http_response_code(502);
  echo json_encode(['ok' => false, 'error' => 'Image API error', 'detail' => $err ?: $res]);
  exit;
}

$data = json_decode($res, true);
if (!$data || empty($data['data'][0]['b64_json'])) {
  http_response_code(502);
  echo json_encode(['ok' => false, 'error' => 'Invalid image API response']);
  exit;
}

$b64 = $data['data'][0]['b64_json'];
$bytes = base64_decode($b64, true);
if ($bytes === false) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Failed to decode image']);
  exit;
}

$dir = PUBLIC_PATH . '/assets/images/products';
if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
$filename = 'product_' . $productId . '_' . time() . '.png';
$path = $dir . '/' . $filename;
$ok = @file_put_contents($path, $bytes);
if ($ok === false) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Failed to save image']);
  exit;
}

$url = '/assets/images/products/' . $filename;
$upd = $pdo->prepare('UPDATE products SET image_url = ? WHERE id = ?');
$upd->execute([$url, $productId]);

echo json_encode(['ok' => true, 'url' => $url]);

