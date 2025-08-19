<?php
require_once '../includes/config.php';

$page_title = 'FAQ';

include '../includes/header.php';
?>

<section class="faq">
    <div class="container">
        <h1>Questions fréquemment posées</h1>
        
        <div class="faq-content">
            <div class="faq-item">
                <h3>Quels moyens de paiement acceptez-vous ?</h3>
                <p>Nous acceptons les cartes bancaires (Visa, Mastercard, American Express), PayPal et Mobile Money.</p>
            </div>
            
            <div class="faq-item">
                <h3>Combien de temps dure la livraison ?</h3>
                <p>La plupart des commandes sont expédiées sous 24-48h et arrivent en 2-5 jours ouvrés selon votre localisation.</p>
            </div>
            
            <div class="faq-item">
                <h3>Quelle est votre politique de retour ?</h3>
                <p>Nous offrons un retour gratuit sous 30 jours pour tous les articles non utilisés dans leur emballage d'origine.</p>
            </div>
            
            <div class="faq-item">
                <h3>Les produits sont-ils garantis ?</h3>
                <p>Oui, tous nos produits bénéficient de la garantie fabricant standard et de notre garantie satisfaction client.</p>
            </div>
            
            <div class="faq-item">
                <h3>Puis-je annuler ma commande ?</h3>
                <p>Vous pouvez annuler votre commande tant qu'elle n'a pas été expédiée. Contactez-nous rapidement pour cela.</p>
            </div>
            
            <div class="faq-item">
                <h3>Livrez-vous à l'international ?</h3>
                <p>Oui, nous livrons dans la plupart des pays. Les frais de livraison et délais varient selon la destination.</p>
            </div>
            
            <div class="faq-item">
                <h3>Comment suivre ma commande ?</h3>
                <p>Vous recevrez un email avec un numéro de suivi dès que votre commande sera expédiée.</p>
            </div>
            
            <div class="faq-item">
                <h3>Proposez-vous le support technique ?</h3>
                <p>Oui, notre équipe technique est disponible pour vous aider avec vos produits. Contactez-nous par email ou téléphone.</p>
            </div>
        </div>
        
        <div class="faq-contact">
            <p>Vous ne trouvez pas la réponse à votre question ? <a href="contact.php">Contactez-nous</a> directement !</p>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>