<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="Boutique d'électronique premium avec un design minimaliste et élégant">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">Ola Store</a>
                
                <nav class="nav">
                    <a href="store.php">Boutique</a>
                    <a href="contact.php">Contact</a>
                    <form class="search-form" action="search.php" method="GET">
                        <input type="search" name="q" placeholder="Rechercher..." required>
                        <button type="submit">🔍</button>
                    </form>
                </nav>
                
                <div class="user-menu">
                    <a href="cart.php" class="cart-btn">
                        🛒 <span class="cart-count">0</span>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                        <div class="dropdown">
                            <button class="user-btn">👤 <?php echo escape($_SESSION['user_name']); ?></button>
                            <div class="dropdown-menu">
                                <a href="account.php">Mon compte</a>
                                <?php if (isAdmin()): ?>
                                    <a href="admin/">Administration</a>
                                <?php endif; ?>
                                <a href="logout.php">Déconnexion</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">Connexion</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <main class="main">