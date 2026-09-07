<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "log", "array", "failover", "roundrobin"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => env('MAIL_SMTP_TIMEOUT', 10),
            'read_timeout' => env('MAIL_SMTP_READ_TIMEOUT', 15),
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Domain Configuration
    |--------------------------------------------------------------------------
    |
    | The domain used in Message-ID headers for booking emails.
    | Falls back to the domain parsed from APP_URL if not specified.
    |
    */

    'domain' => env('MAIL_DOMAIN', 'thewaterfall.com'),

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control email sending rate limits to prevent exceeding
    | Gmail SMTP quotas. The daily limit should be set according to your
    | Gmail account tier (100 for free, higher for Google Workspace).
    |
    */

    'daily_limit' => env('MAIL_DAILY_LIMIT', 100),
    'rate_limit_warning_threshold' => env('MAIL_RATE_WARNING_THRESHOLD', 0.8),

    /*
    |--------------------------------------------------------------------------
    | SMTP Connection Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control SMTP connection timeouts and retry behavior
    | to ensure reliable email delivery while preventing long delays.
    |
    */

    'smtp_timeout' => env('MAIL_SMTP_TIMEOUT', 10),
    'smtp_read_timeout' => env('MAIL_SMTP_READ_TIMEOUT', 15),
    'smtp_retry_attempts' => env('MAIL_SMTP_RETRY_ATTEMPTS', 2),
    'smtp_retry_delay' => env('MAIL_SMTP_RETRY_DELAY', 5),

    /*
    |--------------------------------------------------------------------------
    | Email Validation Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control email template validation and content checks
    | to improve deliverability and reduce spam scoring.
    |
    */

    'validation' => [
        'enabled' => env('MAIL_VALIDATION_ENABLED', true),
        'check_spam_triggers' => env('MAIL_CHECK_SPAM_TRIGGERS', true),
        'minimum_text_ratio' => env('MAIL_MIN_TEXT_RATIO', 0.6),
    ],

    /*
    |--------------------------------------------------------------------------
    | Spam Trigger Words
    |--------------------------------------------------------------------------
    |
    | List of words that commonly trigger spam filters. Email subject lines
    | and content should avoid these words to improve deliverability.
    |
    */

    'spam_trigger_words' => [
        'free', 'winner', 'urgent', 'act now', 'guarantee', 'limited time',
        'exclusive', 'congratulations', 'claim now', 'click here',
        'buy now', 'order now', 'instant', 'incredible', 'amazing deal',
        'risk free', 'no risk', 'cash bonus', 'earn money', 'extra income',
        'double your', 'meet singles', 'lose weight', 'mlm', 'work from home',
        'be your own boss', 'home based business', 'online pharmacy',
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Header Configuration
    |--------------------------------------------------------------------------
    |
    | Default headers applied to all transactional emails to improve
    | deliverability and signal legitimate transactional messaging.
    |
    */

    'headers' => [
        'precedence' => 'bulk',
        'auto_submitted' => 'auto-generated',
        'x_mailer' => 'Laravel/' . app()->version(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Priority Configuration
    |--------------------------------------------------------------------------
    |
    | Priority levels for different email types (1=high, 3=normal, 5=low)
    |
    */

    'priorities' => [
        'payment_success' => 1,      // High priority - payment confirmation
        'booking_confirmation' => 3, // Normal priority - booking creation
        'cancellation' => 3,         // Normal priority - cancellation notice
    ],

];
