# Midtrans Payment Gateway Setup

## Overview
This document provides instructions for configuring Midtrans payment gateway integration for The Waterfall booking system.

## Installation Status
✅ Midtrans PHP SDK (v2.6.2) installed via Composer
✅ Configuration file created at `config/midtrans.php`
✅ Environment variables added to `.env` file

## Getting Your Midtrans Credentials

### Step 1: Create Midtrans Account
1. Go to [https://dashboard.midtrans.com/register](https://dashboard.midtrans.com/register)
2. Sign up for a free account
3. Complete the verification process

### Step 2: Get Sandbox Credentials
1. Login to [Midtrans Dashboard](https://dashboard.midtrans.com/)
2. Go to **Settings** → **Access Keys**
3. Switch to **Sandbox** mode (toggle at the top)
4. Copy your credentials:
   - **Server Key** (starts with `SB-Mid-server-...`)
   - **Client Key** (starts with `SB-Mid-client-...`)

### Step 3: Update .env File
Replace the placeholder values in your `.env` file:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-YOUR_ACTUAL_SERVER_KEY
MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_ACTUAL_CLIENT_KEY
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

**Important:** Keep `MIDTRANS_IS_PRODUCTION=false` for testing/development!

## Configuration Options

### config/midtrans.php

| Setting | Environment Variable | Default | Description |
|---------|---------------------|---------|-------------|
| `server_key` | `MIDTRANS_SERVER_KEY` | `''` | Your Midtrans server key for API authentication |
| `client_key` | `MIDTRANS_CLIENT_KEY` | `''` | Your Midtrans client key for Snap.js integration |
| `is_production` | `MIDTRANS_IS_PRODUCTION` | `false` | Set to `true` for production, `false` for sandbox |
| `is_sanitized` | `MIDTRANS_IS_SANITIZED` | `true` | Enable automatic input sanitization |
| `is_3ds` | `MIDTRANS_IS_3DS` | `true` | Enable 3D Secure for credit card transactions |

## Testing in Sandbox Mode

Midtrans sandbox provides test payment methods. Use these test credentials:

### Test Credit Cards
- **Card Number:** 4811 1111 1111 1114
- **CVV:** 123
- **Expiry:** Any future date
- **3DS Password:** 112233

### Test Virtual Accounts
Each bank has specific test account numbers. Check [Midtrans Testing Payment](https://docs.midtrans.com/en/technical-reference/sandbox-test) for details.

## Verifying Installation

Run this command to verify the configuration is loaded correctly:

```bash
php artisan tinker --execute="echo config('midtrans.server_key');"
```

You should see your server key printed.

## Next Steps

1. ✅ **Task 1 Complete:** SDK and configuration setup
2. ⏳ **Task 2:** Update database schema for pemesanan table
3. ⏳ **Task 3:** Update database schema for pembayaran table
4. ⏳ **Task 4:** Create MidtransService class
5. ⏳ **Task 5:** Create BookingController
6. ⏳ **Task 6:** Create WebhookController
7. ⏳ **Task 7:** Create booking modal and form frontend
8. ⏳ **Task 8:** Integrate Snap.js frontend

## Troubleshooting

### Configuration not loading
```bash
php artisan config:clear
php artisan config:cache
```

### Class not found errors
```bash
composer dump-autoload
```

### Testing API connection
You can test the Midtrans API connection in the next tasks when we create the MidtransService class.

## Resources

- [Midtrans Documentation](https://docs.midtrans.com/)
- [Midtrans PHP SDK](https://github.com/Midtrans/midtrans-php)
- [Snap API Reference](https://snap-docs.midtrans.com/)
- [Testing Payment](https://docs.midtrans.com/en/technical-reference/sandbox-test)

## Security Notes

⚠️ **Never commit actual credentials to version control!**
- Add `.env` to `.gitignore` (should already be there)
- Use different keys for production and sandbox
- Rotate keys periodically for security
- Keep server key confidential (backend only)
- Client key can be exposed in frontend (it's designed for that)

---

**Setup completed on:** $(Get-Date)
**Midtrans SDK Version:** 2.6.2
**Laravel Version:** 10.x
