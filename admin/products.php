<?php
require_once '../includes/config.php';

// Vérifier que l'utilisateur est admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'Gestion des produits';

$db = getDB();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $category_id = (int)($_POST['category_id'] ?? 0);
        $brand = trim($_POST['brand'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');
        $specifications = trim($_POST['specifications'] ?? '');
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        if ($action === 'create') {
            $stmt = $db->prepare("INSERT INTO products (name, description, price, stock, category_id, brand, image_url, specifications, featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $description, $price, $stock, $category_id, $brand, $image_url, $specifications, $featured]);
            $success = 'Produit créé avec succès !';
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?, brand=?, image_url=?, specifications=?, featured=? WHERE id=?");
            $stmt->execute([$name, $description, $price, $stock, $category_id, $brand, $image_url, $specifications, $featured, $id]);
            $success = 'Produit mis à jour avec succès !';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Produit supprimé avec succès !';
    }
}

// Récupérer les produits
$products = $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();

// Récupérer les catégories pour le formulaire
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

include '../includes/header.php';
?>

<section class="admin-products">
    <div class="container">
        <div class="admin-header">
            <h1>Gestion des produits</h1>
            <button class="btn btn-primary" onclick="showCreateForm()">+ Nouveau produit</button>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo escape($success); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <div class="products-form" id="productForm" style="display: none;">
                <h2 id="formTitle">Créer un produit</h2>
                <form method="POST" class="form">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="formId" value="">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nom du produit *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Catégorie *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo escape($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Prix *</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock">Stock *</label>
                            <input type="number" id="stock" name="stock" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="brand">Marque</label>
                        <input type="text" id="brand" name="brand">
                    </div>
                    
                    <div class="form-group">
                        <label for="image_url">URL de l'image</label>
                        <input type="url" id="image_url" name="image_url" placeholder="https://...">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="specifications">Spécifications techniques</label>
                        <textarea id="specifications" name="specifications" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox">
                            <input type="checkbox" id="featured" name="featured">
                            Produit vedette
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <button type="button" class="btn btn-secondary" onclick="hideForm()">Annuler</button>
                    </div>
                </form>
            </div>
            
            <div class="products-list">
                <h2>Liste des produits</h2>
                
                <?php if (empty($products)): ?>
                    <p>Aucun produit pour le moment.</p>
                <?php else: ?>
                    <div class="products-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Vedette</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo escape($product['image_url'] ?: '../assets/images/placeholder.jpg'); ?>" 
                                             alt="<?php echo escape($product['name']); ?>" class="product-thumb">
                                    </td>
                                    <td><?php echo escape($product['name']); ?></td>
                                    <td><?php echo escape($product['category_name']); ?></td>
                                    <td><?php echo number_format($product['price'], 2); ?> €</td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td><?php echo $product['featured'] ? '⭐' : ''; ?></td>
                                    <td>
                                        <button class="btn btn-small" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">Modifier</button>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-small btn-danger">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function showCreateForm() {
    document.getElementById('productForm').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Créer un produit';
    document.getElementById('formAction').value = 'create';
    document.getElementById('formId').value = '';
    document.getElementById('name').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('price').value = '';
    document.getElementById('stock').value = '';
    document.getElementById('brand').value = '';
    document.getElementById('image_url').value = '';
    document.getElementById('description').value = '';
    document.getElementById('specifications').value = '';
    document.getElementById('featured').checked = false;
}

function hideForm() {
    document.getElementById('productForm').style.display = 'none';
}

function editProduct(product) {
    document.getElementById('productForm').style.display = 'block';
    document.getElementById('formTitle').textContent = 'Modifier le produit';
    document.getElementById('formAction').value = 'update';
    document.getElementById('formId').value = product.id;
    document.getElementById('name').value = product.name;
    document.getElementById('category_id').value = product.category_id;
    document.getElementById('price').value = product.price;
    document.getElementById('stock').value = product.stock;
    document.getElementById('brand').value = product.brand || '';
    document.getElementById('image_url').value = product.image_url || '';
    document.getElementById('description').value = product.description || '';
    document.getElementById('specifications').value = product.specifications || '';
    document.getElementById('featured').checked = product.featured == 1;
}
</script>

<?php include '../includes/footer.php'; ?>