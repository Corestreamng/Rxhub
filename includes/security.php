<?php
/**
 * RxHub Security Configuration
 * Enterprise-grade security measures for the application
 */

if (!defined('RXHUB_LOADED')) {
    die('Direct access not permitted');
}

class Security {
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get CSRF token HTML input field
     */
    public static function csrfField() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Rate limiting check
     * 
     * NOTE: This implementation uses PHP sessions for simplicity in single-server environments.
     * For production environments with load balancing or multiple servers, implement one of:
     * - Redis/Memcached for shared rate limit tracking
     * - Database-based rate limiting
     * - API Gateway rate limiting (AWS API Gateway, Kong, etc.)
     * 
     * @param string $key Unique identifier (e.g., IP address, user ID)
     * @param int $max_attempts Maximum allowed attempts
     * @param int $time_window Time window in seconds
     * @return bool True if allowed, false if rate limit exceeded
     */
    public static function checkRateLimit($key, $max_attempts = 100, $time_window = 60) {
        $cache_key = 'rate_limit_' . md5($key);
        
        // TODO: In production with load balancers, replace session storage with:
        // - Redis: $redis->incr($cache_key); $redis->expire($cache_key, $time_window);
        // - Database: Store rate limit data in dedicated table with indexed columns
        
        // Get current attempts from session (single-server implementation)
        if (!isset($_SESSION[$cache_key])) {
            $_SESSION[$cache_key] = [
                'attempts' => 0,
                'reset_time' => time() + $time_window
            ];
        }
        
        $rate_data = $_SESSION[$cache_key];
        
        // Reset if time window has passed
        if (time() > $rate_data['reset_time']) {
            $_SESSION[$cache_key] = [
                'attempts' => 1,
                'reset_time' => time() + $time_window
            ];
            return true;
        }
        
        // Check if limit exceeded
        if ($rate_data['attempts'] >= $max_attempts) {
            self::logSecurityEvent('RATE_LIMIT_EXCEEDED', "Key: $key, Attempts: {$rate_data['attempts']}", 'WARNING');
            return false;
        }
        
        // Increment attempts
        $_SESSION[$cache_key]['attempts']++;
        return true;
    }
    
    /**
     * Get rate limit info
     */
    public static function getRateLimitInfo($key) {
        $cache_key = 'rate_limit_' . md5($key);
        
        if (!isset($_SESSION[$cache_key])) {
            return [
                'attempts' => 0,
                'remaining' => 100,
                'reset_time' => time() + 60
            ];
        }
        
        $rate_data = $_SESSION[$cache_key];
        return [
            'attempts' => $rate_data['attempts'],
            'remaining' => max(0, 100 - $rate_data['attempts']),
            'reset_time' => $rate_data['reset_time']
        ];
    }
    
    /**
     * Account lockout after failed login attempts
     * @param string $identifier User email or IP
     * @param int $max_attempts Maximum failed attempts before lockout
     * @param int $lockout_duration Lockout duration in seconds
     */
    public static function checkLoginAttempts($identifier, $max_attempts = 5, $lockout_duration = 900) {
        $cache_key = 'login_attempts_' . md5($identifier);
        
        if (!isset($_SESSION[$cache_key])) {
            $_SESSION[$cache_key] = [
                'attempts' => 0,
                'locked_until' => 0
            ];
        }
        
        $login_data = $_SESSION[$cache_key];
        
        // Check if currently locked
        if ($login_data['locked_until'] > time()) {
            $remaining = $login_data['locked_until'] - time();
            return [
                'allowed' => false,
                'locked_until' => $login_data['locked_until'],
                'remaining_seconds' => $remaining
            ];
        }
        
        // Reset if lockout expired
        if ($login_data['locked_until'] > 0 && $login_data['locked_until'] <= time()) {
            $_SESSION[$cache_key] = [
                'attempts' => 0,
                'locked_until' => 0
            ];
            return ['allowed' => true, 'attempts' => 0];
        }
        
        return [
            'allowed' => $login_data['attempts'] < $max_attempts,
            'attempts' => $login_data['attempts'],
            'remaining_attempts' => max(0, $max_attempts - $login_data['attempts'])
        ];
    }
    
    /**
     * Record failed login attempt
     */
    public static function recordFailedLogin($identifier, $max_attempts = 5, $lockout_duration = 900) {
        $cache_key = 'login_attempts_' . md5($identifier);
        
        if (!isset($_SESSION[$cache_key])) {
            $_SESSION[$cache_key] = [
                'attempts' => 0,
                'locked_until' => 0
            ];
        }
        
        $_SESSION[$cache_key]['attempts']++;
        
        // Lock account if max attempts reached
        if ($_SESSION[$cache_key]['attempts'] >= $max_attempts) {
            $_SESSION[$cache_key]['locked_until'] = time() + $lockout_duration;
        }
    }
    
    /**
     * Reset login attempts on successful login
     */
    public static function resetLoginAttempts($identifier) {
        $cache_key = 'login_attempts_' . md5($identifier);
        unset($_SESSION[$cache_key]);
    }
    
    /**
     * Sanitize input to prevent XSS
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number (Nigerian format)
     */
    public static function validatePhone($phone) {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid Nigerian phone number
        // Should be 10-14 digits (with or without country code)
        return preg_match('/^(\+?234|0)?[789][01]\d{8}$/', $phone);
    }
    
    /**
     * Validate password strength
     * Requirements: Min 8 characters, at least one uppercase, one lowercase, one number
     */
    public static function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Generate secure random password
     */
    public static function generateSecurePassword($length = 12) {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $all = $uppercase . $lowercase . $numbers . $special;
        
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        return str_shuffle($password);
    }
    
    /**
     * Hash password securely
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    /**
     * Verify password against hash
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate JWT token for API authentication
     * @throws Exception if JWT_SECRET is not configured
     */
    public static function generateJWT($payload, $secret_key = null) {
        if ($secret_key === null) {
            $secret_key = getenv('JWT_SECRET');
            if (empty($secret_key)) {
                throw new Exception('JWT_SECRET environment variable must be configured. Generate a secure random key and set it in your environment.');
            }
        }
        
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = time() + 86400; // 24 hours expiration
        $payload['iat'] = time();
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    /**
     * Verify and decode JWT token
     * @throws Exception if JWT_SECRET is not configured
     */
    public static function verifyJWT($jwt, $secret_key = null) {
        if ($secret_key === null) {
            $secret_key = getenv('JWT_SECRET');
            if (empty($secret_key)) {
                throw new Exception('JWT_SECRET environment variable must be configured. Generate a secure random key and set it in your environment.');
            }
        }
        
        $tokenParts = explode('.', $jwt);
        
        if (count($tokenParts) !== 3) {
            return false;
        }
        
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
        
        // Verify signature
        $signature = hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], $secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        if ($base64UrlSignature !== $signatureProvided) {
            return false;
        }
        
        $payloadArray = json_decode($payload, true);
        
        // Check expiration
        if (isset($payloadArray['exp']) && $payloadArray['exp'] < time()) {
            return false;
        }
        
        return $payloadArray;
    }
    
    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Log security event
     */
    public static function logSecurityEvent($event_type, $details, $severity = 'INFO') {
        $log_file = ROOT_PATH . '/logs/security.log';
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? $_SESSION['investor_id'] ?? 'guest';
        
        $log_entry = sprintf(
            "[%s] [%s] [%s] IP: %s | User: %s | Event: %s | Details: %s | UA: %s\n",
            $timestamp,
            $severity,
            $event_type,
            $ip,
            $user_id,
            $event_type,
            is_array($details) ? json_encode($details) : $details,
            $user_agent
        );
        
        error_log($log_entry, 3, $log_file);
    }
    
    /**
     * Check for SQL injection patterns
     */
    public static function detectSQLInjection($input) {
        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(--|\#|\/\*|\*\/)/i',
            '/(\bOR\b.*=.*)/i',
            '/(\bAND\b.*=.*)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                self::logSecurityEvent('SQL_INJECTION_ATTEMPT', $input, 'CRITICAL');
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for XSS patterns
     */
    public static function detectXSS($input) {
        $patterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
            '/on\w+\s*=\s*["\'].*?["\']/i',
            '/javascript:/i',
            '/vbscript:/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                self::logSecurityEvent('XSS_ATTEMPT', $input, 'CRITICAL');
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Secure headers configuration
     */
    public static function setSecureHeaders() {
        // Prevent clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // XSS Protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Content Type Options
        header('X-Content-Type-Options: nosniff');
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; img-src 'self' data: https:; font-src 'self' https://cdnjs.cloudflare.com;");
        
        // HTTPS Strict Transport Security (only for production)
        if (getenv('APP_ENV') === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
}

// Set secure headers on every request
Security::setSecureHeaders();
