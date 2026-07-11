-- ============================================================
-- Phase 3.1: Database Integrity — Foreign Keys + CHECK Constraints
-- Target: FLY CASH (POCKET Rewards)
-- Run this AFTER applying Phase 2 migrations (InnoDB, types, indexes)
-- ============================================================

-- -------------------------------------------------------
-- 1. Clean orphaned rows before adding FKs
-- -------------------------------------------------------

-- Delete tracker entries with no matching user
DELETE FROM tracker WHERE username NOT IN (SELECT login FROM users);
-- Delete orphaned redemptions with no matching user
DELETE FROM redemptions WHERE user_id NOT IN (SELECT id FROM users);
-- Delete offer_status with no matching user
DELETE FROM offer_status WHERE `user` NOT IN (SELECT login FROM users);
-- Delete referers with no matching user
DELETE FROM referers WHERE username NOT IN (SELECT login FROM users);
-- Delete access_data with no matching user
DELETE FROM access_data WHERE accountId NOT IN (SELECT id FROM users);
-- Delete restore_data with no matching user
DELETE FROM restore_data WHERE accountId NOT IN (SELECT id FROM users);
-- Delete audit_log with no matching admin
DELETE FROM audit_log WHERE admin_id NOT IN (SELECT id FROM admins);

-- -------------------------------------------------------
-- 2. ALTER tables to add proper column types (remaining VARCHAR→INT)
-- -------------------------------------------------------

-- offerwalls: convert numeric columns
ALTER TABLE offerwalls MODIFY COLUMN points INT(11) NOT NULL DEFAULT 0;
ALTER TABLE offerwalls MODIFY COLUMN featured TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE offerwalls MODIFY COLUMN `position` INT(11) NOT NULL DEFAULT 0;
ALTER TABLE offerwalls MODIFY COLUMN `status` TINYINT(1) NOT NULL DEFAULT 1;

-- payouts: convert numeric columns
ALTER TABLE payouts MODIFY COLUMN points INT(11) NOT NULL DEFAULT 0;
ALTER TABLE payouts MODIFY COLUMN `status` TINYINT(1) NOT NULL DEFAULT 1;

-- offer_status: convert numeric columns
ALTER TABLE offer_status MODIFY COLUMN of_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00;
ALTER TABLE offer_status MODIFY COLUMN `status` TINYINT(1) NOT NULL DEFAULT 0;

-- referers: convert numeric columns
ALTER TABLE referers MODIFY COLUMN points INT(11) NOT NULL DEFAULT 0;

-- -------------------------------------------------------
-- 3. Add FOREIGN KEY constraints
-- -------------------------------------------------------

-- access_data -> users
ALTER TABLE access_data ADD CONSTRAINT fk_access_user
    FOREIGN KEY (accountId) REFERENCES users(id) ON DELETE CASCADE;

-- redemptions -> users
ALTER TABLE redemptions ADD CONSTRAINT fk_redemptions_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- tracker -> users
ALTER TABLE tracker ADD CONSTRAINT fk_tracker_user
    FOREIGN KEY (username) REFERENCES users(login) ON DELETE CASCADE;

-- offer_status -> users
ALTER TABLE offer_status ADD CONSTRAINT fk_offer_status_user
    FOREIGN KEY (`user`) REFERENCES users(login) ON DELETE CASCADE;

-- referers -> users
ALTER TABLE referers ADD CONSTRAINT fk_referers_user
    FOREIGN KEY (username) REFERENCES users(login) ON DELETE CASCADE;

-- restore_data -> users
ALTER TABLE restore_data ADD CONSTRAINT fk_restore_user
    FOREIGN KEY (accountId) REFERENCES users(id) ON DELETE CASCADE;

-- audit_log -> admins (match admins.id type: int(11) unsigned, must allow NULL for SET NULL)
ALTER TABLE audit_log MODIFY COLUMN admin_id INT(11) UNSIGNED DEFAULT NULL;
ALTER TABLE audit_log ADD CONSTRAINT fk_audit_admin
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL;

-- -------------------------------------------------------
-- 4. Add CHECK constraints (enforced in MySQL 8.0.16+ / MariaDB 10.2+)
-- -------------------------------------------------------

-- users: state must be 0 (active), 1 (disabled), 2 (blocked), 3 (deactivated)
ALTER TABLE users ADD CONSTRAINT chk_user_state
    CHECK (state IN (0, 1, 2, 3));

-- users: points cannot be negative
ALTER TABLE users ADD CONSTRAINT chk_user_points
    CHECK (points >= 0);

-- redemptions: status must be a valid ENUM value
ALTER TABLE redemptions ADD CONSTRAINT chk_redemption_status
    CHECK (status IN ('pending', 'processing', 'completed', 'rejected', 'cancelled'));

-- tracker: points should not be zero for valid transactions (note: negative allowed for debits)
ALTER TABLE tracker ADD CONSTRAINT chk_tracker_points
    CHECK (points != 0);

-- -------------------------------------------------------
-- 5. Add remaining useful indexes
-- -------------------------------------------------------

-- whitelists: speed up IP lookups
ALTER TABLE whitelists ADD INDEX idx_whitelist_ip (ip_addr);

-- offer_status: speed up cid lookups (used in postback checks)
ALTER TABLE offer_status ADD INDEX idx_offer_cid (cid);

-- referers: speed up referer lookups
ALTER TABLE referers ADD INDEX idx_referers_referer (referer);

-- notifications (already exists, ensure proper indices)
ALTER TABLE notifications ADD INDEX idx_notify_created (created_at);

-- -------------------------------------------------------
-- 6. Add AUTO_INCREMENT where still missing
-- -------------------------------------------------------

ALTER TABLE offer_status MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT;

-- -------------------------------------------------------
-- 7. Update install schema reference
-- -------------------------------------------------------
-- Note: The main install SQL (pocket_db.sql) has been updated separately.
-- For new installations, use that file directly.
