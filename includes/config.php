<?php
/**
 * RxHub Configuration File
 * Core settings for the pharmaceutical supply chain platform
 */

// Prevent direct access
if (!defined('RXHUB_LOADED')) {
    define('RXHUB_LOADED', true);
}

// Application settings
define('APP_NAME', 'RxHub');
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

// Database credentials - Use environment variables for production
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'discom_rxhubdb');
define('DB_USER', getenv('DB_USER') ?: 'discom_rxhubuser');
define('DB_PASS', getenv('DB_PASS') ?: 'Team@3443_$#');
define('DB_CHARSET', 'utf8mb4');

// Session settings
define('SESSION_NAME', 'rxhub_session');
define('SESSION_LIFETIME', 3600 * 24); // 24 hours

// File paths
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('USER_PATH', ROOT_PATH . '/user');
define('INVESTOR_PATH', ROOT_PATH . '/investor');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

// Default settings
define('DEFAULT_CURRENCY', 'NGN');
define('DEFAULT_CURRENCY_SYMBOL', '₦');
define('DEFAULT_TIMEZONE', 'Africa/Lagos');

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Error reporting (set to 0 in production)
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
