<?php
/**
 * RxHub Authentication Functions
 * Handles user, investor, and admin authentication
 */

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Register a new user (healthcare provider)
     */
    public function registerUser($data) {
        // Validate required fields
        $required = ['full_name', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Field '{$field}' is required"];
            }
        }
        
        // Check if email exists
        $existing = $this->db->fetchOne("SELECT id FROM users WHERE email = ?", [$data['email']]);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert user
        try {
            $userId = $this->db->insert('users', [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'facility_name' => $data['facility_name'] ?? null,
                'facility_type' => $data['facility_type'] ?? 'pharmacy',
                'phone' => $data['phone'] ?? null
            ]);
            
            return ['success' => true, 'user_id' => $userId, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            error_log("User registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    /**
     * Login user
     */
    public function loginUser($email, $password) {
        $user = $this->db->fetchOne(
            "SELECT id, full_name, email, password, facility_name, facility_type, is_active FROM users WHERE email = ?",
            [$email]
        );
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Account is deactivated'];
        }
        
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Set session
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['full_name']);
        Session::set('user_email', $user['email']);
        Session::set('facility_name', $user['facility_name']);
        Session::set('facility_type', $user['facility_type']);
        
        return ['success' => true, 'message' => 'Login successful', 'redirect' => 'user/dashboard.php'];
    }
    
    /**
     * Register a new investor
     */
    public function registerInvestor($data) {
        // Validate required fields
        $required = ['full_name', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Field '{$field}' is required"];
            }
        }
        
        // Check if email exists
        $existing = $this->db->fetchOne("SELECT id FROM investors WHERE email = ?", [$data['email']]);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert investor
        try {
            $investorId = $this->db->insert('investors', [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'investor_type' => $data['investor_type'] ?? 'individual',
                'investment_range' => $data['investment_range'] ?? 'under_10k'
            ]);
            
            return ['success' => true, 'investor_id' => $investorId, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            error_log("Investor registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    /**
     * Login investor
     */
    public function loginInvestor($email, $password) {
        $investor = $this->db->fetchOne(
            "SELECT id, full_name, email, password, company_name, investor_type, is_active FROM investors WHERE email = ?",
            [$email]
        );
        
        if (!$investor) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        if (!$investor['is_active']) {
            return ['success' => false, 'message' => 'Account is deactivated'];
        }
        
        if (!password_verify($password, $investor['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Set session
        Session::regenerate();
        Session::set('investor_id', $investor['id']);
        Session::set('investor_name', $investor['full_name']);
        Session::set('investor_email', $investor['email']);
        Session::set('company_name', $investor['company_name']);
        Session::set('investor_type', $investor['investor_type']);
        
        return ['success' => true, 'message' => 'Login successful', 'redirect' => 'investor/dashboard.php'];
    }
    
    /**
     * Login admin
     */
    public function loginAdmin($email, $password) {
        $admin = $this->db->fetchOne(
            "SELECT a.id, a.full_name, a.email, a.password, a.role_id, a.is_active, r.name as role_name, r.permissions 
             FROM admins a 
             LEFT JOIN roles r ON a.role_id = r.id 
             WHERE a.email = ?",
            [$email]
        );
        
        if (!$admin) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        if (!$admin['is_active']) {
            return ['success' => false, 'message' => 'Account is deactivated'];
        }
        
        if (!password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Update last login
        $this->db->update('admins', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $admin['id']]);
        
        // Set session
        Session::regenerate();
        Session::set('admin_id', $admin['id']);
        Session::set('admin_name', $admin['full_name']);
        Session::set('admin_email', $admin['email']);
        Session::set('admin_role', $admin['role_name']);
        Session::set('admin_permissions', json_decode($admin['permissions'], true));
        
        return ['success' => true, 'message' => 'Login successful', 'redirect' => 'admin/dashboard.php'];
    }
    
    /**
     * Logout
     */
    public function logout($type = 'user') {
        Session::destroy();
        
        $redirects = [
            'admin' => 'admin/login.php',
            'investor' => 'investor/login.php',
            'user' => 'index.php'
        ];
        
        return ['success' => true, 'redirect' => $redirects[$type] ?? 'index.php'];
    }
    
    /**
     * Check authentication
     */
    public function requireAuth($type = 'user') {
        if (!Session::isLoggedIn($type)) {
            $redirects = [
                'admin' => 'admin/login.php',
                'investor' => 'investor/login.php',
                'user' => 'index.php'
            ];
            
            header('Location: ' . ($redirects[$type] ?? 'index.php'));
            exit();
        }
    }
}
