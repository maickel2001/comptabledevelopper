<?php
require_once '../includes/config.php';

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'Mon compte';

$db = getDB();
$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Récupérer l'historique des commandes
$stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include '../includes/header.php';
?>

<section class="account">
    <div class="container">
        <h1>Mon compte</h1>
        
        <div class="account-content">
            <div class="account-sidebar">
                <div class="user-info">
                    <h3>Informations personnelles</h3>
                    <div class="info-item">
                        <span class="label">Nom :</span>
                        <span><?php echo escape($user['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email :</span>
                        <span><?php echo escape($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Membre depuis :</span>
                        <span><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
                
                <div class="account-actions">
                    <a href="edit-profile.php" class="btn btn-secondary">Modifier le profil</a>
                    <a href="change-password.php" class="btn btn-secondary">Changer le mot de passe</a>
                </div>
            </div>
            
            <div class="account-main">
                <div class="orders-section">
                    <h2>Historique des commandes</h2>
                    
                    <?php if (empty($orders)): ?>
                        <div class="no-orders">
                            <p>Vous n'avez pas encore passé de commande.</p>
                            <a href="store.php" class="btn btn-primary">Découvrir nos produits</a>
                        </div>
                    <?php else: ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                            <div class="order-item">
                                <div class="order-header">
                                    <div class="order-number">
                                        <strong>Commande #<?php echo $order['id']; ?></strong>
                                    </div>
                                    <div class="order-date">
                                        <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                                    </div>
                                    <div class="order-status status-<?php echo $order['status']; ?>">
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
                                    </div>
                                </div>
                                
                                <div class="order-details">
                                    <div class="order-amount">
                                        <strong>Total : <?php echo number_format($order['total_amount'], 2); ?> €</strong>
                                    </div>
                                    <div class="order-address">
                                        <strong>Livraison :</strong><br>
                                        <?php echo escape($order['address']); ?><br>
                                        <?php echo escape($order['postal_code'] . ' ' . $order['city']); ?>
                                    </div>
                                </div>
                                
                                <div class="order-actions">
                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-small">
                                        Voir les détails
                                    </a>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <a href="cancel-order.php?id=<?php echo $order['id']; ?>" class="btn btn-small btn-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                            Annuler
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>