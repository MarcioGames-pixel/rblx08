# Roblox 2008 Revival

Independent PHP + MariaDB revival foundation. The application uses PHP includes and PDO; it does not require Laravel, Composer, Docker, or a Node.js build step.

## Current structure

- `index.php`: front controller for the legacy `.aspx` routes.
- `app/bootstrap.php`: configuration, session, PDO, CSRF and shared helpers.
- `app/layout.php`: shared legacy-style page shell.
- `pages/`: individual page controllers.
- `assets/css/site.css`: compatibility stylesheet for the page controllers.
- `tools/import_assets.py`: imports authorized static assets from a local Goodblox folder or ZIP.
- `wayback/`: archival capture tools and references.

## Importing static assets

The asset importer copies only static files from the following directories: `images/`, `CSS/`, `js/`, `scripts/`, `fonts/`, and `content/`. It does not copy PHP files or execute anything.

Only use assets you own or have permission to reuse. The Goodblox reference repository currently has no visible LICENSE file, so its code and artwork should not be redistributed without permission.

From a local clone or extracted ZIP:

```sh
python3 tools/import_assets.py /path/to/goodblox
# or
python3 tools/import_assets.py /path/to/goodblox.zip
```

Files are copied to `assets/goodblox/`, preserving their relative directories. Existing files are skipped unless `--overwrite` is supplied. A SHA-256 manifest is written to `assets/goodblox/assets-manifest.json`.

## Install

1. Import `database/schema.sql` into MariaDB.
2. Copy `config.example.php` to `config.php` and set the database credentials.
3. Configure Apache with `mod_rewrite` enabled.
4. Open `/Default.aspx`.

For a local PHP-only preview, run `php -S 127.0.0.1:8000` and visit `/?page=Default`. PHP's built-in server does not read `.htaccess`.

The legacy-style shell now follows the 900px header/banner/navigation structure seen in the Goodblox reference. The current page controllers still need a page-by-page port; they have not been replaced with copied Goodblox PHP code.
