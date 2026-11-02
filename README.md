# rblx08

Independent Roblox 2008-inspired revival site built with plain PHP, MariaDB and a small router. It is not affiliated with Roblox Corporation.

## Structure
- `router.php`: routes modern PHP paths and legacy .aspx aliases.
- Root pages: `Default.php`, `Games.php`, `Catalog.php`, `Browse.php`, `People.php`, `Item.php`, `User.php`, `PlaceItem.php`, `Login.php`, `Register.php`, `Logout.php`.
- `Forum/`: topic list, topic creation and replies.
- `My/`: profile, character inventory, create places and catalog listings, friends, inbox and currency history.
- `Admin/`: user moderation, reports, catalog, places and test currency tools.
- `inc/`: database bootstrap, helpers and shared layout.
- `Report.php`: community content reporting.
- `api/legacy/`: JSON endpoints for place discovery and one-time client-bridge join tickets.
- `database/schema.sql`: complete schema for new installs.
- `database/migrations/001_platform_features.sql`: additions for an existing earlier database.
- `css/`, `js/`, `images/`, `wayback/`: frontend and archived static assets.

## Implemented features
- Account registration/login with password hashing and CSRF checks.
- Profiles, friend requests, private messages and forum discussions.
- Place listings and optional HTTPS links to browser-playable games.
- Catalog purchases using Tickets, finite/unlimited stock, inventory ownership and a currency ledger.
- Simulated Robux balance for development/admin testing only; no real-money payments.
- Admin and moderator roles, reports, account suspension/ban, content moderation and catalog/place management.

## Not implemented yet
- The original Roblox 2008 client/server protocol or actual legacy game execution. The new `rblx08-bridge-v1` API validates a user and place for a separately modified client, but it is not a Roblox protocol implementation or game server.
- Real avatar rendering. This is intentionally the final development phase, after the platform systems are stable.
- Real Robux purchases, payments or cash-out.

## Setup
1. For a new database, import `database/schema.sql`.
2. For an existing database created with the earlier schema, apply `database/migrations/001_platform_features.sql`, then `database/migrations/002_legacy_bridge.sql`.
3. Copy `inc/config.example.php` to `inc/config.php` and set the MariaDB credentials.
4. Point Apache's document root to the repository root and enable `mod_rewrite` and `.htaccess`.
5. Local test: `php -S 0.0.0.0:8080 router.php`.
6. Required PHP extensions: `PDO` and `pdo_mysql`.

## Legacy client bridge API
- `GET /api/legacy/place.php?id=PLACE_ID` returns public metadata for an active place.
- An authenticated client requests `POST /api/legacy/join.php` with the session cookie, `X-CSRF-Token` header, and JSON `{\"place_id\":123}`. The response contains a random one-use token valid for five minutes.
- The bridge redeems it with `POST /api/legacy/redeem.php` and JSON `{\"join_token\":\"...\"}`. A successful response confirms the player identity and place.
- Redeem tokens immediately, keep them private, and use HTTPS. The API does not transfer passwords or implement movement, physics, replication, or the original Roblox wire protocol. A compatible game server and client adapter are still required.

## First administrator
Register an account, then grant it administrator access directly in MariaDB:

```sql
UPDATE users SET role='admin' WHERE username='YOUR_USERNAME';
```

Assign moderator access only to trusted people:

```sql
UPDATE users SET role='moderator' WHERE username='YOUR_USERNAME';
```

Never expose database credentials or grant staff access to untrusted users.

## Wayback assets
Open GitHub Actions → **Import Roblox 2008 assets** → **Run workflow**. The importer uses the 23 February 2008 Internet Archive capture and can only retrieve assets preserved by the archive. The import workflow has not been confirmed as completed yet.
