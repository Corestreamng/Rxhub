#!/usr/bin/env php
<?php
/**
 * Generate Secure Secrets for RxHub Configuration
 * 
 * This script generates secure random secrets for use in production.
 * Run this script and copy the output to your environment variables.
 * 
 * Usage: php scripts/generate-secrets.php
 */

echo "=================================================\n";
echo "   RxHub Secure Configuration Generator\n";
echo "=================================================\n\n";

echo "Generated secure secrets for your production environment:\n\n";

// Generate JWT Secret (64 characters)
$jwt_secret = bin2hex(random_bytes(32));
echo "JWT_SECRET={$jwt_secret}\n";

// Generate Database Password (32 characters with special chars)
$db_pass_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
$db_pass = '';
for ($i = 0; $i < 32; $i++) {
    $db_pass .= $db_pass_chars[random_int(0, strlen($db_pass_chars) - 1)];
}
echo "DB_PASS={$db_pass}\n";

// Generate Session Secret (64 characters)
$session_secret = bin2hex(random_bytes(32));
echo "SESSION_SECRET={$session_secret}\n";

// Generate API Key (32 characters)
$api_key = bin2hex(random_bytes(16));
echo "API_KEY={$api_key}\n";

echo "\n=================================================\n";
echo "IMPORTANT: Store these secrets securely!\n";
echo "=================================================\n\n";

echo "Instructions:\n";
echo "1. Copy these values to your .env file or server environment variables\n";
echo "2. Never commit these secrets to version control\n";
echo "3. Use different secrets for each environment (dev, staging, production)\n";
echo "4. Rotate secrets periodically (recommended: every 90 days)\n";
echo "5. Keep a secure backup of production secrets\n\n";

echo "Example .env file format:\n";
echo "-------------------------------------------------\n";
echo "APP_ENV=production\n";
echo "APP_URL=https://yourdomain.com\n";
echo "DB_HOST=localhost\n";
echo "DB_NAME=rxhub_production\n";
echo "DB_USER=rxhub_user\n";
echo "DB_PASS={$db_pass}\n";
echo "JWT_SECRET={$jwt_secret}\n";
echo "SESSION_SECRET={$session_secret}\n";
echo "API_KEY={$api_key}\n";
echo "-------------------------------------------------\n\n";

echo "✅ Configuration generation complete!\n\n";
