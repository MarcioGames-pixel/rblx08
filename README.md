# Roblox 2008 Revival

Independent PHP + MariaDB revival. The application uses plain PHP includes and PDO; it does not require Laravel, Composer, Docker, or a Node.js build step.

## Structure

- `index.php`: front controller for legacy `.aspx` routes.
- `app/bootstrap.php`: configuration, sessions, PDO, CSRF and shared helpers.
- `app/layout.php`: Goodblox-style legacy header, banner, navigation and footer.
- `pages/`: individual PHP controllers for home, games, catalog, people, profiles, items, places, inventory, login and forum.
- `AllCSSnew.css`: original Goodblox legacy stylesheet used as the visual baseline.
- `tools/download_goodblox_assets.py`: downloads the static images and stylesheet from the Goodblox reference repository.
- `tools/import_assets.py`: imports static assets from a local Goodblox checkout or ZIP.
- `wayback/`: historical Roblox capture tools.

## Download the Goodblox assets

You can run the downloader from the repository root:

```sh
python3 tools/download_goodblox_assets.py
```

It downloads `AllCSSnew.css` and files under `images/`, keeping the original folder paths. Existing files are skipped. Use `--overwrite` to replace them or `--dry-run` to list what would be downloaded.

You can also start it from GitHub Actions: open **Actions → Download Goodblox static assets → Run workflow**. The workflow downloads the assets and commits the result to `main`.

The source code and layout are being adapted to this repository's PHP + MariaDB schema. Legacy database calls and server-specific settings are not copied. The profile, catalog and game pages use the local `database/schema.sql` tables; some gameplay/economy features are not implemented yet.

## Install

1. Import `database/schema.sql` into MariaDB.
2. Copy `config.example.php` to `config.php` and set the database credentials.
3. Configure Apache with `mod_rewrite` enabled.
4. Open `/Default.aspx`.

For a local preview, run `php -S 127.0.0.1:8000` and visit `/?page=Default`. PHP's built-in server does not process `.htaccess`.

Never commit `config.php` or real passwords. The Goodblox source and visual assets are included with permission as confirmed by the project owner.
