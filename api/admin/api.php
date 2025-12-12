<?php
/**
 * RxHub Admin API
 * Handles admin actions: create users, products, investments, etc.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';
$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

switch ($action) {
    case 'create_user':
        createUser($pdo);
        break;
    case 'create_product':
        createProduct($pdo);
        break;
    case 'create_investment':
        createInvestment($pdo);
        break;
    case 'update_order_status':
        updateOrderStatus($pdo);
        break;
    case 'add_stock':
        addStock($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function createUser($pdo) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? 'user';
    $facility_name = trim($_POST['facility_name'] ?? '');
    $facility_type = $_POST['facility_type'] ?? 'pharmacy';
    
    if (empty($full_name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Required fields missing']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if ($user_type === 'investor') {
            $stmt = $pdo->prepare("INSERT INTO investors (full_name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $hashedPassword, $phone]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, facility_name, facility_type) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $hashedPassword, $phone, $facility_name, $facility_type]);
        }
        
        echo json_encode(['success' => true, 'message' => 'User created successfully']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
        } else {
            error_log("Create user error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to create user']);
        }
    }
}

function createProduct($pdo) {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = $_POST['category_id'] ?: null;
    $manufacturer = trim($_POST['manufacturer'] ?? '');
    $purchase_price = floatval($_POST['purchase_price'] ?? 0);
    $selling_price = floatval($_POST['selling_price'] ?? 0);
    
    if (empty($sku) || empty($name) || $purchase_price <= 0 || $selling_price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Required fields missing or invalid']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO products (sku, name, description, category_id, manufacturer, purchase_price, selling_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$sku, $name, $description, $category_id, $manufacturer, $purchase_price, $selling_price]);
        
        echo json_encode(['success' => true, 'message' => 'Product created successfully']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'SKU already exists']);
        } else {
            error_log("Create product error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to create product']);
        }
    }
}

function createInvestment($pdo) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'equity';
    $risk_level = $_POST['risk_level'] ?? 'medium';
    $min_investment = floatval($_POST['min_investment'] ?? 0);
    $max_investment = floatval($_POST['max_investment'] ?? 0) ?: null;
    $target_amount = floatval($_POST['target_amount'] ?? 0);
    $expected_roi = trim($_POST['expected_roi'] ?? '');
    $duration_months = intval($_POST['duration_months'] ?? 0);
    
    if (empty($title) || empty($description) || $min_investment <= 0 || $target_amount <= 0 || $duration_months <= 0) {
        echo json_encode(['success' => false, 'message' => 'Required fields missing or invalid']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO investment_options (title, description, category, min_investment, max_investment, target_amount, expected_roi, duration_months, risk_level, status, start_date, end_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'open', CURDATE(), DATE_ADD(CURDATE(), INTERVAL ? MONTH))
        ");
        $stmt->execute([$title, $description, $category, $min_investment, $max_investment, $target_amount, $expected_roi, $duration_months, $risk_level, $duration_months]);
        
        echo json_encode(['success' => true, 'message' => 'Investment option created successfully']);
    } catch (PDOException $e) {
        error_log("Create investment error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to create investment']);
    }
}

function updateOrderStatus($pdo) {
    $order_id = intval($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if ($order_id <= 0 || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        
        echo json_encode(['success' => true, 'message' => 'Order status updated']);
    } catch (PDOException $e) {
        error_log("Update order error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to update order']);
    }
}

function addStock($pdo) {
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $batch_number = trim($_POST['batch_number'] ?? '');
    $expiry_date = $_POST['expiry_date'] ?? null;
    $purchase_price = floatval($_POST['purchase_price'] ?? 0);
    
    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO stock (product_id, quantity, batch_number, expiry_date, purchase_price, added_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $quantity, $batch_number, $expiry_date, $purchase_price, $_SESSION['admin_id']]);
        
        echo json_encode(['success' => true, 'message' => 'Stock added successfully']);
    } catch (PDOException $e) {
        error_log("Add stock error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to add stock']);
    }
}
?>
