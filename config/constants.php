<?php
    $dotenv = parse_ini_file(__DIR__ . '/../.env');
    define('DB_SERVERNAME', $dotenv['DB_SERVERNAME']);
    define('DB_USERNAME', $dotenv['DB_USERNAME']);
    define('DB_PASSWORD', $dotenv['DB_PASSWORD']);
    define('DB_NAME', $dotenv['DB_NAME']);
    define('SMTP_SERVER', $dotenv['SMTP_SERVER']);
    define('SMTP_USERNAME', $dotenv['SMTP_USERNAME']);
    define('SMTP_PASSWORD', $dotenv['SMTP_PASSWORD']);
    define('TO_MAIL_ID', $dotenv['TO_MAIL_ID']);
    define('TO_MAIL_RECIPIENT_NAME', $dotenv['TO_MAIL_RECIPIENT_NAME']);
    define('ENABLE_LOGGING', filter_var($dotenv['ENABLE_LOGGING'], FILTER_VALIDATE_BOOLEAN));
