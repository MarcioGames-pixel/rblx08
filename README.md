# rblx08

A fresh Roblox 2008-inspired revival site using **plain PHP, MariaDB and router.php**.

## Goodblox-style project structure

- `router.php`: central front controller and legacy URL router
- `inc/`: database bootstrap, settings, shared functions, header and footer
- `pages/`: home, games, catalog, people, profiles, login, register and forum
- `database/schema.sql`: MariaDB schema
- `public/css/` and `public/js/`: local CSS and JavaScript
- `public/wayback/`: archived 2008 CSS, JS and images after import
- `tools/import_wayback_assets.py`: Wayback asset mirror
- `.github/workflows/import-wayback.yml`: manual asset-import workflow

## Install

1. Create a MariaDB database and import `database/schema.sql`.
2. Copy `inc/config.example.php` to `inc/config.php` and fill in your database details.
3. Point Apache's document root at the repository, enable `mod_rewrite`, and allow `.htaccess`.
4. For local testing, run `php -S 0.0.0.0:8080 router.php`.

PHP extensions: `PDO` and `pdo_mysql`.

## Import the original 2008 website assets

Go to **Actions → Import Roblox 2008 assets → Run workflow**. It reads the 23 February 2008 Wayback capture, mirrors linked CSS/JS/images into `public/wayback`, and attempts to identify the original Roblox logo. The script only downloads files that the Internet Archive preserved.

## Current scope

This is a fresh site foundation with account registration/login, player directory, games listing, catalog/inventory and forum topics. It is not a Roblox game engine, multiplayer server or legacy client launcher. Git history is preserved, but the new `main` tree replaces the previous working files.
