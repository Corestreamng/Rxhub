<?php
/**
 * RxHub User Registration API
 * Handles user (healthcare provider) registration
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once dirname(__DIR__) . '/includes/init.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// If JSON parsing failed, try regular POST
if (!$data) {
    $data = $_POST;
}

// Validate required fields
$required = ['full_name', 'email', 'password'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit();
    }
}

// Sanitize inputs
$full_name = htmlspecialchars(trim($data['full_name']), ENT_QUOTES, 'UTF-8');
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$password = $data['password'];
$facility_name = isset($data['facility_name']) ? htmlspecialchars(trim($data['facility_name']), ENT_QUOTES, 'UTF-8') : '';
$facility_type = isset($data['facility_type']) ? htmlspecialchars(trim($data['facility_type']), ENT_QUOTES, 'UTF-8') : 'pharmacy';
$phone = isset($data['phone']) ? htmlspecialchars(trim($data['phone']), ENT_QUOTES, 'UTF-8') : '';

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate password strength (minimum 8 characters)
if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
    exit();
}

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, facility_name, facility_type, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $hashedPassword, $facility_name, $facility_type, $phone]);
    
    $userId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user_id' => $userId
    ]);
    
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}
?>
