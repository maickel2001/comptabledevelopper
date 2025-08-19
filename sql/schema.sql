-- MySQL schema for Ola Store Electronics

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(200) NOT NULL,
  brand VARCHAR(120) NULL,
  description TEXT NULL,
  specs TEXT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image_url VARCHAR(500) NULL,
  popularity INT NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  address VARCHAR(255) NOT NULL,
  city VARCHAR(120) NOT NULL,
  zip VARCHAR(30) NOT NULL,
  payment_method VARCHAR(30) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'processing',
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  user_id INT NULL,
  rating TINYINT NOT NULL,
  comment TEXT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed categories
INSERT IGNORE INTO categories (id, name, slug) VALUES
  (1, 'Smartphones', 'smartphones'),
  (2, 'Laptops', 'laptops'),
  (3, 'Smartwatches', 'smartwatches'),
  (4, 'Accessories', 'accessories');

-- Sample products
INSERT INTO products (category_id, name, brand, description, specs, price, stock, image_url, popularity, is_featured, created_at)
VALUES
  (1, 'OlaPhone X', 'Ola', 'A sleek flagship smartphone.', 'Display: 6.1" OLED\nRAM: 8GB\nStorage: 128GB', 999.00, 50, '/assets/images/placeholder.svg', 100, 1, NOW()),
  (2, 'OlaBook Pro 14"', 'Ola', 'Minimalist power laptop.', 'CPU: 8-core\nRAM: 16GB\nSSD: 512GB', 1999.00, 20, '/assets/images/placeholder.svg', 90, 1, NOW()),
  (3, 'OlaWatch S', 'Ola', 'Elegant smartwatch.', 'Case: 42mm\nBattery: 2 days', 349.00, 80, '/assets/images/placeholder.svg', 80, 0, NOW()),
  (4, 'OlaBuds', 'Ola', 'Wireless earbuds.', 'Battery: 24h with case', 149.00, 120, '/assets/images/placeholder.svg', 70, 0, NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

