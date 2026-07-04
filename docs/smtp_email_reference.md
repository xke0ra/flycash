# SMTP & Email Configuration Reference
## FLY CASH v4 — Phase 0 Baseline Documentation

## SMTP Configuration
Stored in `configuration` table — loaded by `class.emails.inc.php`

| Config Key | Default | Description |
|-----------|---------|-------------|
| SMTP_EMAIL | (empty) | Sender email address |
| SMTP_HOST | smtp.gmail.com | SMTP server hostname |
| SMTP_SECURE | TLS | Encryption (TLS/SSL) — maps to `PHPMailer::ENCRYPTION_STARTTLS` |
| SMTP_PORT | 587 | SMTP server port |
| SMTP_AUTH | 1 | Enable SMTP authentication (boolean) |
| SMTP_USERNAME | (empty) | SMTP login username |
| SMTP_PASSWORD | (empty) | SMTP login password |

## Email Types

### 1. Email Verification
- **File**: `class.emails.inc.php:22-77` (`sendVerificationEmail()`)
- **Subject**: `{APP_NAME} | Email Verification`
- **Recipients**: Newly registered user
- **Template**: Inline HTML with verify link
- **Link format**: `{WEB_ROOT}admin/api/v4/auth/verify?hash={hash}`
- **Expiry**: 24 hours (stored in `email_verify.expiresAt`)

### 2. Password Reset
- **File**: `class.emails.inc.php:79-176` (`sendPasswordResetEmail()`)
- **Subject**: `{app_name} | Password reset` (with placeholder replacement)
- **Recipients**: User requesting password reset
- **Template**: Inline HTML with reset link
- **Link format**: `{WEB_ROOT}admin/restore/?hash={hash}`
- **Placeholders**: `{user_name}`, `{user_email}`, `{app_name}`, `{reset_link}`

## Email Sending Details
- **Library**: PHPMailer (bundled: `class.PHPMailer.inc.php`)
- **Character set**: UTF-8 (default)
- **Format**: HTML with plaintext AltBody
- **Reply-To**: Set to SMTP_EMAIL
- **From Name**: APP_NAME config value

## Flow Diagrams

### Email Verification Flow
```
User Signup → account::signup()
  → account::sendVerificationEmail()
    → email stored in `email_verify` table
    → emails::sendVerificationEmail()
      → PHPMailer via SMTP
      → User clicks {WEB_ROOT}admin/api/v4/auth/verify?hash=xxx
        → account::verifyEmail()
          → email_verify.used = 1
          → users.email_verified = 1
```

### Password Reset Flow
```
User clicks "Forgot Password"
  → Input email → functions::sendPasswordResetEmail()
    → restorePointCreate() → stored in `restore_data`
    → emails::sendPasswordResetEmail()
      → PHPMailer via SMTP
      → User clicks {WEB_ROOT}admin/restore/?hash=xxx
        → controller-restore-password.php
        → account::newPassword() → bcrypt update
        → restore_data.removeAt = timestamp (soft delete)
```

## Current Status (Pre-Modernization)
- [ ] SMTP credentials are configured
- [ ] Email verification is enabled
- [ ] Password reset is functional
- [ ] PHPMailer bundled (vintage version)
- [ ] Templates are inline HTML (not customizable without code edit)
- [ ] No email queue system (synchronous send)
