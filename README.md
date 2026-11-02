# rblx08

Fresh Roblox 2008-style revival site using plain PHP, MariaDB and `router.php`.

## Structure
- `router.php`: central front controller and legacy .aspx/.php router
- Root page entry points: `Default.php`, `Games.php`, `Catalog.php`, `Browse.php`, `People.php`, `Item.php`, `User.php`, `PlaceItem.php`, `Login.php`, `Register.php`, `Logout.php`, `Forum.php` and `Forum/Default.php`
- `inc/`: database bootstrap, settings, helpers, header and footer
- `pages/index.php`: page controller/views
- `database/schema.sql`: MariaDB schema
- `css/` and `js/`: local styles and scripts
- `images/`: downloaded Roblox logo
- `wayback/`: archived CSS, JS and images after import
- `tools/import_wayback_assets.py`: Wayback static asset mirror
- `.github/workflows/import-wayback.yml`: asset import workflow

## Platform features\n- Ticket balance and purchase ledger; catalog purchases are atomic and inventory-safe.\n- Optional HTTPS browser-game launch links (not a Roblox legacy-client emulator).\n- Friend requests, private messages, reporting, moderation queue, account suspension/ban and admin catalog/place tools.\n- Robux is a simulated test balance only; there are no real-money payments.\n\n## Setup
1. Import `database/schema.sql` into MariaDB.
2. For an existing database, apply `database/migrations/001_platform_features.sql` after the original schema.\n3. Copy `inc/config.example.php` to `inc/config.php` and set database credentials.
4. Point Apache document root to the repository root; enable `mod_rewrite` and `.htaccess`.
5. Local test: `php -S 0.0.0.0:8080 router.php`.
PHP extensions: PDO and pdo_mysql.

## Wayback assets
Open Actions → Import Roblox 2008 assets → Run workflow. It reads the 23 February 2008 capture and downloads archived CSS, JavaScript and images. The script only recovers files preserved by the Internet Archive.
\n## Administrator setup\nAfter registering your first account, grant it admin access manually in MariaDB: `UPDATE users SET role='admin' WHERE username='YOUR_USERNAME';`. Moderators can be assigned with `UPDATE users SET role='moderator' WHERE username='YOUR_USERNAME';`. Never expose database credentials or grant admin access to untrusted users.\n