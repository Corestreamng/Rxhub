<?php
/**
 * Redirect to new investor login location
 */
header('Location: investor/login.php');
exit();
?>


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
if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit();
}

// Sanitize inputs
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$password = $data['password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    // Find investor by email
    $stmt = $pdo->prepare("SELECT id, full_name, email, password, company_name, investor_type, investment_range, is_active FROM investors WHERE email = ?");
    $stmt->execute([$email]);
    $investor = $stmt->fetch();
    
    if (!$investor) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    }
    
    // Check if investor is active
    if (!$investor['is_active']) {
        echo json_encode(['success' => false, 'message' => 'Account is deactivated. Please contact support.']);
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $investor['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    }
    
    // Set session
    $_SESSION['investor_id'] = $investor['id'];
    $_SESSION['investor_email'] = $investor['email'];
    $_SESSION['investor_name'] = $investor['full_name'];
    $_SESSION['user_type'] = 'investor';
    $_SESSION['company_name'] = $investor['company_name'];
    $_SESSION['investor_type'] = $investor['investor_type'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'investor' => [
            'id' => $investor['id'],
            'name' => $investor['full_name'],
            'email' => $investor['email'],
            'company_name' => $investor['company_name'],
            'investor_type' => $investor['investor_type']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Investor login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Login failed. Please try again.']);
}
?>
