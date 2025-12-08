# Email Configuration Guide

This project includes a production-ready email system using CodeIgniter's Email library with SMTP support.

## Quick Start

### 1. Configure Email Settings

Edit `application/config/email.php` and set your SMTP credentials:

```php
$config['smtp_host'] = 'smtp.gmail.com';  // Your SMTP server
$config['smtp_user'] = 'your-email@gmail.com';  // Your SMTP username
$config['smtp_pass'] = 'your-app-password';  // Your SMTP password
$config['smtp_port'] = 587;  // Usually 587 for TLS, 465 for SSL
$config['smtp_crypto'] = 'tls';  // 'tls' or 'ssl'
$config['from_email'] = 'noreply@yourdomain.com';
$config['from_name'] = 'Your Application Name';
```

### 2. Using Environment Variables (Recommended for Production)

For better security, use environment variables instead of hardcoding credentials:

```php
$config['smtp_host'] = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
$config['smtp_user'] = getenv('SMTP_USER') ?: '';
$config['smtp_pass'] = getenv('SMTP_PASS') ?: '';
```

Then set these in your server environment or `.env` file (if using a package like `vlucas/phpdotenv`).

### 3. Common Email Provider Settings

#### Gmail
- **SMTP Host:** `smtp.gmail.com`
- **Port:** `587` (TLS) or `465` (SSL)
- **Crypto:** `tls` or `ssl`
- **Note:** You'll need to use an [App Password](https://support.google.com/accounts/answer/185833) instead of your regular password

#### SendGrid
- **SMTP Host:** `smtp.sendgrid.net`
- **Port:** `587`
- **Crypto:** `tls`
- **Username:** `apikey`
- **Password:** Your SendGrid API key

#### Mailgun
- **SMTP Host:** `smtp.mailgun.org`
- **Port:** `587`
- **Crypto:** `tls`
- **Username:** Your Mailgun SMTP username
- **Password:** Your Mailgun SMTP password

#### AWS SES
- **SMTP Host:** `email-smtp.[region].amazonaws.com` (e.g., `email-smtp.us-east-1.amazonaws.com`)
- **Port:** `587`
- **Crypto:** `tls`
- **Username:** Your AWS SES SMTP username
- **Password:** Your AWS SES SMTP password

#### Office 365
- **SMTP Host:** `smtp.office365.com`
- **Port:** `587`
- **Crypto:** `tls`

## Usage Examples

### Basic Email Sending

```php
// In your controller
$this->load->library('emailer');

// Simple email
$result = $this->emailer->send(
    'recipient@example.com',
    'Subject Here',
    '<h1>Hello!</h1><p>This is the email body.</p>'
);

// With options
$result = $this->emailer->send(
    'recipient@example.com',
    'Subject Here',
    '<h1>Hello!</h1><p>This is the email body.</p>',
    null, // Use default from
    [
        'cc' => 'cc@example.com',
        'bcc' => 'bcc@example.com',
        'reply_to' => ['email' => 'reply@example.com', 'name' => 'Reply Name'],
        'attachments' => ['/path/to/file.pdf']
    ]
);
```

### Using Email Templates

```php
// Send welcome email
$this->emailer->send_welcome('user@example.com', 'John Doe');

// Send password reset email
$reset_token = 'your-reset-token-here';
$this->emailer->send_password_reset('user@example.com', 'John Doe', $reset_token);

// Send notification
$this->emailer->send_notification('user@example.com', 'Important Update', 'Your account has been updated.');

// Send contact form email
$this->emailer->send_contact('sender@example.com', 'John Doe', 'Message content here');

// Custom template
$this->emailer->send_template(
    'user@example.com',
    'Custom Subject',
    'custom_template', // views/emails/custom_template.php
    ['data' => 'value'] // Data to pass to template
);
```

### Testing Email Configuration

Visit: `http://your-domain.com/test-email?to=your-email@example.com`

Or use the API endpoint:
```php
// GET request
/test-email?to=your-email@example.com
```

## Available Email Templates

The following email templates are included in `application/views/emails/`:

1. **welcome.php** - Welcome email for new users
2. **password_reset.php** - Password reset email with reset link
3. **notification.php** - General notification email
4. **contact.php** - Contact form submission email

### Creating Custom Templates

Create a new template in `application/views/emails/your_template.php`:

```php
<!DOCTYPE html>
<html>
<head>
    <title>Your Template</title>
</head>
<body>
    <h1>Hello <?php echo htmlspecialchars($name); ?>!</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
</body>
</html>
```

Then use it:
```php
$this->emailer->send_template(
    'user@example.com',
    'Subject',
    'your_template',
    ['name' => 'John', 'message' => 'Hello!']
);
```

## API Endpoints

The following endpoints are available in `Ctrl_Main`:

1. **GET /test-email?to=email@example.com** - Test email configuration
2. **GET /send-welcome-email?email=user@example.com&name=John** - Send welcome email
3. **POST /contact** - Handle contact form submissions
   - Parameters: `email`, `name`, `message`
4. **POST /send-custom-email** - Send custom notification
   - Parameters: `to`, `subject`, `message`

## Error Handling

The Emailer library logs errors automatically. Check `application/logs/` for error messages.

To get the last error:
```php
$error = $this->emailer->get_error();
```

## Production Deployment Checklist

- [ ] Update SMTP credentials in `application/config/email.php`
- [ ] Use environment variables for sensitive credentials
- [ ] Test email sending with `/test-email` endpoint
- [ ] Verify email templates render correctly
- [ ] Set up proper `from_email` and `from_name`
- [ ] Configure SPF and DKIM records for your domain (if using custom domain)
- [ ] Test with real email addresses before going live
- [ ] Monitor email logs for delivery issues

## Troubleshooting

### Emails not sending

1. **Check SMTP credentials** - Verify username, password, host, and port
2. **Check firewall** - Ensure port 587 or 465 is open
3. **Check logs** - Review `application/logs/` for error messages
4. **Test connection** - Use `/test-email` endpoint
5. **Verify email provider settings** - Some providers require app passwords

### Gmail-specific issues

- Use an [App Password](https://support.google.com/accounts/answer/185833) instead of your regular password
- Enable "Less secure app access" (not recommended) or use OAuth2
- Check if 2-factor authentication is enabled (requires app password)

### Common errors

- **"Failed to authenticate"** - Check username and password
- **"Connection timeout"** - Check SMTP host and port
- **"Could not instantiate mail function"** - Check PHP mail configuration

## Security Notes

1. **Never commit credentials** - Use environment variables or config files excluded from version control
2. **Use App Passwords** - For Gmail and similar services, use app-specific passwords
3. **Validate input** - Always validate email addresses and user input
4. **Rate limiting** - Consider implementing rate limiting for email endpoints
5. **SPF/DKIM** - Set up SPF and DKIM records to improve deliverability

## Support

For issues or questions, check:
- CodeIgniter Email Library Documentation: https://codeigniter.com/userguide3/libraries/email.html
- Your email provider's SMTP documentation

