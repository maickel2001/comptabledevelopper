<?php
require_once '../includes/config.php';

$page_title = 'Livraison et retours';

include '../includes/header.php';
?>

<section class="shipping">
    <div class="container">
        <h1>Livraison et retours</h1>
        
        <div class="shipping-content">
            <div class="shipping-section">
                <h2>🚚 Livraison</h2>
                <div class="shipping-info">
                    <h3>Délais de traitement</h3>
                    <p>Nous traitons et expédions toutes les commandes sous 24-48h ouvrées.</p>
                    
                    <h3>Options de livraison</h3>
                    <ul>
                        <li><strong>Livraison standard :</strong> 3-5 jours ouvrés (gratuite pour les commandes de plus de 50€)</li>
                        <li><strong>Livraison express :</strong> 1-2 jours ouvrés (+9.99€)</li>
                        <li><strong>Livraison internationale :</strong> 5-10 jours ouvrés (frais variables selon la destination)</li>
                    </ul>
                    
                    <h3>Suivi de commande</h3>
                    <p>Vous recevrez un email avec un numéro de suivi dès que votre commande sera expédiée.</p>
                </div>
            </div>
            
            <div class="shipping-section">
                <h2>↩️ Retours et remboursements</h2>
                <div class="shipping-info">
                    <h3>Politique de retour</h3>
                    <p>Nous acceptons les retours sous 30 jours pour tous les articles non utilisés dans leur emballage d'origine.</p>
                    
                    <h3>Comment retourner un article</h3>
                    <ol>
                        <li>Contactez notre service client pour initier le retour</li>
                        <li>Nous vous enverrons une étiquette de retour prépayée</li>
                        <li>Emballez l'article et collez l'étiquette</li>
                        <li>Déposez le colis dans un point relais ou bureau de poste</li>
                    </ol>
                    
                    <h3>Remboursements</h3>
                    <p>Les remboursements sont traités sous 5-7 jours ouvrés après réception de votre retour.</p>
                </div>
            </div>
            
            <div class="shipping-section">
                <h2>📦 Emballage</h2>
                <div class="shipping-info">
                    <p>Tous nos produits sont emballés avec soin pour garantir leur protection pendant le transport. Nous utilisons des matériaux recyclables dans la mesure du possible.</p>
                </div>
            </div>
            
            <div class="shipping-section">
                <h2>❓ Questions ?</h2>
                <div class="shipping-info">
                    <p>Pour toute question concernant la livraison ou les retours, n'hésitez pas à <a href="contact.php">nous contacter</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>