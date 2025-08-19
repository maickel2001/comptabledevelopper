<?php
require_once '../includes/config.php';

$page_title = 'Accueil';

// Récupérer les produits vedettes
$db = getDB();
$featured_products = $db->query("SELECT * FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT 8")->fetchAll();

include '../includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Élégance dans chaque circuit</h1>
            <p>Smartphones, ordinateurs portables, montres connectées et accessoires — sélectionnés avec goût.</p>
            <a href="store.php" class="btn btn-primary btn-large">Acheter maintenant</a>
        </div>
    </div>
</section>

<section class="categories">
    <div class="container">
        <h2>Catégories</h2>
        <div class="categories-grid">
            <a href="store.php?category=smartphones" class="category-card">
                <div class="category-icon">📱</div>
                <h3>Smartphones</h3>
                <p>Les derniers modèles</p>
            </a>
            <a href="store.php?category=laptops" class="category-card">
                <div class="category-icon">💻</div>
                <h3>Ordinateurs portables</h3>
                <p>Performance et style</p>
            </a>
            <a href="store.php?category=smartwatches" class="category-card">
                <div class="category-icon">⌚</div>
                <h3>Montres connectées</h3>
                <p>Technologie portable</p>
            </a>
            <a href="store.php?category=accessories" class="category-card">
                <div class="category-icon">🎧</div>
                <h3>Accessoires</h3>
                <p>Complétez votre setup</p>
            </a>
        </div>
    </div>
</section>

<?php if (!empty($featured_products)): ?>
<section class="featured">
    <div class="container">
        <h2>Produits vedettes</h2>
        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?php echo escape($product['image_url'] ?: 'assets/images/placeholder.jpg'); ?>" 
                         alt="<?php echo escape($product['name']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo escape($product['name']); ?></h3>
                    <p class="product-price"><?php echo number_format($product['price'], 2); ?> €</p>
                    <button class="btn btn-primary add-to-cart" data-id="<?php echo $product['id']; ?>">
                        Ajouter au panier
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="testimonials">
    <div class="container">
        <h2>Avis clients</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p>"Design époustouflant, shopping fluide."</p>
                <span class="author">— Alex</span>
            </div>
            <div class="testimonial-card">
                <p>"La qualité des produits est exceptionnelle."</p>
                <span class="author">— Jordan</span>
            </div>
            <div class="testimonial-card">
                <p>"Commande simple et rapide."</p>
                <span class="author">— Casey</span>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>