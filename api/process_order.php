<?php
/**
 * RxHub Order Processing API
 * Handles order creation from cart
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once dirname(__DIR__) . '/includes/init.php';

// Check if user is logged in
if (!Session::isLoggedIn('user')) {
    echo json_encode(['success' => false, 'message' => 'Please log in to place an order']);
    exit();
}

$user_id = Session::get('user_id');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['items']) || empty($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'No items in cart']);
    exit();
}

$items = $input['items'];

$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Generate order number with robust unique ID
    $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    
    // Validate and recalculate prices from database (server-side validation)
    $subtotal = 0;
    $validated_items = [];
    
    foreach ($items as $item) {
        $product_id = intval($item['id']);
        $quantity = intval($item['quantity']);
        
        if ($product_id <= 0 || $quantity <= 0) {
            continue;
        }
        
        // Fetch actual price from database to prevent price tampering
        $stmt = $pdo->prepare("SELECT id, name, selling_price FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            continue;
        }
        
        $validated_items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => floatval($product['selling_price']),
            'quantity' => $quantity
        ];
        
        $subtotal += floatval($product['selling_price']) * $quantity;
    }
    
    if (empty($validated_items)) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'No valid items in cart']);
        exit();
    }
    
    // Get tax rate from settings
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'tax_rate'");
    $stmt->execute();
    $tax_rate = floatval($stmt->fetch()['setting_value'] ?? 7.5);
    
    $tax = $subtotal * ($tax_rate / 100);
    $total = $subtotal + $tax;
    
    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (order_number, user_id, subtotal, tax, total, status, payment_status)
        VALUES (?, ?, ?, ?, ?, 'pending', 'unpaid')
    ");
    $stmt->execute([$order_number, $user_id, $subtotal, $tax, $total]);
    $order_id = $pdo->lastInsertId();
    
    // Add order items using validated data
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    foreach ($validated_items as $item) {
        $item_total = $item['price'] * $item['quantity'];
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price'], $item_total]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_number' => $order_number,
        'order_id' => $order_id,
        'total' => $total
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Order error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to create order']);
}
?>
