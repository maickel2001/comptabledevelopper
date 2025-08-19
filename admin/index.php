<?php
require_once '../includes/config.php';

// Vérifier que l'utilisateur est admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'Administration';

$db = getDB();

// Statistiques
$total_products = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_revenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();

// Commandes récentes
$recent_orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Produits populaires
$popular_products = $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.popularity DESC LIMIT 5")->fetchAll();

include '../includes/header.php';
?>

<section class="admin-dashboard">
    <div class="container">
        <div class="admin-header">
            <h1>Tableau de bord</h1>
            <div class="admin-actions">
                <a href="products.php" class="btn btn-primary">Gérer les produits</a>
                <a href="orders.php" class="btn btn-primary">Gérer les commandes</a>
                <a href="categories.php" class="btn btn-primary">Gérer les catégories</a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-content">
                    <h3>Produits</h3>
                    <div class="stat-number"><?php echo $total_products; ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-content">
                    <h3>Commandes</h3>
                    <div class="stat-number"><?php echo $total_orders; ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3>Utilisateurs</h3>
                    <div class="stat-number"><?php echo $total_users; ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h3>Chiffre d'affaires</h3>
                    <div class="stat-number"><?php echo number_format($total_revenue, 2); ?> €</div>
                </div>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="admin-section">
                <h2>Commandes récentes</h2>
                <?php if (empty($recent_orders)): ?>
                    <p>Aucune commande pour le moment.</p>
                <?php else: ?>
                    <div class="orders-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo escape($order['name']); ?></td>
                                    <td><?php echo number_format($order['total_amount'], 2); ?> €</td>
                                    <td>
                                        <span class="status status-<?php echo $order['status']; ?>">
                                            <?php 
                                            $status_labels = [
                                                'pending' => 'En attente',
                                                'processing' => 'En cours',
                                                'shipped' => 'Expédiée',
                                                'delivered' => 'Livrée',
                                                'cancelled' => 'Annulée'
                                            ];
                                            echo $status_labels[$order['status']] ?? $order['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-small">Voir</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="admin-section">
                <h2>Produits populaires</h2>
                <?php if (empty($popular_products)): ?>
                    <p>Aucun produit pour le moment.</p>
                <?php else: ?>
                    <div class="products-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Popularité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popular_products as $product): ?>
                                <tr>
                                    <td><?php echo escape($product['name']); ?></td>
                                    <td><?php echo escape($product['category_name']); ?></td>
                                    <td><?php echo number_format($product['price'], 2); ?> €</td>
                                    <td><?php echo $product['popularity']; ?></td>
                                    <td>
                                        <a href="../product.php?id=<?php echo $product['id']; ?>" class="btn btn-small">Voir</a>
                                        <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-small">Modifier</a>
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

<?php include '../includes/footer.php'; ?>