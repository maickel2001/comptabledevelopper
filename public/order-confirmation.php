<?php
require_once '../includes/config.php';

$page_title = 'Confirmation de commande';

$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    redirect('index.php');
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('index.php');
}

include '../includes/header.php';
?>

<section class="order-confirmation">
    <div class="container">
        <div class="confirmation-content">
            <div class="success-icon">✅</div>
            <h1>Commande confirmée !</h1>
            <p>Merci pour votre commande. Nous avons reçu votre demande et nous la traiterons dans les plus brefs délais.</p>
            
            <div class="order-details">
                <h2>Détails de la commande</h2>
                <div class="order-info">
                    <div class="info-row">
                        <span>Numéro de commande:</span>
                        <span>#<?php echo $order_id; ?></span>
                    </div>
                    <div class="info-row">
                        <span>Date:</span>
                        <span><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span>Total:</span>
                        <span><?php echo number_format($order['total_amount'], 2); ?> €</span>
                    </div>
                    <div class="info-row">
                        <span>Statut:</span>
                        <span class="status status-pending">En attente</span>
                    </div>
                </div>
            </div>
            
            <div class="next-steps">
                <h3>Prochaines étapes</h3>
                <ol>
                    <li>Nous traiterons votre commande dans les 24h</li>
                    <li>Vous recevrez un email de confirmation</li>
                    <li>Votre commande sera expédiée sous 2-3 jours ouvrés</li>
                    <li>Vous recevrez un email avec le numéro de suivi</li>
                </ol>
            </div>
            
            <div class="actions">
                <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                <a href="store.php" class="btn btn-secondary">Continuer les achats</a>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>