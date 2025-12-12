<?php
/**
 * RxHub Payment Processing API
 * Handles payment recording for orders
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once dirname(__DIR__) . '/includes/init.php';

// Check if user is logged in
if (!Session::isLoggedIn('user')) {
    echo json_encode(['success' => false, 'message' => 'Please log in to make a payment']);
    exit();
}

$user_id = Session::get('user_id');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order_id']) || !isset($input['payment_method'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$order_id = intval($input['order_id']);
$payment_method = $input['payment_method'];
$reference = $input['reference'] ?? '';

$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    // Verify order belongs to user
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit();
    }
    
    if ($order['payment_status'] === 'paid') {
        echo json_encode(['success' => false, 'message' => 'Order is already paid']);
        exit();
    }
    
    $pdo->beginTransaction();
    
    // Generate payment reference with more robust unique ID
    $payment_reference = 'PAY-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    
    // Create payment record
    $stmt = $pdo->prepare("
        INSERT INTO payments (payment_reference, order_id, user_id, amount, payment_method, status, transaction_reference)
        VALUES (?, ?, ?, ?, ?, 'completed', ?)
    ");
    $stmt->execute([$payment_reference, $order_id, $user_id, $order['total'], $payment_method, $reference]);
    
    // Update order payment status
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', status = 'confirmed' WHERE id = ?");
    $stmt->execute([$order_id]);
    
    // Record sales for profit tracking
    $stmt = $pdo->prepare("
        SELECT oi.*, p.purchase_price 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();
    
    $sales_cycle = date('Y-m'); // Monthly cycle
    $stmt = $pdo->prepare("
        INSERT INTO sales (order_id, product_id, quantity, purchase_price, selling_price, profit, sales_cycle)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($items as $item) {
        $profit = ($item['unit_price'] - $item['purchase_price']) * $item['quantity'];
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['purchase_price'],
            $item['unit_price'],
            $profit,
            $sales_cycle
        ]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment recorded successfully',
        'payment_reference' => $payment_reference
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Payment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to process payment']);
}
?>
