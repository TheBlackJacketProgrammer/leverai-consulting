<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Email Configuration
|--------------------------------------------------------------------------
|
| This file contains configuration for the CodeIgniter Email library.
| Configure these settings based on your email provider.
|
| For production deployment, use SMTP with proper credentials.
| Common providers:
| - Gmail: smtp.gmail.com, port 587 (TLS) or 465 (SSL)
| - SendGrid: smtp.sendgrid.net, port 587
| - Mailgun: smtp.mailgun.org, port 587
| - AWS SES: email-smtp.[region].amazonaws.com, port 587
| - Office 365: smtp.office365.com, port 587
|
*/

// Email Protocol: 'mail', 'sendmail', or 'smtp'
$config['protocol'] = 'smtp';

// SMTP Server Settings
$config['smtp_host'] = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
$config['smtp_user'] = getenv('SMTP_USER') ?: 'neomaster667@gmail.com';
// Note: Gmail App Passwords are displayed with spaces (e.g., "wzte hmhu fqps ylbc")
// but must be used WITHOUT spaces in the config (e.g., "wztehmhufqpsylbc")
$config['smtp_pass'] = getenv('SMTP_PASS') ?: 'wztehmhufqpsylbc';
$config['smtp_port'] = getenv('SMTP_PORT') ?: 587;
$config['smtp_timeout'] = 30;
$config['smtp_keepalive'] = FALSE;

// SMTP Encryption: 'tls' or 'ssl' (usually 'tls' for port 587, 'ssl' for port 465)
$config['smtp_crypto'] = getenv('SMTP_CRYPTO') ?: 'tls';

// Email Settings
$config['mailtype'] = 'html'; // 'text' or 'html'
$config['charset'] = 'utf-8';
$config['wordwrap'] = TRUE;
$config['wrapchars'] = 76;
$config['validate'] = TRUE;

// Default From Email (can be overridden when sending)
$config['from_email'] = getenv('FROM_EMAIL') ?: 'neomaster667@gmail.com';
$config['from_name'] = getenv('FROM_NAME') ?: 'LeverAI';

// Reply-To Email
$config['reply_to_email'] = getenv('REPLY_TO_EMAIL') ?: $config['from_email'];
$config['reply_to_name'] = getenv('REPLY_TO_NAME') ?: $config['from_name'];

// BCC Batch Mode (for sending to many recipients)
$config['bcc_batch_mode'] = FALSE;
$config['bcc_batch_size'] = 200;

// Newline character
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";

/*
|--------------------------------------------------------------------------
| Environment-Specific Configuration
|--------------------------------------------------------------------------
|
| You can override these settings based on environment by checking
| $_SERVER['HTTP_HOST'] or using environment variables.
|
*/

// Detect environment
// $is_production = !in_array($_SERVER['HTTP_HOST'] ?? 'localhost', ['localhost', '127.0.0.1', '::1']);

// if (!$is_production) {
//     // Development/Testing settings
//     // You might want to use a test SMTP server or mailtrap.io for testing
//     // $config['smtp_host'] = 'smtp.mailtrap.io';
//     // $config['smtp_user'] = 'your_mailtrap_user';
//     // $config['smtp_pass'] = 'your_mailtrap_pass';
//     // $config['smtp_port'] = 2525;
// }

