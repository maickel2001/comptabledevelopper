<?php
/**
 * Test de connexion à la base de données
 * Utilisez ce fichier pour vérifier que votre configuration fonctionne
 */

// Inclure la configuration
require_once 'includes/config.php';

echo "<h1>Test de connexion à la base de données</h1>";
echo "<h2>Configuration actuelle :</h2>";
echo "<ul>";
echo "<li><strong>Host :</strong> " . DB_HOST . "</li>";
echo "<li><strong>Port :</strong> " . DB_PORT . "</li>";
echo "<li><strong>Base de données :</strong> " . DB_NAME . "</li>";
echo "<li><strong>Utilisateur :</strong> " . DB_USER . "</li>";
echo "<li><strong>Mot de passe :</strong> " . (DB_PASS ? '***' : 'Non défini') . "</li>";
echo "</ul>";

echo "<h2>Test de connexion :</h2>";

try {
    $db = getDB();
    echo "<p style='color: green;'>✅ Connexion à la base de données réussie !</p>";
    
    // Test des requêtes de base
    echo "<h3>Test des requêtes :</h3>";
    
    // Test de la table categories
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM categories");
        $result = $stmt->fetch();
        echo "<p>📁 Catégories : " . $result['count'] . " trouvée(s)</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur table categories : " . $e->getMessage() . "</p>";
    }
    
    // Test de la table products
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch();
        echo "<p>📦 Produits : " . $result['count'] . " trouvé(s)</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur table products : " . $e->getMessage() . "</p>";
    }
    
    // Test de la table users
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<p>👥 Utilisateurs : " . $result['count'] . " trouvé(s)</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur table users : " . $e->getMessage() . "</p>";
    }
    
    // Test de la table orders
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
        $result = $stmt->fetch();
        echo "<p>🛒 Commandes : " . $result['count'] . " trouvée(s)</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur table orders : " . $e->getMessage() . "</p>";
    }
    
    // Test des informations de version
    try {
        $stmt = $db->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        echo "<p>🔧 Version MySQL : " . $result['version'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur version : " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion : " . $e->getMessage() . "</p>";
    
    echo "<h3>Solutions possibles :</h3>";
    echo "<ul>";
    echo "<li>Vérifiez que MySQL est démarré sur votre serveur</li>";
    echo "<li>Vérifiez les informations de connexion dans <code>includes/config.php</code></li>";
    echo "<li>Vérifiez que l'utilisateur a les permissions sur la base de données</li>";
    echo "<li>Vérifiez que la base de données existe</li>";
    echo "<li>Vérifiez que le port MySQL est correct (3306 par défaut)</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><strong>Note :</strong> Supprimez ce fichier après avoir vérifié la connexion pour des raisons de sécurité.</p>";
echo "<p><a href='index.php'>← Retour à l'accueil</a></p>";
?>