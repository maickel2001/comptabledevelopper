<?php
require_once '../includes/config.php';

$product_id = (int)($_GET['id'] ?? 0);
if (!$product_id) {
    redirect('store.php');
}

$db = getDB();
$stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('store.php');
}

$page_title = $product['name'];

// Produits similaires
$similar_stmt = $db->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT 4");
$similar_stmt->execute([$product['category_id'], $product_id]);
$similar_products = $similar_stmt->fetchAll();

include '../includes/header.php';
?>

<section class="product-detail">
    <div class="container">
        <div class="product-layout">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?php echo escape($product['image_url'] ?: 'assets/images/placeholder.jpg'); ?>" 
                         alt="<?php echo escape($product['name']); ?>">
                </div>
            </div>
            
            <div class="product-info">
                <nav class="breadcrumb">
                    <a href="store.php">Boutique</a> &gt;
                    <a href="store.php?category=<?php echo escape($product['category_name']); ?>"><?php echo escape($product['category_name']); ?></a> &gt;
                    <span><?php echo escape($product['name']); ?></span>
                </nav>
                
                <h1><?php echo escape($product['name']); ?></h1>
                
                <div class="product-meta">
                    <p class="product-category"><?php echo escape($product['category_name']); ?></p>
                    <p class="product-brand"><?php echo escape($product['brand'] ?? ''); ?></p>
                </div>
                
                <div class="product-price">
                    <span class="price"><?php echo number_format($product['price'], 2); ?> €</span>
                </div>
                
                <div class="product-stock">
                    <?php if ($product['stock'] > 0): ?>
                        <span class="in-stock">✅ En stock (<?php echo $product['stock']; ?> disponibles)</span>
                    <?php else: ?>
                        <span class="out-of-stock">❌ Rupture de stock</span>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['description']): ?>
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(escape($product['description'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($product['specifications']): ?>
                <div class="product-specs">
                    <h3>Spécifications</h3>
                    <div class="specs-content">
                        <?php echo nl2br(escape($product['specifications'])); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="product-actions">
                    <?php if ($product['stock'] > 0): ?>
                        <button class="btn btn-primary btn-large add-to-cart" data-id="<?php echo $product['id']; ?>">
                            🛒 Ajouter au panier
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-large" disabled>
                            Indisponible
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($similar_products)): ?>
        <section class="similar-products">
            <h2>Produits similaires</h2>
            <div class="products-grid">
                <?php foreach ($similar_products as $similar): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="product.php?id=<?php echo $similar['id']; ?>">
                            <img src="<?php echo escape($similar['image_url'] ?: 'assets/images/placeholder.jpg'); ?>" 
                                 alt="<?php echo escape($similar['name']); ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <h3><a href="product.php?id=<?php echo $similar['id']; ?>"><?php echo escape($similar['name']); ?></a></h3>
                        <p class="product-price"><?php echo number_format($similar['price'], 2); ?> €</p>
                        <button class="btn btn-primary add-to-cart" data-id="<?php echo $similar['id']; ?>">
                            Ajouter au panier
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>