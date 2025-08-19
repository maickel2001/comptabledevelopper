<?php
require_once '../includes/config.php';

$page_title = 'Commande';

// Vérifier que le panier n'est pas vide
if (empty($_SESSION['cart'])) {
    redirect('cart.php');
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'card';
    
    $errors = [];
    
    // Validation
    if (empty($name)) $errors[] = 'Le nom est requis';
    if (empty($email)) $errors[] = 'L\'email est requis';
    if (empty($address)) $errors[] = 'L\'adresse est requise';
    if (empty($city)) $errors[] = 'La ville est requise';
    if (empty($postal_code)) $errors[] = 'Le code postal est requis';
    
    if (empty($errors)) {
        try {
            $db = getDB();
            $db->beginTransaction();
            
            // Calculer le total
            $total = 0;
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();
                if ($product) {
                    $total += $product['price'] * $quantity;
                }
            }
            
            // Créer la commande
            $stmt = $db->prepare("INSERT INTO orders (user_id, name, email, phone, address, city, postal_code, country, total_amount, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
            $stmt->execute([$user_id, $name, $email, $phone, $address, $city, $postal_code, $country, $total, $payment_method]);
            
            $order_id = $db->lastInsertId();
            
            // Ajouter les articles de la commande
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product_stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
                $product_stmt->execute([$product_id]);
                $product = $product_stmt->fetch();
                if ($product) {
                    $stmt->execute([$order_id, $product_id, $quantity, $product['price']]);
                    
                    // Mettre à jour le stock
                    $update_stmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    $update_stmt->execute([$quantity, $product_id]);
                }
            }
            
            $db->commit();
            
            // Vider le panier
            $_SESSION['cart'] = [];
            
            // Rediriger vers la confirmation
            redirect("order-confirmation.php?id=$order_id");
            
        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = 'Erreur lors de la création de la commande. Veuillez réessayer.';
        }
    }
}

include '../includes/header.php';
?>

<section class="checkout">
    <div class="container">
        <h1>Finaliser votre commande</h1>
        
        <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p class="error"><?php echo escape($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="checkout-content">
            <div class="checkout-form">
                <h2>Informations de livraison</h2>
                <form method="POST" class="form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nom complet *</label>
                            <input type="text" id="name" name="name" value="<?php echo escape($_POST['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo escape($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo escape($_POST['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Adresse *</label>
                        <input type="text" id="address" name="address" value="<?php echo escape($_POST['address'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ville *</label>
                            <input type="text" id="city" name="city" value="<?php echo escape($_POST['city'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="postal_code">Code postal *</label>
                            <input type="text" id="postal_code" name="postal_code" value="<?php echo escape($_POST['postal_code'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="country">Pays</label>
                        <input type="text" id="country" name="country" value="<?php echo escape($_POST['country'] ?? 'France'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Méthode de paiement</label>
                        <select id="payment_method" name="payment_method">
                            <option value="card" <?php echo ($_POST['payment_method'] ?? '') === 'card' ? 'selected' : ''; ?>>Carte bancaire</option>
                            <option value="paypal" <?php echo ($_POST['payment_method'] ?? '') === 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                            <option value="mobile" <?php echo ($_POST['payment_method'] ?? '') === 'mobile' ? 'selected' : ''; ?>>Mobile Money</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        Confirmer la commande
                    </button>
                </form>
            </div>
            
            <div class="order-summary">
                <h2>Résumé de la commande</h2>
                <?php 
                $total = 0;
                $db = getDB();
                foreach ($_SESSION['cart'] as $product_id => $quantity):
                    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch();
                    if ($product):
                        $line_total = $product['price'] * $quantity;
                        $total += $line_total;
                ?>
                <div class="order-item">
                    <span><?php echo escape($product['name']); ?> × <?php echo $quantity; ?></span>
                    <span><?php echo number_format($line_total, 2); ?> €</span>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
                
                <div class="order-total">
                    <span>Total:</span>
                    <span><?php echo number_format($total, 2); ?> €</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>