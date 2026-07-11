-- ============================================================
-- Phase 4.4: Refresh Tokens — access_data table upgrade
-- Target: FLY CASH (POCKET Rewards)
-- ============================================================

-- Increase accessToken to 64 chars (supports bin2hex(random_bytes(32)))
ALTER TABLE access_data MODIFY COLUMN accessToken VARCHAR(64) NOT NULL DEFAULT '';

-- Add refreshToken column (64 chars)
ALTER TABLE access_data ADD COLUMN refreshToken VARCHAR(64) NOT NULL DEFAULT '' AFTER accessToken;

-- Add expiresAt column (Unix timestamp)
ALTER TABLE access_data ADD COLUMN expiresAt INT(10) UNSIGNED DEFAULT 0 AFTER removeAt;

-- Add indexes for fast token lookups
ALTER TABLE access_data ADD INDEX idx_access_token (accessToken);
ALTER TABLE access_data ADD INDEX idx_refresh_token (refreshToken);
