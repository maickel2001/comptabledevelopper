<?php
require_once '../includes/config.php';

$page_title = 'Politique de confidentialité';

include '../includes/header.php';
?>

<section class="privacy">
    <div class="container">
        <h1>Politique de confidentialité</h1>
        
        <div class="privacy-content">
            <div class="privacy-section">
                <h2>🔒 Protection de vos données</h2>
                <p>Chez Ola Store Electronics, nous nous engageons à protéger votre vie privée et vos données personnelles. Cette politique explique comment nous collectons, utilisons et protégeons vos informations.</p>
            </div>
            
            <div class="privacy-section">
                <h2>📋 Informations collectées</h2>
                <p>Nous collectons uniquement les informations nécessaires pour :</p>
                <ul>
                    <li>Traiter vos commandes et assurer la livraison</li>
                    <li>Vous contacter concernant votre commande</li>
                    <li>Améliorer votre expérience d'achat</li>
                    <li>Respecter nos obligations légales</li>
                </ul>
                
                <h3>Données collectées :</h3>
                <ul>
                    <li>Nom et coordonnées de contact</li>
                    <li>Adresse de livraison</li>
                    <li>Historique des commandes</li>
                    <li>Préférences de navigation (cookies)</li>
                </ul>
            </div>
            
            <div class="privacy-section">
                <h2>🛡️ Utilisation des données</h2>
                <p>Vos données sont utilisées exclusivement pour :</p>
                <ul>
                    <li>Traiter et expédier vos commandes</li>
                    <li>Communiquer avec vous concernant votre commande</li>
                    <li>Améliorer nos services et produits</li>
                    <li>Respecter nos obligations légales et fiscales</li>
                </ul>
                
                <p><strong>Nous ne vendons jamais vos données à des tiers.</strong></p>
            </div>
            
            <div class="privacy-section">
                <h2>🍪 Cookies</h2>
                <p>Nous utilisons des cookies pour :</p>
                <ul>
                    <li>Mémoriser vos préférences</li>
                    <li>Améliorer la navigation sur notre site</li>
                    <li>Analyser l'utilisation du site (anonymement)</li>
                </ul>
                <p>Vous pouvez désactiver les cookies dans les paramètres de votre navigateur.</p>
            </div>
            
            <div class="privacy-section">
                <h2>🔐 Sécurité</h2>
                <p>Nous mettons en place des mesures de sécurité appropriées pour protéger vos données :</p>
                <ul>
                    <li>Chiffrement SSL pour toutes les transmissions</li>
                    <li>Accès restreint aux données personnelles</li>
                    <li>Surveillance continue de nos systèmes</li>
                    <li>Sauvegardes sécurisées</li>
                </ul>
            </div>
            
            <div class="privacy-section">
                <h2>⏰ Conservation des données</h2>
                <p>Nous conservons vos données :</p>
                <ul>
                    <li>Pendant la durée de votre relation client</li>
                    <li>Jusqu'à 3 ans après votre dernière commande</li>
                    <li>Selon les obligations légales (facturation, etc.)</li>
                </ul>
            </div>
            
            <div class="privacy-section">
                <h2>👤 Vos droits</h2>
                <p>Conformément au RGPD, vous avez le droit de :</p>
                <ul>
                    <li>Accéder à vos données personnelles</li>
                    <li>Rectifier vos informations</li>
                    <li>Demander la suppression de vos données</li>
                    <li>Vous opposer au traitement</li>
                    <li>Demander la portabilité de vos données</li>
                </ul>
            </div>
            
            <div class="privacy-section">
                <h2>📞 Contact</h2>
                <p>Pour toute question concernant cette politique ou pour exercer vos droits :</p>
                <ul>
                    <li>Email : privacy@olastore.dev</li>
                    <li>Adresse : San Francisco, CA, États-Unis</li>
                </ul>
            </div>
            
            <div class="privacy-section">
                <h2>📅 Mise à jour</h2>
                <p>Cette politique peut être mise à jour. La version la plus récente sera toujours disponible sur cette page avec la date de dernière modification.</p>
                <p><em>Dernière mise à jour : <?php echo date('d/m/Y'); ?></em></p>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>