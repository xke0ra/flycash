# Admin Guide

## Adding an Offerwall

1. Register as a publisher with an offerwall network (AdGem, OGAds, AdGateMedia, etc.).
2. Log into their dashboard and get your offerwall iframe URL.
3. The URL should contain a user identifier parameter — replace it with the literal text `{user_id}` (the system substitutes the logged-in user's ID automatically).
4. In FlyCash admin: **Offerwalls → Add Offerwall**.
5. Enter:
   - **Name**: Display name (e.g., "AdGem Offers")
   - **Subtitle**: Optional short description
   - **URL**: Paste the iframe URL with `{user_id}` placeholder
   - **Position**: Ordering number (1 = first tab)
6. Save. The offerwall will appear immediately in the user dashboard.

## Offerwall Types

The system no longer distinguishes "built-in" vs "custom" offerwall types. All offerwalls use the same custom URL mechanism — any network that provides an iframe URL works.

Internal system types (`checkin`, `spin`, `refer`, `redeem`, `instructions`, `transactions`, `share`, `rate`, `about`) are automatically excluded from the offerwalls tab.

## Reviewing Redemption Requests

1. **Admin → Requests**: Shows all pending requests.
2. Click **Details** to view user info and request.
3. **Approve**: Marks as `processing` → `completed`. Points are deducted (already deducted at request time).
4. **Reject**: Marks as `rejected` and **refunds** the points to the user.
5. **Processing**: Intermediate status for manual review.

## Configuration Settings

Accessible via **Admin → Settings**. Key groups:

| Section | Description |
|---------|-------------|
| General | App name, description, contact URLs |
| Referral | Bonus amounts for referrer and referred user |
| Check-in | Daily check-in reward amount |
| Spin | Lucky spin reward configuration |
| Offerwalls | Postback secrets for AdGateMedia, AdScendMedia, AdMantum, OgAds |
| Firebase | FCM API key for push notifications |
| SMTP | Email server for verification/password reset |
| Security | Rate limiting, 2FA, ban settings |

See `docs/configuration_reference.json` for the complete list of all config keys with defaults and descriptions.

## User Management

1. **Admin → Users**: Search, view, and edit user accounts.
2. **State**: `Enabled` (normal), `Disabled` (cannot login), `Blocked` (suspicious), `Deactivated`.
3. **Balance Adjustment**: Process → Add Points (logs the action).

## IP Whitelist

Postback IPs that bypass HMAC signature verification (for networks that don't support signatures):

**Admin → Whitelist IPs**.

To find the IPs used by your offerwall networks, check their documentation or ask their support.
