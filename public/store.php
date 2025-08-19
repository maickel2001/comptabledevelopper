<?php
require_once '../includes/config.php';

$page_title = 'Boutique';

$db = getDB();

// Filtres
$category = $_GET['category'] ?? '';
$search = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'name';

// Construire la requête
$where = [];
$params = [];

if ($category) {
    $where[] = "c.slug = ?";
    $params[] = $category;
}

if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Tri
$orderBy = match($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'newest' => 'p.created_at DESC',
    default => 'p.name ASC'
};

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $whereClause 
        ORDER BY $orderBy";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Récupérer les catégories pour les filtres
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

include '../includes/header.php';
?>

<section class="store">
    <div class="container">
        <div class="store-header">
            <h1>Boutique</h1>
            
            <div class="filters">
                <form method="GET" class="filters-form">
                    <input type="text" name="q" placeholder="Rechercher..." value="<?php echo escape($search); ?>">
                    
                    <select name="category">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo escape($cat['slug']); ?>" 
                                <?php echo $category === $cat['slug'] ? 'selected' : ''; ?>>
                            <?php echo escape($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="sort">
                        <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Nom A-Z</option>
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Prix croissant</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Prix décroissant</option>
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Plus récents</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </form>
            </div>
        </div>
        
        <?php if (empty($products)): ?>
        <div class="no-products">
            <p>Aucun produit trouvé.</p>
        </div>
        <?php else: ?>
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
</section>

<?php include '../includes/footer.php'; ?>