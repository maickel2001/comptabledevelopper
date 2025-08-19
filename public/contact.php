<?php
require_once '../includes/config.php';

$page_title = 'Contact';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        // Ici vous pouvez ajouter l'envoi d'email
        // Pour l'instant, on simule le succès
        $success = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
    }
}

include '../includes/header.php';
?>

<section class="contact">
    <div class="container">
        <h1>Contactez-nous</h1>
        
        <?php if ($success): ?>
            <div class="success"><?php echo escape($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo escape($error); ?></div>
        <?php endif; ?>
        
        <div class="contact-content">
            <div class="contact-info">
                <h2>Informations de contact</h2>
                <div class="contact-item">
                    <span class="icon">📧</span>
                    <div>
                        <h3>Email</h3>
                        <p>hello@olastore.dev</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <span class="icon">📞</span>
                    <div>
                        <h3>Téléphone</h3>
                        <p>+1 (555) 123-4567</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <span class="icon">📍</span>
                    <div>
                        <h3>Adresse</h3>
                        <p>San Francisco, CA<br>États-Unis</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Envoyez-nous un message</h2>
                <form method="POST" class="form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nom complet *</label>
                            <input type="text" id="name" name="name" value="<?php echo escape($_POST['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo escape($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Sujet *</label>
                        <input type="text" id="subject" name="subject" value="<?php echo escape($_POST['subject'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required><?php echo escape($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>