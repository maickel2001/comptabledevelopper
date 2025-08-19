<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    $product_id = (int)($input['product_id'] ?? 0);
    $quantity = (int)($input['quantity'] ?? 1);
    
    if (!$product_id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID produit manquant']);
        exit;
    }
    
    // Initialiser le panier
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    switch ($action) {
        case 'add':
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            break;
            
        case 'update':
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            break;
            
        case 'remove':
            unset($_SESSION['cart'][$product_id]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Action invalide']);
            exit;
    }
    
    // Calculer le total du panier
    $total = 0;
    $count = 0;
    if (!empty($_SESSION['cart'])) {
        $db = getDB();
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$pid]);
            $product = $stmt->fetch();
            if ($product) {
                $total += $product['price'] * $qty;
                $count += $qty;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'cart_count' => $count,
        'cart_total' => $total,
        'cart' => $_SESSION['cart']
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Récupérer le contenu du panier
    $cart = $_SESSION['cart'] ?? [];
    $total = 0;
    $count = 0;
    $items = [];
    
    if (!empty($cart)) {
        $db = getDB();
        foreach ($cart as $product_id => $quantity) {
            $stmt = $db->prepare("SELECT id, name, price, image_url FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            if ($product) {
                $line_total = $product['price'] * $quantity;
                $total += $line_total;
                $count += $quantity;
                
                $items[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image_url' => $product['image_url'],
                    'quantity' => $quantity,
                    'line_total' => $line_total
                ];
            }
        }
    }
    
    echo json_encode([
        'cart_count' => $count,
        'cart_total' => $total,
        'cart' => $cart,
        'items' => $items
    ]);
    
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
}
?>