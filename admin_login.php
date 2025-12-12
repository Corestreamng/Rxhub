<?php
/**
 * RxHub Admin Login API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit();
}

$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$password = $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT a.*, r.name as role_name FROM admins a LEFT JOIN roles r ON a.role_id = r.id WHERE a.email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    }
    
    if (!$admin['is_active']) {
        echo json_encode(['success' => false, 'message' => 'Account is deactivated']);
        exit();
    }
    
    if (!password_verify($password, $admin['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    }
    
    // Set session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['full_name'];
    $_SESSION['admin_role'] = $admin['role_name'];
    $_SESSION['user_type'] = 'admin';
    
    // Update last login
    $stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$admin['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'admin' => [
            'id' => $admin['id'],
            'name' => $admin['full_name'],
            'email' => $admin['email'],
            'role' => $admin['role_name']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Admin login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Login failed. Please try again.']);
}
?>
