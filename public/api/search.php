<?php
require_once __DIR__ . '/../../src/lib/db.php';
require_once __DIR__ . '/../../src/lib/helpers.php';

header('Content-Type: application/json');
$q = trim($_GET['q'] ?? '');
if ($q === '') { echo json_encode([]); exit; }
$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT id, name FROM products WHERE name LIKE ? ORDER BY popularity DESC LIMIT 10');
$stmt->execute(["%$q%"]); $rows = $stmt->fetchAll();
echo json_encode($rows);
