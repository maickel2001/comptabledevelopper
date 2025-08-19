<?php
// Configuration de la base de données
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'u634930929_qq');
define('DB_USER', 'u634930929_qq');
define('DB_PASS', 'Ino1234');
define('DB_PORT', 3306);

// Configuration du site
define('SITE_NAME', 'Ola Store Electronics');
define('SITE_URL', 'https://darkslateblue-chicken-503860.hostingersite.com');

// Démarrer la session
session_start();

// Connexion à la base de données
function getDB() {
    try {
        $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Erreur de connexion: " . $e->getMessage());
    }
}

// Fonctions utilitaires
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}
?>