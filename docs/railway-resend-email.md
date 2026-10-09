# Resend email on Railway

## Railway variables

Set these under the production service's Variables in Railway:

```text
MAIL_MAILER=resend
RESEND_API_KEY=<new Resend API key>
MAIL_FROM_ADDRESS=<address on a verified Resend domain>
MAIL_FROM_NAME=Godong Ijo
```

Do not commit the API key or put it in an `.env` file tracked by Git. The key previously pasted into chat should be revoked and replaced before production use.

Verify the sender domain in Resend and publish its required DNS records before testing. Resend may only allow sending to a limited set of recipients until a domain is verified.

## Deploy and verify

Deploy the commit containing the Resend transport, then use Railway's service shell to send a test message:

```sh
php artisan email:test booking --email=your-address@example.com
```

Check the recipient inbox and spam folder, the Resend email logs for a delivered event, and Railway application logs for `Email sent successfully` or a send failure. A successful API response means Resend accepted the message; confirm delivery in Resend.

The test command checks sending and the booking-confirmation template. Also place a controlled production test booking and verify its customer confirmation email, then complete a test payment if payment-success email delivery needs verification. Confirm **Email Notifications** is enabled in the admin settings.

## Restore

This integration is kept in its own Git commit. To back it out while preserving shared Git history, run:

```sh
git revert <resend-integration-commit>
```

Do not commit Railway variables, API keys, or local `.env` files.
