# Ola Store Electronics

Une boutique d'électronique premium avec un design minimaliste et élégant, inspiré du style Apple.

## 🚀 Fonctionnalités

### Frontend
- **Page d'accueil** : Hero section, catégories, produits vedettes, témoignages
- **Boutique** : Grille de produits avec filtres et tri
- **Détail produit** : Images, description, spécifications, produits similaires
- **Panier** : Gestion des quantités, calcul automatique du total
- **Checkout** : Formulaire de commande avec validation
- **Compte utilisateur** : Inscription, connexion, historique des commandes
- **Recherche** : Recherche en temps réel des produits
- **Pages statiques** : FAQ, livraison, confidentialité, contact

### Backend
- **Authentification** : Système de connexion sécurisé
- **Gestion des produits** : CRUD complet avec images
- **Gestion des commandes** : Suivi des statuts
- **API REST** : Endpoints pour le panier et la recherche
- **Administration** : Dashboard avec statistiques

### Design
- **Style Apple** : Interface minimaliste et élégante
- **Responsive** : Optimisé pour tous les appareils
- **Animations** : Transitions fluides et effets hover
- **Liquid Glass** : Effets de transparence et ombres

## 🛠️ Technologies

- **Frontend** : HTML5, CSS3, JavaScript (ES6+)
- **Backend** : PHP 8.0+
- **Base de données** : MySQL 8.0+
- **Serveur** : Apache/Nginx
- **Design** : CSS Grid, Flexbox, Animations CSS

## 📁 Structure du projet

```
/workspace/
├── /public/                 # Fichiers publics (racine web)
│   ├── index.php           # Page d'accueil
│   ├── store.php           # Boutique
│   ├── product.php         # Détail produit
│   ├── cart.php            # Panier
│   ├── checkout.php        # Checkout
│   ├── login.php           # Connexion
│   ├── register.php        # Inscription
│   ├── account.php         # Compte utilisateur
│   ├── contact.php         # Contact
│   ├── search.php          # Recherche
│   ├── faq.php             # FAQ
│   ├── shipping.php        # Livraison
│   ├── privacy.php         # Confidentialité
│   ├── order-confirmation.php # Confirmation commande
│   ├── /admin/             # Administration
│   │   ├── index.php       # Dashboard
│   │   └── products.php    # Gestion produits
│   ├── /api/               # API
│   │   └── cart.php        # API panier
│   └── /assets/            # Ressources
│       ├── /css/           # Styles
│       ├── /js/            # JavaScript
│       └── /images/        # Images
├── /includes/               # Fichiers PHP inclus
│   ├── config.php          # Configuration
│   ├── header.php          # En-tête
│   └── footer.php          # Pied de page
├── database.sql             # Schéma de base de données
└── README.md               # Ce fichier
```

## 🚀 Installation et déploiement

### Prérequis
- Serveur web (Apache/Nginx)
- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Extensions PHP : PDO, PDO_MySQL, mbstring

### 1. Téléchargement des fichiers

1. Téléchargez tous les fichiers du projet
2. Uploadez le contenu du dossier `/public` dans le répertoire `public_html` de votre hébergeur
3. Uploadez le dossier `/includes` à la racine de votre site

### 2. Configuration de la base de données

1. Créez une base de données MySQL sur votre hébergeur
2. Importez le fichier `database.sql` dans votre base de données
3. Modifiez le fichier `/includes/config.php` avec vos informations de connexion :

```php
define('DB_HOST', 'votre_host');
define('DB_NAME', 'votre_nom_db');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
define('DB_PORT', 3306);
```

### 3. Configuration du site

Dans `/includes/config.php`, modifiez l'URL de votre site :

```php
define('SITE_URL', 'https://votre-domaine.com');
```

### 4. Permissions des dossiers

Assurez-vous que les dossiers suivants sont accessibles en écriture :
- `/public/assets/images/` (pour les uploads d'images)

### 5. Test de la connexion

Créez un fichier `test_db.php` à la racine pour tester la connexion :

```php
<?php
require_once 'includes/config.php';
try {
    $db = getDB();
    echo "Connexion à la base de données réussie !";
} catch (Exception $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
```

## 👤 Accès administrateur

Un compte administrateur est créé par défaut :
- **Email** : admin@olastore.dev
- **Mot de passe** : admin123

⚠️ **Important** : Changez ce mot de passe après la première connexion !

## 🔧 Configuration avancée

### Personnalisation des couleurs

Modifiez le fichier `/public/assets/css/style.css` pour changer la palette de couleurs :

```css
:root {
    --primary-color: #0071e3;      /* Couleur principale */
    --secondary-color: #6e6e73;    /* Couleur secondaire */
    --accent-color: #34c759;       /* Couleur d'accent */
    --danger-color: #ff3b30;       /* Couleur de danger */
    --text-color: #1d1d1f;         /* Couleur du texte */
    --bg-color: #f5f5f7;           /* Couleur de fond */
}
```

### Ajout de nouvelles catégories

1. Ajoutez la catégorie dans la base de données
2. Créez l'icône correspondante dans le HTML
3. Ajoutez les styles CSS si nécessaire

### Intégration de paiement

Le système est prêt pour l'intégration de :
- Stripe
- PayPal
- Autres processeurs de paiement

## 📱 Responsive Design

Le site est optimisé pour :
- **Mobile** : 320px - 768px
- **Tablet** : 768px - 1024px
- **Desktop** : 1024px+

## 🔒 Sécurité

- Mots de passe hashés avec `password_hash()`
- Protection contre les injections SQL avec PDO
- Validation des données côté serveur
- Sessions sécurisées
- Protection CSRF pour l'administration

## 📊 Performance

- Images optimisées et responsives
- CSS et JavaScript minifiés
- Cache des requêtes de base de données
- Index sur les colonnes fréquemment utilisées

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifiez les informations de connexion dans `config.php`
- Assurez-vous que MySQL est actif
- Vérifiez les permissions de l'utilisateur

### Pages qui ne se chargent pas
- Vérifiez que tous les fichiers sont uploadés
- Vérifiez les permissions des dossiers
- Consultez les logs d'erreur de votre hébergeur

### Images qui ne s'affichent pas
- Vérifiez les chemins dans la base de données
- Assurez-vous que le dossier `images` est accessible
- Vérifiez les permissions des fichiers

## 📞 Support

Pour toute question ou problème :
- Consultez la FAQ du site
- Vérifiez les logs d'erreur
- Contactez le support technique de votre hébergeur

## 📝 Licence

Ce projet est fourni à des fins éducatives et commerciales. Vous êtes libre de le modifier et de l'utiliser selon vos besoins.

## 🔄 Mises à jour

Pour mettre à jour le site :
1. Sauvegardez votre base de données
2. Sauvegardez vos fichiers personnalisés
3. Uploadez les nouveaux fichiers
4. Testez le site en local avant la mise en production

---

**Ola Store Electronics** - Élégance dans chaque circuit 🚀