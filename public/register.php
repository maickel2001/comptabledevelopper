<?php
require_once '../includes/config.php';

$page_title = 'Créer un compte';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Validation
    if (empty($name)) $errors[] = 'Le nom est requis';
    if (empty($email)) $errors[] = 'L\'email est requis';
    if (empty($password)) $errors[] = 'Le mot de passe est requis';
    if (strlen($password) < 6) $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
    if ($password !== $confirm_password) $errors[] = 'Les mots de passe ne correspondent pas';
    
    if (empty($errors)) {
        $db = getDB();
        
        // Vérifier si l'email existe déjà
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cet email est déjà utilisé';
        } else {
            // Créer l'utilisateur
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
            } else {
                $errors[] = 'Erreur lors de la création du compte';
            }
        }
    }
    
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    }
}

include '../includes/header.php';
?>

<section class="auth">
    <div class="container">
        <div class="auth-form">
            <h1>Créer un compte</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo escape($success); ?></div>
                <div class="auth-links">
                    <a href="login.php" class="btn btn-primary">Se connecter</a>
                </div>
            <?php else: ?>
                <form method="POST" class="form">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" id="name" name="name" value="<?php echo escape($_POST['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo escape($_POST['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        Créer le compte
                    </button>
                </form>
                
                <div class="auth-links">
                    <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>