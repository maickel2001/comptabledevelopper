Ola Store Electronics — Minimal Apple-style PHP Shop

Overview
- Full-stack PHP + MySQL e-commerce in a minimalist, liquid-glass aesthetic.
- Features: product catalog, search with suggestions, cart, checkout, auth, customer area, admin dashboard.

Requirements
- PHP 8.1+
- MySQL 8.x (or MariaDB 10.4+)
- Apache with mod_php, mod_rewrite enabled (or Nginx pointing to public/)

Setup
1) Create database and import schema
   - mysql -u root -p -e "CREATE DATABASE ola_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   - mysql -u root -p ola_store < sql/schema.sql

2) Configure environment (Apache vhost or .htaccess recognized)
   - Set env vars or edit src/lib/config.php defaults:
     - DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
     - APP_BASE_URL (optional)
     - MAIL_FROM, CONTACT_TO (optional)

3) Deploy
   - Upload all files to your hosting.
   - Point document root to public/.
   - Ensure PHP sessions are writable; create storage/ if needed.

Admin access
- Create an admin user directly:
  INSERT INTO users (name,email,password_hash,is_admin,created_at) VALUES ('Admin','admin@example.com', PASSWORD('change_me'), 1, NOW());
  Replace PASSWORD('...') with PHP hash: run `php -r "echo password_hash('change_me', PASSWORD_DEFAULT);"` and paste the string into password_hash field.

SEO
- Edit <title> and meta description in templates/layout.php.
- Serve friendly URLs by uncommenting the front-controller rewrite rule in public/.htaccess if desired.

Notes
- Email uses PHP mail(); in dev it logs to storage/logs/mail.log when not deliverable.
- For real payments, integrate a provider (Stripe/PayPal) in checkout flow.

# comptabledevelopper