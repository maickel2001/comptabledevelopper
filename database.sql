-- Schéma de base de données pour Ola Store Electronics
-- Créer la base de données
CREATE DATABASE IF NOT EXISTS `u634930929_qq` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `u634930929_qq`;

-- Table des catégories
CREATE TABLE `categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL UNIQUE,
    `description` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des utilisateurs
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `is_admin` tinyint(1) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des produits
CREATE TABLE `products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL,
    `description` text,
    `price` decimal(10,2) NOT NULL,
    `stock` int(11) NOT NULL DEFAULT 0,
    `category_id` int(11),
    `brand` varchar(100),
    `image_url` varchar(500),
    `specifications` text,
    `featured` tinyint(1) DEFAULT 0,
    `popularity` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes
CREATE TABLE `orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11),
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(20),
    `address` text NOT NULL,
    `city` varchar(100) NOT NULL,
    `postal_code` varchar(20) NOT NULL,
    `country` varchar(100) DEFAULT 'France',
    `total_amount` decimal(10,2) NOT NULL,
    `payment_method` varchar(50) DEFAULT 'card',
    `status` enum('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des articles de commande
CREATE TABLE `order_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `unit_price` decimal(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des avis clients
CREATE TABLE `reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `rating` int(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    `comment` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des promotions
CREATE TABLE `promotions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(50) NOT NULL UNIQUE,
    `description` varchar(200),
    `discount_type` enum('percentage', 'fixed') DEFAULT 'percentage',
    `discount_value` decimal(10,2) NOT NULL,
    `min_amount` decimal(10,2) DEFAULT 0,
    `max_uses` int(11) DEFAULT NULL,
    `used_count` int(11) DEFAULT 0,
    `active` tinyint(1) DEFAULT 1,
    `valid_from` timestamp DEFAULT CURRENT_TIMESTAMP,
    `valid_until` timestamp NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des données de base

-- Catégories
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Smartphones', 'smartphones', 'Téléphones intelligents et mobiles'),
('Ordinateurs portables', 'laptops', 'PC portables et ultrabooks'),
('Montres connectées', 'smartwatches', 'Montres intelligentes et trackers'),
('Accessoires', 'accessories', 'Accessoires et périphériques');

-- Utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `is_admin`) VALUES
('Administrateur', 'admin@olastore.dev', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Produits d'exemple
INSERT INTO `products` (`name`, `description`, `price`, `stock`, `category_id`, `brand`, `image_url`, `specifications`, `featured`, `popularity`) VALUES
('iPhone 15 Pro', 'Le dernier iPhone avec des fonctionnalités avancées et une caméra professionnelle.', 1199.00, 50, 1, 'Apple', 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400', 'Écran: 6.1" OLED\nProcesseur: A17 Pro\nRAM: 8GB\nStockage: 256GB\nCaméra: 48MP + 12MP + 12MP', 1, 95),
('MacBook Air M2', 'Ordinateur portable ultra-léger avec puce M2 pour des performances exceptionnelles.', 1499.00, 30, 2, 'Apple', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400', 'Écran: 13.6" Retina\nProcesseur: M2\nRAM: 8GB\nStockage: 256GB SSD\nPoids: 1.24kg', 1, 88),
('Apple Watch Series 9', 'Montre connectée avec suivi de santé avancé et écran toujours actif.', 399.00, 75, 3, 'Apple', 'https://images.unsplash.com/photo-1544117519-31a4b719223d?w=400', 'Écran: 45mm\nRésistance: IP6X\nBatterie: 18h\nGPS: Intégré\nCapteurs: Cardio, Oxygène', 1, 92),
('AirPods Pro', 'Écouteurs sans fil avec réduction de bruit active et audio spatial.', 249.00, 100, 4, 'Apple', 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=400', 'Audio: Spatial\nRéduction de bruit: Active\nBatterie: 6h\nRésistance: IPX4\nConnexion: Bluetooth 5.0', 1, 85),
('Samsung Galaxy S24', 'Smartphone Android premium avec IA intégrée et caméra polyvalente.', 999.00, 40, 1, 'Samsung', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400', 'Écran: 6.2" AMOLED\nProcesseur: Snapdragon 8 Gen 3\nRAM: 8GB\nStockage: 256GB\nCaméra: 50MP + 12MP + 10MP', 0, 78),
('Dell XPS 13', 'Ultrabook Windows avec design premium et performances élevées.', 1299.00, 25, 2, 'Dell', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400', 'Écran: 13.4" 4K\nProcesseur: Intel i7-1355U\nRAM: 16GB\nStockage: 512GB SSD\nPoids: 1.17kg', 0, 72),
('Garmin Fenix 7', 'Montre multisport avec cartographie et suivi d''activité avancé.', 699.00, 35, 3, 'Garmin', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400', 'Écran: 47mm\nRésistance: 10ATM\nBatterie: 18 jours\nGPS: Multi-fréquence\nCapteurs: Multi-sport', 0, 68),
('Sony WH-1000XM5', 'Casque audio avec réduction de bruit leader du marché.', 399.00, 60, 4, 'Sony', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400', 'Audio: Hi-Res\nRéduction de bruit: NC\nBatterie: 30h\nConnexion: Bluetooth 5.2\nPoids: 250g', 0, 75);

-- Index pour améliorer les performances
CREATE INDEX `idx_products_category` ON `products`(`category_id`);
CREATE INDEX `idx_products_featured` ON `products`(`featured`);
CREATE INDEX `idx_products_popularity` ON `products`(`popularity`);
CREATE INDEX `idx_orders_user` ON `orders`(`user_id`);
CREATE INDEX `idx_orders_status` ON `orders`(`status`);
CREATE INDEX `idx_order_items_order` ON `order_items`(`order_id`);
CREATE INDEX `idx_order_items_product` ON `order_items`(`product_id`);
CREATE INDEX `idx_reviews_product` ON `reviews`(`product_id`);
CREATE INDEX `idx_reviews_user` ON `reviews`(`user_id`);