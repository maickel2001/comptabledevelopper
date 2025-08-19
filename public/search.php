<?php
require_once '../includes/config.php';

$page_title = 'Recherche';

$search = trim($_GET['q'] ?? '');
$products = [];

if ($search) {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name LIKE ? OR p.description LIKE ? ORDER BY p.name");
    $stmt->execute(["%$search%", "%$search%"]);
    $products = $stmt->fetchAll();
}

include '../includes/header.php';
?>

<section class="search">
    <div class="container">
        <h1>Recherche</h1>
        
        <form method="GET" class="search-form-large">
            <input type="search" name="q" placeholder="Rechercher des produits..." value="<?php echo escape($search); ?>" required>
            <button type="submit" class="btn btn-primary">🔍 Rechercher</button>
        </form>
        
        <?php if ($search): ?>
            <div class="search-results">
                <h2>Résultats pour "<?php echo escape($search); ?>"</h2>
                
                <?php if (empty($products)): ?>
                    <p>Aucun produit trouvé pour votre recherche.</p>
                <?php else: ?>
                    <p><?php echo count($products); ?> produit(s) trouvé(s)</p>
                    
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <a href="product.php?id=<?php echo $product['id']; ?>">
                                    <img src="<?php echo escape($product['image_url'] ?: 'assets/images/placeholder.jpg'); ?>" 
                                         alt="<?php echo escape($product['name']); ?>">
                                </a>
                            </div>
                            <div class="product-info">
                                <h3><a href="product.php?id=<?php echo $product['id']; ?>"><?php echo escape($product['name']); ?></a></h3>
                                <p class="product-category"><?php echo escape($product['category_name']); ?></p>
                                <p class="product-price"><?php echo number_format($product['price'], 2); ?> €</p>
                                <button class="btn btn-primary add-to-cart" data-id="<?php echo $product['id']; ?>">
                                    Ajouter au panier
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>