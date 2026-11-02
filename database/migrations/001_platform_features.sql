-- Apply once to an existing rblx08 database. New installs should use schema.sql.
ALTER TABLE users
 ADD COLUMN IF NOT EXISTS tickets INT UNSIGNED NOT NULL DEFAULT 1000,
 ADD COLUMN IF NOT EXISTS robux INT UNSIGNED NOT NULL DEFAULT 0,
 ADD COLUMN IF NOT EXISTS role ENUM('user','moderator','admin') NOT NULL DEFAULT 'user',
 ADD COLUMN IF NOT EXISTS account_status ENUM('active','suspended','banned') NOT NULL DEFAULT 'active';
ALTER TABLE catalog_items
 ADD COLUMN IF NOT EXISTS creator_id BIGINT UNSIGNED NULL,
 ADD COLUMN IF NOT EXISTS stock INT UNSIGNED NULL DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS sold_count BIGINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE games
 ADD COLUMN IF NOT EXISTS status ENUM('active','hidden','removed') NOT NULL DEFAULT 'active',
 ADD COLUMN IF NOT EXISTS launch_url VARCHAR(500) NULL;
CREATE TABLE IF NOT EXISTS currency_transactions(
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 currency ENUM('tickets','robux') NOT NULL,
 amount INT NOT NULL,
 reason VARCHAR(160) NOT NULL,
 reference_type VARCHAR(32) NULL,
 reference_id BIGINT UNSIGNED NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_currency_user(user_id,created_at),
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS moderation_reports(
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 reporter_id BIGINT UNSIGNED NOT NULL,
 target_type ENUM('user','game','item','forum_topic','forum_reply') NOT NULL,
 target_id BIGINT UNSIGNED NOT NULL,
 reason VARCHAR(1000) NOT NULL,
 status ENUM('pending','resolved','dismissed') NOT NULL DEFAULT 'pending',
 reviewed_by BIGINT UNSIGNED NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 reviewed_at DATETIME NULL,
 KEY idx_reports_status(status,created_at),
 FOREIGN KEY(reporter_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS moderation_actions(
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 moderator_id BIGINT UNSIGNED NOT NULL,
 target_user_id BIGINT UNSIGNED NULL,
 action VARCHAR(40) NOT NULL,
 note VARCHAR(500) NOT NULL DEFAULT '',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(moderator_id) REFERENCES users(id),
 FOREIGN KEY(target_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
