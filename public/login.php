<?php
require_once '../includes/config.php';

$page_title = 'Connexion';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            $redirect = $_GET['redirect'] ?? 'index.php';
            redirect($redirect);
        } else {
            $error = 'Email ou mot de passe incorrect';
        }
    }
}

include '../includes/header.php';
?>

<section class="auth">
    <div class="container">
        <div class="auth-form">
            <h1>Connexion</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo escape($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo escape($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large">
                    Se connecter
                </button>
            </form>
            
            <div class="auth-links">
                <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>