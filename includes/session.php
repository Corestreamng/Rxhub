<?php
/**
 * RxHub Session Handler
 * Manages user sessions securely
 */

require_once __DIR__ . '/config.php';

class Session {
    private static $started = false;
    
    /**
     * Start session with secure settings
     */
    public static function start() {
        if (self::$started) {
            return;
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session settings
            ini_set('session.name', SESSION_NAME);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
            ini_set('session.cookie_lifetime', SESSION_LIFETIME);
            
            // Use secure cookies in production
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            
            session_start();
        }
        
        self::$started = true;
    }
    
    /**
     * Set session value
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session value
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session key exists
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session value
     */
    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Destroy session
     */
    public static function destroy() {
        self::start();
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        self::$started = false;
    }
    
    /**
     * Regenerate session ID
     */
    public static function regenerate() {
        self::start();
        session_regenerate_id(true);
    }
    
    /**
     * Flash message (set and get once)
     */
    public static function flash($key, $value = null) {
        self::start();
        
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return;
        }
        
        if (isset($_SESSION['_flash'][$key])) {
            $value = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $value;
        }
        
        return null;
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn($type = 'user') {
        self::start();
        
        switch ($type) {
            case 'admin':
                return isset($_SESSION['admin_id']);
            case 'investor':
                return isset($_SESSION['investor_id']);
            case 'user':
            default:
                return isset($_SESSION['user_id']);
        }
    }
    
    /**
     * Get current user ID based on type
     */
    public static function getUserId($type = 'user') {
        self::start();
        
        switch ($type) {
            case 'admin':
                return $_SESSION['admin_id'] ?? null;
            case 'investor':
                return $_SESSION['investor_id'] ?? null;
            case 'user':
            default:
                return $_SESSION['user_id'] ?? null;
        }
    }
}

// Auto-start session
Session::start();
