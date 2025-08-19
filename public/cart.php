<?php
require_once '../includes/config.php';

$page_title = 'Panier';

// Initialiser le panier
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];

include '../includes/header.php';
?>

<section class="cart">
    <div class="container">
        <h1>Votre panier</h1>
        
        <?php if (empty($cart)): ?>
        <div class="empty-cart">
            <p>Votre panier est vide.</p>
            <a href="store.php" class="btn btn-primary">Continuer les achats</a>
        </div>
        <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <?php 
                $total = 0;
                $db = getDB();
                foreach ($cart as $product_id => $quantity):
                    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch();
                    if ($product):
                        $line_total = $product['price'] * $quantity;
                        $total += $line_total;
                ?>
                <div class="cart-item" data-id="<?php echo $product_id; ?>">
                    <div class="item-image">
                        <img src="<?php echo escape($product['image_url'] ?: 'assets/images/placeholder.jpg'); ?>" 
                             alt="<?php echo escape($product['name']); ?>">
                    </div>
                    
                    <div class="item-details">
                        <h3><?php echo escape($product['name']); ?></h3>
                        <p class="item-price"><?php echo number_format($product['price'], 2); ?> €</p>
                    </div>
                    
                    <div class="item-quantity">
                        <button class="quantity-btn minus" data-id="<?php echo $product_id; ?>">-</button>
                        <input type="number" value="<?php echo $quantity; ?>" min="1" 
                               class="quantity-input" data-id="<?php echo $product_id; ?>">
                        <button class="quantity-btn plus" data-id="<?php echo $product_id; ?>">+</button>
                    </div>
                    
                    <div class="item-total">
                        <?php echo number_format($line_total, 2); ?> €
                    </div>
                    
                    <button class="remove-item" data-id="<?php echo $product_id; ?>">🗑️</button>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
            
            <div class="cart-summary">
                <h3>Résumé de la commande</h3>
                <div class="summary-row">
                    <span>Sous-total:</span>
                    <span><?php echo number_format($total, 2); ?> €</span>
                </div>
                <div class="summary-row">
                    <span>Livraison:</span>
                    <span>Gratuite</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span><?php echo number_format($total, 2); ?> €</span>
                </div>
                
                <a href="checkout.php" class="btn btn-primary btn-large checkout-btn">
                    Passer la commande
                </a>
                
                <a href="store.php" class="btn btn-secondary">
                    Continuer les achats
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>