# Security Policy

## Overview
RxHub is committed to maintaining the highest security standards to protect user data and ensure the integrity of our healthcare supply chain platform.

## Security Features

### Authentication & Authorization
- ✅ **Password Hashing**: Argon2ID algorithm with high memory and time costs
- ✅ **Session Management**: Secure session handling with HTTPOnly and Secure flags
- ✅ **Role-Based Access Control**: Separate dashboards for users, investors, and admins
- ✅ **Account Lockout**: Automatic lockout after 5 failed login attempts (15 minutes)
- ✅ **JWT Tokens**: For mobile app authentication with 24-hour expiration

### Data Protection
- ✅ **Input Validation**: All user inputs are validated and sanitized
- ✅ **SQL Injection Prevention**: Prepared statements for all database queries
- ✅ **XSS Protection**: Output encoding and Content Security Policy headers
- ✅ **CSRF Protection**: Token-based CSRF protection for all forms
- ✅ **Rate Limiting**: 100 requests per minute per IP address

### Network Security
- ✅ **HTTPS Only**: All communications encrypted with TLS 1.2+
- ✅ **Secure Headers**: X-Frame-Options, X-XSS-Protection, CSP, HSTS
- ✅ **CORS Policy**: Restricted cross-origin requests
- ✅ **Referrer Policy**: Strict referrer policy to prevent information leakage

### Application Security
- ✅ **Error Handling**: Custom error pages, no sensitive information in errors
- ✅ **Logging**: Comprehensive security event logging
- ✅ **File Upload Security**: Validation and sanitization of uploaded files
- ✅ **Directory Protection**: .htaccess protection for sensitive directories

## Security Best Practices for Developers

### 1. Database Security
```php
// ✅ GOOD - Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// ❌ BAD - Never concatenate user input
$query = "SELECT * FROM users WHERE email = '$email'"; // Vulnerable to SQL injection
```

### 2. XSS Prevention
```php
// ✅ GOOD - Always escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
// or use the helper function
echo h($user_input);

// ❌ BAD - Never output raw user input
echo $user_input; // Vulnerable to XSS
```

### 3. CSRF Protection
```php
// ✅ GOOD - Include CSRF token in forms
<form method="POST">
    <?php echo Security::csrfField(); ?>
    <!-- form fields -->
</form>

// Verify on submission
if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

### 4. Password Security
```php
// ✅ GOOD - Use secure password hashing
$hash = Security::hashPassword($password);

// Verify password
if (Security::verifyPassword($password, $hash)) {
    // Password correct
}

// ❌ BAD - Never use MD5 or SHA1
$hash = md5($password); // Insecure
```

### 5. Session Security
```php
// ✅ GOOD - Regenerate session ID after login
session_regenerate_id(true);

// Set secure session parameters
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
```

### 6. Rate Limiting
```php
// ✅ GOOD - Check rate limits
if (!Security::checkRateLimit($_SERVER['REMOTE_ADDR'], 100, 60)) {
    http_response_code(429);
    die('Rate limit exceeded');
}
```

## Configuration Requirements

### Environment Variables
Set these in production:
```env
APP_ENV=production
APP_URL=https://rxhub.com.ng
DB_HOST=localhost
DB_NAME=rxhub_production
DB_USER=rxhub_user
DB_PASS=strong_secure_password_here
JWT_SECRET=very_long_random_secret_key_here
```

### Server Configuration

#### Apache (.htaccess)
```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Disable directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Security headers
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set X-Content-Type-Options "nosniff"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"

# HSTS (uncomment in production)
# Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

#### PHP Configuration (php.ini)
```ini
; Hide PHP version
expose_php = Off

; Disable dangerous functions
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source

; Session security
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict
session.use_strict_mode = 1
session.use_only_cookies = 1

; Upload limits
upload_max_filesize = 10M
post_max_size = 10M

; Error reporting (production)
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
error_log = /var/log/php_errors.log
```

### Database Security

#### MySQL/MariaDB
```sql
-- Create dedicated user with limited privileges
CREATE USER 'rxhub_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON rxhub_db.* TO 'rxhub_user'@'localhost';
FLUSH PRIVILEGES;

-- Disable remote root access
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
FLUSH PRIVILEGES;
```

## Incident Response

### If You Discover a Security Vulnerability

**DO NOT** create a public GitHub issue.

Instead, please email: **security@rxhub.com.ng**

Include:
1. Description of the vulnerability
2. Steps to reproduce
3. Potential impact
4. Suggested fix (if available)

We will respond within 48 hours and work with you to address the issue.

## Security Checklist for Deployment

- [ ] All environment variables configured
- [ ] HTTPS enabled with valid SSL certificate
- [ ] Database credentials use strong passwords
- [ ] JWT secret is randomly generated and secure
- [ ] Error reporting disabled in production
- [ ] All security headers configured
- [ ] File permissions set correctly (755 for directories, 644 for files)
- [ ] Sensitive directories protected
- [ ] Logs directory is writable
- [ ] Backup strategy implemented
- [ ] Monitoring and alerting configured
- [ ] Security event logging enabled
- [ ] Rate limiting configured
- [ ] WAF (Web Application Firewall) enabled
- [ ] Regular security updates scheduled

## Security Monitoring

### Log Files
- `/logs/security.log` - Security events (login attempts, suspicious activity)
- `/logs/error.log` - Application errors
- `/logs/access.log` - Access logs (server level)

### What We Log
- Failed login attempts
- Account lockouts
- SQL injection attempts
- XSS attempts
- Rate limit violations
- CSRF token failures
- Unauthorized access attempts
- API authentication failures

### Regular Audits
- Weekly log review
- Monthly security assessment
- Quarterly penetration testing
- Annual third-party security audit

## Compliance

### Data Protection
- NDPR (Nigeria Data Protection Regulation) compliant
- GDPR principles followed for data handling
- User data encrypted at rest and in transit

### Healthcare Standards
- HIPAA principles for healthcare data (where applicable)
- Secure handling of pharmaceutical supply chain data

## Updates and Patches

### Security Update Policy
- Critical vulnerabilities: Patched within 24 hours
- High severity: Patched within 7 days
- Medium severity: Patched within 30 days
- Low severity: Included in next regular release

### Dependencies
- Regular updates of third-party libraries
- Automated vulnerability scanning
- Monthly dependency audit

## Contact

**Security Team**: security@rxhub.com.ng  
**Emergency Contact**: +234-800-RXHUB-SEC  
**Bug Bounty Program**: Coming soon

---

**Last Updated**: 2025-12-13  
**Version**: 2.0.0
