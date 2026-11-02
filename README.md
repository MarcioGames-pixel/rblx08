# Roblox 2008 Revival

An independent PHP + MariaDB foundation for a classic-era community revival.

Included: legacy routes (Default.aspx, Games.aspx, Browse.aspx, Catalog.aspx, Item.aspx, User.aspx, Inventory.aspx, Login.aspx, Register.aspx and Forum routes), account registration/login, profiles, inventory, catalog, game listings, forum topics/replies, PDO prepared statements, password hashing, CSRF protection, responsive classic-inspired styling, MariaDB schema and GitHub Actions ZIP builds.

## Install
1. Import database/schema.sql into MariaDB.
2. Copy config.example.php to config.php and set database credentials.
3. Configure Apache to serve this directory and enable mod_rewrite.
4. Visit /Default.aspx and register.

Preview: php -S 127.0.0.1:8000 then open /?page=Default. The PHP development server does not process .htaccess.

The included interface is a starter, not a verified pixel-perfect copy. Original 2008 Wayback HTML/CSS/JS/images still need to be recovered and checked page by page. Do not substitute Goodblox assets for Wayback originals. Archive source references and the capture workflow can be added under wayback/original. Never commit config.php or real passwords.
