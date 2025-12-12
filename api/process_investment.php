<?php
/**
 * RxHub Process Investment API
 * Handles investment submission from investor dashboard
 */

header('Content-Type: application/json');

require_once dirname(__DIR__) . '/includes/init.php';

// Check if investor is logged in
if (!Session::isLoggedIn('investor')) {
    echo json_encode(['success' => false, 'message' => 'Please login to invest']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$investment_option_id = isset($_POST['investment_option_id']) ? (int)$_POST['investment_option_id'] : 0;
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
$notes = isset($_POST['notes']) ? htmlspecialchars(trim($_POST['notes']), ENT_QUOTES, 'UTF-8') : '';

// Validate inputs
if ($investment_option_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid investment option']);
    exit();
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid investment amount']);
    exit();
}

$investor_id = $_SESSION['investor_id'];

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    // Verify investment option exists and is open
    $stmt = $pdo->prepare("SELECT * FROM investment_options WHERE id = ? AND is_active = 1 AND status = 'open'");
    $stmt->execute([$investment_option_id]);
    $option = $stmt->fetch();
    
    if (!$option) {
        echo json_encode(['success' => false, 'message' => 'Investment option not available']);
        exit();
    }
    
    // Validate amount is within range
    if ($amount < $option['min_investment']) {
        echo json_encode(['success' => false, 'message' => 'Amount is below minimum investment of $' . number_format($option['min_investment'], 2)]);
        exit();
    }
    
    $maxInvestment = $option['max_investment'] ?? $option['target_amount'];
    if ($amount > $maxInvestment) {
        echo json_encode(['success' => false, 'message' => 'Amount exceeds maximum investment of $' . number_format($maxInvestment, 2)]);
        exit();
    }
    
    // Check remaining amount available
    $remaining = $option['target_amount'] - $option['current_amount'];
    if ($amount > $remaining) {
        echo json_encode(['success' => false, 'message' => 'Amount exceeds remaining available investment of $' . number_format($remaining, 2)]);
        exit();
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert investment record
    $stmt = $pdo->prepare("INSERT INTO investor_investments (investor_id, investment_option_id, amount, notes, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$investor_id, $investment_option_id, $amount, $notes]);
    
    // Update current_amount in investment_options
    $stmt = $pdo->prepare("UPDATE investment_options SET current_amount = current_amount + ? WHERE id = ?");
    $stmt->execute([$amount, $investment_option_id]);
    
    // Check if investment is now fully funded
    $stmt = $pdo->prepare("SELECT current_amount, target_amount FROM investment_options WHERE id = ?");
    $stmt->execute([$investment_option_id]);
    $updatedOption = $stmt->fetch();
    
    if ($updatedOption['current_amount'] >= $updatedOption['target_amount']) {
        $stmt = $pdo->prepare("UPDATE investment_options SET status = 'fully_funded' WHERE id = ?");
        $stmt->execute([$investment_option_id]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Investment submitted successfully! Your investment is pending confirmation.'
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Investment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Investment failed. Please try again.']);
}
?>
