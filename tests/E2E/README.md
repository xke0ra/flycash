# End-to-End Test Scenarios

These scenarios are intended for **manual or semi-automated** verification before releases. Each covers a complete user journey that unit/integration tests cannot fully simulate.

## Scenario 1: Full User Journey (Earn → Redeem)

**Prerequisites:** A running local/dev instance with a valid database, and an offerwall provider URL.

### Steps

1. **Registration** — Open `dashboard/register.php`. Create a user with username `e2e_test_user`. Verify redirect to login page.

2. **Login** — Open `dashboard/login.php`. Sign in with `e2e_test_user`. Verify redirect to dashboard index.

3. **View Offerwalls** — Navigate to the offerwalls tab. At least one offerwall must be configured (see Phase 0.1). Verify the iframe loads.

4. **Points Initial State** — Check `e2e_test_user` has `points = 0` in the database.

5. **Simulate Postback** — Send a GET request to the appropriate postback URL:
   ```
   http://localhost/postbacks/adgem.php?user_id=e2e_test_user&amount=100&transaction_id=e2e_test_001
   ```
   (This requires the offerwall's postback secret to be configured in `configuration` table, or IP whitelisted.)

6. **Verify Credit** — Check `e2e_test_user.points = 100`, `tracker` has a row with `username='e2e_test_user'`, `points=100`, `type='AdGem offerwall Credit'`.

7. **Redeem** — Open `dashboard/redeem.php`. Select a payout item (must exist in `payouts` table). Submit. Verify redirect shows success.

8. **Verify Deduction** — Check `e2e_test_user.points` decreased by the payout amount. `redemptions` table has a row with `username='e2e_test_user'`, `status='pending'`.

### Cleanup

```sql
DELETE FROM users WHERE login = 'e2e_test_user';
DELETE FROM tracker WHERE username = 'e2e_test_user';
DELETE FROM redemptions WHERE username = 'e2e_test_user';
DELETE FROM postback_log WHERE user_id = 'e2e_test_user';
```

## Scenario 2: Duplicate Postback Rejection

**Steps 1-5** same as Scenario 1. Then:

6. Send the exact same postback request again. Verify `respondOk()` is called (body: `OK`), and `points` are NOT increased (still 100, not 200).

7. Verify `postback_log` has exactly one row with `status = 'success'` for `transaction_id = 'e2e_test_001'`.

## Scenario 3: Rate Limiting on API Signin

1. Send 6 rapid POST requests to `admin/api/v4/endpoints/auth/signin.php` with wrong password.
2. Verify the 6th request returns an HTTP 429 or error code indicating rate-limited.
3. Wait 60 seconds (or reset `rate_limits` table). Verify a fresh request succeeds.
