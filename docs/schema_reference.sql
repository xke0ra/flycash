============================================================
# FLY CASH v4 — Database Schema Reference
# Generated: Phase 0 — Baseline Documentation
# This file is a CODE-DERIVED reconstruction from PHP queries.
# The original install/pocket_db.sql is incomplete (empty shell).
============================================================

CREATE DATABASE IF NOT EXISTS pocket_db DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE pocket_db;

-- -------------------------------------------------------
-- 1. Users
-- -------------------------------------------------------
CREATE TABLE users (
    id          INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login       VARCHAR(64) NOT NULL DEFAULT '',
    email       VARCHAR(128) NOT NULL DEFAULT '',
    passw       VARCHAR(255) NOT NULL DEFAULT '',
    salt        VARCHAR(16) NOT NULL DEFAULT '',
    state       TINYINT(1) NOT NULL DEFAULT 0,       -- 0=enabled, 1=disabled, 2=blocked, 3=deactivated
    fullname    VARCHAR(128) NOT NULL DEFAULT '',
    image       VARCHAR(255) NOT NULL DEFAULT '',
    regtime     INT(10) UNSIGNED NOT NULL DEFAULT 0,
    regtype     VARCHAR(32) NOT NULL DEFAULT '',
    ip_addr     VARCHAR(45) NOT NULL DEFAULT '',
    last_access INT(10) UNSIGNED NOT NULL DEFAULT 0,
    last_ip_addr VARCHAR(45) NOT NULL DEFAULT '',
    gcm_regid   TEXT DEFAULT NULL,
    mobile      VARCHAR(32) NOT NULL DEFAULT '',
    points      INT(11) NOT NULL DEFAULT 0,
    refer       VARCHAR(32) NOT NULL DEFAULT '',
    refered     TINYINT(1) NOT NULL DEFAULT 0,
    referer     VARCHAR(64) NOT NULL DEFAULT '',
    email_verified TINYINT(1) NOT NULL DEFAULT 0,
    INDEX idx_login (login),
    INDEX idx_email (email),
    INDEX idx_refer (refer),
    INDEX idx_referer (referer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 2. Admins
-- -------------------------------------------------------
CREATE TABLE admins (
    id            INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(64) NOT NULL DEFAULT '',
    password      VARCHAR(255) NOT NULL DEFAULT '',
    salt          VARCHAR(32) NOT NULL DEFAULT '',
    fullname      VARCHAR(128) NOT NULL DEFAULT '',
    role_id       INT(11) DEFAULT 0,
    twofa_secret  VARCHAR(64) DEFAULT NULL,
    twofa_enabled TINYINT(1) NOT NULL DEFAULT 0,
    createAt      INT(10) UNSIGNED NOT NULL DEFAULT 0,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 3. Admin Roles
-- -------------------------------------------------------
CREATE TABLE admin_roles (
    id          INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(64) NOT NULL DEFAULT '',
    permissions TEXT DEFAULT NULL                  -- comma-separated or '*' for all
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 4. Access Data (API tokens)
-- -------------------------------------------------------
CREATE TABLE access_data (
    id            INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accountId     INT(11) UNSIGNED NOT NULL DEFAULT 0,
    accessToken   VARCHAR(64) NOT NULL DEFAULT '',
    refreshToken  VARCHAR(64) NOT NULL DEFAULT '',
    clientId      INT(11) NOT NULL DEFAULT 0,
    createAt      INT(10) UNSIGNED NOT NULL DEFAULT 0,
    removeAt      INT(10) UNSIGNED NOT NULL DEFAULT 0,   -- 0=active, >0 = soft-deleted
    expiresAt     INT(10) UNSIGNED DEFAULT 0,
    u_agent       VARCHAR(255) DEFAULT NULL,
    ip_addr       VARCHAR(45) DEFAULT NULL,
    INDEX idx_access_token (accessToken),
    INDEX idx_refresh_token (refreshToken),
    INDEX idx_account_id (accountId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 5. Configuration (key-value settings)
-- -------------------------------------------------------
CREATE TABLE configuration (
    id            INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    config_name   VARCHAR(128) NOT NULL DEFAULT '',
    config_value  TEXT DEFAULT NULL,
    api_status    INT(11) DEFAULT 1,
    INDEX idx_config_name (config_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 6. Offerwalls
-- -------------------------------------------------------
CREATE TABLE offerwalls (
    id        INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(128) NOT NULL DEFAULT '',
    subtitle  VARCHAR(255) DEFAULT NULL,
    url       TEXT DEFAULT NULL,
    points    INT(11) NOT NULL DEFAULT 0,
    image     VARCHAR(255) DEFAULT NULL,
    type      VARCHAR(64) NOT NULL DEFAULT '',
    featured  TINYINT(1) NOT NULL DEFAULT 0,
    position  INT(11) NOT NULL DEFAULT 0,
    status    TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 7. Payouts (redeemable items)
-- -------------------------------------------------------
CREATE TABLE payouts (
    id        INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(128) NOT NULL DEFAULT '',
    subtitle  VARCHAR(255) DEFAULT NULL,
    message   TEXT DEFAULT NULL,
    amount    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    points    INT(11) NOT NULL DEFAULT 0,
    image     VARCHAR(255) DEFAULT NULL,
    status    TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 8. Redemptions (unified payout requests, replaces legacy Requests + Completed)
-- Added by: db/migrations/20260705000002_unify_requests_completed.php
-- -------------------------------------------------------
CREATE TABLE redemptions (
    id            INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT(11) UNSIGNED DEFAULT NULL,
    request_from  VARCHAR(128) NOT NULL DEFAULT '',
    dev_name      VARCHAR(128) DEFAULT NULL,
    dev_man       VARCHAR(128) DEFAULT NULL,
    gift_name     VARCHAR(128) NOT NULL DEFAULT '',
    req_amount    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    points_used   INT(11) NOT NULL DEFAULT 0,
    date          INT(10) UNSIGNED NOT NULL DEFAULT 0,
    status        ENUM('pending','processing','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
    username      VARCHAR(64) NOT NULL DEFAULT '',
    note          TEXT DEFAULT NULL,
    created_at    INT(10) UNSIGNED NOT NULL DEFAULT 0,
    updated_at    INT(10) UNSIGNED NOT NULL DEFAULT 0,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_user_status (user_id, status),
    CONSTRAINT fk_redemptions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 10. Tracker (points transaction log)
-- -------------------------------------------------------
CREATE TABLE tracker (
    id        INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username  VARCHAR(64) NOT NULL DEFAULT '',
    points    INT(11) NOT NULL DEFAULT 0,
    type      VARCHAR(128) NOT NULL DEFAULT '',
    date      INT(10) UNSIGNED NOT NULL DEFAULT 0,
    INDEX idx_username (username),
    INDEX idx_type (type),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 11. Referers (referral tracking)
-- -------------------------------------------------------
CREATE TABLE referers (
    id        INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username  VARCHAR(64) NOT NULL DEFAULT '',
    referer   VARCHAR(64) NOT NULL DEFAULT '',
    points    INT(11) NOT NULL DEFAULT 0,
    type      VARCHAR(128) DEFAULT NULL,
    date      INT(10) UNSIGNED DEFAULT 0,
    INDEX idx_username (username),
    INDEX idx_referer (referer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 12. Restore Data (password reset tokens)
-- -------------------------------------------------------
CREATE TABLE restore_data (
    id          INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accountId   INT(11) UNSIGNED NOT NULL DEFAULT 0,
    hash        VARCHAR(128) NOT NULL DEFAULT '',
    email       VARCHAR(128) NOT NULL DEFAULT '',
    removeAt    INT(10) UNSIGNED NOT NULL DEFAULT 0,   -- 0=active, >0=used/expired
    INDEX idx_hash (hash),
    INDEX idx_account (accountId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 13. Analytics (daily stats)
-- -------------------------------------------------------
CREATE TABLE analytics (
    id        INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date      VARCHAR(16) NOT NULL DEFAULT '',          -- YYYY-MM-DD format
    sessions  INT(11) NOT NULL DEFAULT 0,
    requests  INT(11) NOT NULL DEFAULT 0,
    completed INT(11) NOT NULL DEFAULT 0,
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 14. Whitelists (IP whitelisting for postbacks)
-- -------------------------------------------------------
CREATE TABLE whitelists (
    id      INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(128) NOT NULL DEFAULT '',
    ip_addr VARCHAR(45) NOT NULL DEFAULT '',
    INDEX idx_ip_addr (ip_addr)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 15. Rate Limits
-- -------------------------------------------------------
CREATE TABLE rate_limits (
    id            INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identifier    VARCHAR(128) NOT NULL DEFAULT '',
    action        VARCHAR(64) NOT NULL DEFAULT '',
    attempts      INT(11) NOT NULL DEFAULT 0,
    window_start  INT(10) UNSIGNED NOT NULL DEFAULT 0,
    INDEX idx_identifier (identifier, action, window_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 16. Audit Log (admin actions)
-- -------------------------------------------------------
CREATE TABLE audit_log (
    id          INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT(11) UNSIGNED DEFAULT NULL,
    admin_name  VARCHAR(64) NOT NULL DEFAULT '',
    action      VARCHAR(128) NOT NULL DEFAULT '',
    target      VARCHAR(255) DEFAULT NULL,
    details     TEXT DEFAULT NULL,
    ip_addr     VARCHAR(45) NOT NULL DEFAULT '',
    created_at  INT(10) UNSIGNED NOT NULL DEFAULT 0,
    INDEX idx_admin (admin_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 17. Notifications (in-app)
-- -------------------------------------------------------
CREATE TABLE notifications (
    id          INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(64) NOT NULL DEFAULT '',
    title       VARCHAR(255) NOT NULL DEFAULT '',
    message     TEXT DEFAULT NULL,
    points      INT(11) NOT NULL DEFAULT 0,
    type        VARCHAR(64) DEFAULT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  INT(10) UNSIGNED NOT NULL DEFAULT 0,
    INDEX idx_username (username),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 18. Banned IPs
-- -------------------------------------------------------
CREATE TABLE banned_ips (
    id          INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_addr     VARCHAR(45) NOT NULL DEFAULT '',
    reason      VARCHAR(255) DEFAULT NULL,
    banned_at   INT(10) UNSIGNED NOT NULL DEFAULT 0,
    expires_at  INT(10) UNSIGNED NOT NULL DEFAULT 0,
    INDEX idx_ip (ip_addr),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 19. Email Verify (email verification tokens)
-- -------------------------------------------------------
CREATE TABLE email_verify (
    id          INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accountId   INT(11) UNSIGNED NOT NULL DEFAULT 0,
    email       VARCHAR(128) NOT NULL DEFAULT '',
    hash        VARCHAR(128) NOT NULL DEFAULT '',
    createAt    INT(10) UNSIGNED NOT NULL DEFAULT 0,
    expiresAt   INT(10) UNSIGNED NOT NULL DEFAULT 0,
    used        TINYINT(1) NOT NULL DEFAULT 0,
    INDEX idx_hash (hash),
    INDEX idx_account (accountId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 20. Offer Status (offer completion tracking)
-- -------------------------------------------------------
CREATE TABLE offer_status (
    id          INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cid         VARCHAR(128) NOT NULL DEFAULT '',
    user        VARCHAR(64) NOT NULL DEFAULT '',
    of_id       VARCHAR(128) DEFAULT NULL,
    of_title    VARCHAR(255) DEFAULT NULL,
    of_amount   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    of_url      TEXT DEFAULT NULL,
    partner     VARCHAR(128) DEFAULT NULL,
    date        INT(10) UNSIGNED DEFAULT 0,
    status      TINYINT(1) NOT NULL DEFAULT 0,
    INDEX idx_cid (cid),
    INDEX idx_user (user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 21. OgAds Postback (legacy log)
-- -------------------------------------------------------
CREATE TABLE ogadspostback (
    id            INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    offer_id      VARCHAR(128) DEFAULT NULL,
    offer_name    VARCHAR(255) DEFAULT NULL,
    affiliate_id  VARCHAR(64) DEFAULT NULL,
    source        VARCHAR(64) DEFAULT NULL,
    aff_sub3      VARCHAR(128) DEFAULT NULL,
    session_ip    VARCHAR(45) DEFAULT NULL,
    ip            VARCHAR(45) DEFAULT NULL,
    time          VARCHAR(32) DEFAULT NULL,
    ran           VARCHAR(16) DEFAULT NULL,
    payout        VARCHAR(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 22. YouTube (Watch & Earn addon)
-- -------------------------------------------------------
CREATE TABLE youtube (
    id        INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    url       TEXT DEFAULT NULL,
    points    BIGINT(20) DEFAULT NULL,
    duration  VARCHAR(255) DEFAULT NULL,
    date      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    image     TEXT DEFAULT NULL,
    status    TINYINT(4) DEFAULT 1,
    title     TEXT DEFAULT NULL,
    subtitle  TEXT DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- 23. Watched Video (Watch & Earn addon)
-- -------------------------------------------------------
-- Table: postback_log
-- Purpose: Idempotency and audit log for all offerwall postbacks
-- Added: Phase 6 — API & Postback Hardening
-- -------------------------------------------------------
CREATE TABLE postback_log (
    id              INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider        VARCHAR(64) NOT NULL DEFAULT '',
    transaction_id  VARCHAR(255) NOT NULL DEFAULT '',
    user_id         VARCHAR(128) NOT NULL DEFAULT '',
    amount          INT(11) NOT NULL DEFAULT 0,
    status          ENUM('pending','success','failed','skipped') NOT NULL DEFAULT 'success',
    ip_addr         VARCHAR(45) NOT NULL DEFAULT '',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tx (transaction_id(64)),
    UNIQUE INDEX uq_provider_tx (provider, transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
CREATE TABLE watched_video (
    id        INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    video_id  INT(11) NOT NULL,
    user_id   INT(11) NOT NULL,
    date      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_video_user (video_id, user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
