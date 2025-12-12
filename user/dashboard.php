<?php
/**
 * RxHub Healthcare Facility Dashboard
 * View products, manage orders, payments, and reports
 */

require_once dirname(__DIR__) . '/includes/init.php';

// Check if user is logged in
if (!Session::isLoggedIn('user')) {
    header('Location: login.php');
    exit();
}

$user_id = Session::get('user_id');
$user_name = h(Session::get('user_name'));
$facility_name = h(Session::get('facility_name') ?? '');

// Get database connection
$db = Database::getInstance();

// Get current section
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Initialize data
$products = [];
$orders = [];
$stats = [
    'total_orders' => 0,
    'pending_orders' => 0,
    'total_spent' => 0,
    'products_ordered' => 0
];

try {
    // Get products
    $products = $db->fetchAll("SELECT p.*, c.name as category_name FROM products p LEFT JOIN product_categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.name");
    
    // Get user's orders
    $orders = $db->fetchAll("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC LIMIT 10", [$user_id]);
    
    // Get stats
    $stats['total_orders'] = $db->fetchColumn("SELECT COUNT(*) FROM orders WHERE user_id = ?", [$user_id]);
    $stats['pending_orders'] = $db->fetchColumn("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status IN ('pending', 'confirmed', 'processing')", [$user_id]);
    $stats['total_spent'] = $db->fetchColumn("SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = ? AND payment_status = 'paid'", [$user_id]);
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}

// Get categories for filter
$categories = [];
try {
    $categories = $db->fetchAll("SELECT * FROM product_categories ORDER BY name");
} catch (Exception $e) {
    error_log("Categories error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RxHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #9900cc;
            --primary-dark: #7a00a3;
            --secondary: #ff3300;
            --accent: #006666;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-width: 260px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f1f5f9; color: var(--dark); }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--dark) 0%, #0f172a 100%);
            padding: 20px 0;
            z-index: 100;
            overflow-y: auto;
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px 30px;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-logo i { color: var(--secondary); }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 5px; }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--primary);
        }
        
        .sidebar-menu a i { width: 20px; text-align: center; }
        .menu-section { padding: 15px 20px 10px; color: rgba(255,255,255,0.4); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
        
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        
        .topbar {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .topbar-left h1 { font-size: 1.5rem; color: var(--dark); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .user-dropdown { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        
        .content { padding: 30px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .stat-card.purple { border-top: 4px solid var(--primary); }
        .stat-card.green { border-top: 4px solid var(--success); }
        .stat-card.orange { border-top: 4px solid var(--warning); }
        .stat-card.blue { border-top: 4px solid var(--accent); }
        
        .stat-card .icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
        .stat-card.purple .icon { background: rgba(153,0,204,0.1); color: var(--primary); }
        .stat-card.green .icon { background: rgba(34,197,94,0.1); color: var(--success); }
        .stat-card.orange .icon { background: rgba(245,158,11,0.1); color: var(--warning); }
        .stat-card.blue .icon { background: rgba(0,102,102,0.1); color: var(--accent); }
        
        .stat-card h3 { font-size: 2rem; margin-bottom: 5px; }
        .stat-card p { color: var(--gray); font-size: 0.9rem; }
        
        .section-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--light-gray); }
        .section-header h2 { font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
        
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        
        .product-card { border: 1px solid var(--light-gray); border-radius: 10px; padding: 20px; transition: all 0.3s; }
        .product-card:hover { border-color: var(--primary); box-shadow: 0 5px 15px rgba(153,0,204,0.1); }
        .product-category { font-size: 0.75rem; color: var(--primary); text-transform: uppercase; margin-bottom: 8px; }
        .product-name { font-size: 1.1rem; font-weight: 600; margin-bottom: 5px; }
        .product-manufacturer { font-size: 0.85rem; color: var(--gray); margin-bottom: 15px; }
        .product-price { font-size: 1.3rem; font-weight: 700; color: var(--primary); margin-bottom: 15px; }
        .product-actions { display: flex; gap: 10px; }
        
        .btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.3s; border: none; font-size: 0.9rem; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; border: 1px solid var(--primary); color: var(--primary); }
        .btn-outline:hover { background: var(--primary); color: white; }
        .btn-sm { padding: 8px 15px; font-size: 0.85rem; }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid var(--light-gray); }
        .data-table th { background: var(--light); font-weight: 600; color: var(--dark); font-size: 0.9rem; }
        .data-table tr:hover { background: var(--light); }
        
        .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
        .badge.pending, .badge.warning { background: rgba(245,158,11,0.1); color: var(--warning); }
        .badge.confirmed, .badge.info { background: rgba(59,130,246,0.1); color: #3b82f6; }
        .badge.processing, .badge.primary { background: rgba(153,0,204,0.1); color: var(--primary); }
        .badge.shipped { background: rgba(0,102,102,0.1); color: var(--accent); }
        .badge.delivered, .badge.paid, .badge.success { background: rgba(34,197,94,0.1); color: var(--success); }
        .badge.cancelled, .badge.unpaid, .badge.danger { background: rgba(239,68,68,0.1); color: var(--danger); }
        .badge.partial { background: rgba(245,158,11,0.1); color: var(--warning); }
        
        .empty-state { text-align: center; padding: 50px 20px; color: var(--gray); }
        .empty-state i { font-size: 3rem; margin-bottom: 15px; color: var(--light-gray); }
        
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 12px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px 25px; border-bottom: 1px solid var(--light-gray); display: flex; justify-content: space-between; align-items: center; }
        .modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray); }
        .modal-body { padding: 25px; }
        .modal-footer { padding: 20px 25px; border-top: 1px solid var(--light-gray); display: flex; gap: 15px; justify-content: flex-end; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border: 1px solid var(--light-gray); border-radius: 8px; font-size: 1rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
        
        .cart-count { background: var(--secondary); color: white; font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; margin-left: 5px; }
        
        @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="../index.php" class="sidebar-logo">
            <i class="fas fa-clinic-medical"></i>
            <span>RxHub</span>
        </a>
        
        <div class="menu-section">Main Menu</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="<?php echo $section === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="dashboard.php?section=products" class="<?php echo $section === 'products' ? 'active' : ''; ?>"><i class="fas fa-pills"></i> Products</a></li>
            <li><a href="dashboard.php?section=orders" class="<?php echo $section === 'orders' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> My Orders</a></li>
        </ul>
        
        <div class="menu-section">Finance</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=payments" class="<?php echo $section === 'payments' ? 'active' : ''; ?>"><i class="fas fa-credit-card"></i> Payments</a></li>
            <li><a href="dashboard.php?section=invoices" class="<?php echo $section === 'invoices' ? 'active' : ''; ?>"><i class="fas fa-file-invoice"></i> Invoices</a></li>
        </ul>
        
        <div class="menu-section">Reports</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=reports" class="<?php echo $section === 'reports' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Sales Reports</a></li>
            <li><a href="dashboard.php?section=analytics" class="<?php echo $section === 'analytics' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
        
        <div class="menu-section">Account</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=profile" class="<?php echo $section === 'profile' ? 'active' : ''; ?>"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="dashboard.php?section=settings" class="<?php echo $section === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h1>Welcome, <?php echo $user_name; ?></h1>
            </div>
            <div class="topbar-right">
                <button class="btn btn-outline btn-sm" onclick="openCartModal()">
                    <i class="fas fa-shopping-cart"></i> Cart <span class="cart-count" id="cartCount">0</span>
                </button>
                <div class="user-dropdown">
                    <div class="user-avatar"><?php echo getInitial($user_name); ?></div>
                    <span><?php echo $facility_name ?: $user_name; ?></span>
                </div>
            </div>
        </div>
        
        <div class="content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card purple">
                    <div class="icon"><i class="fas fa-shopping-bag"></i></div>
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card orange">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <h3><?php echo $stats['pending_orders']; ?></h3>
                    <p>Pending Orders</p>
                </div>
                <div class="stat-card green">
                    <div class="icon"><i class="fas fa-naira-sign"></i></div>
                    <h3>₦<?php echo number_format($stats['total_spent'], 0); ?></h3>
                    <p>Total Spent</p>
                </div>
                <div class="stat-card blue">
                    <div class="icon"><i class="fas fa-pills"></i></div>
                    <h3><?php echo count($products); ?></h3>
                    <p>Products Available</p>
                </div>
            </div>
            
            <!-- Products Section -->
            <div class="section-card" id="products">
                <div class="section-header">
                    <h2><i class="fas fa-pills"></i> Available Products</h2>
                    <select id="categoryFilter" class="btn btn-outline btn-sm" onchange="filterProducts(this.value)">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo h($cat['name']); ?>"><?php echo h($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Products Available</h3>
                    <p>Check back later for available products.</p>
                </div>
                <?php else: ?>
                <div class="products-grid" id="productsGrid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card" data-category="<?php echo h($product['category_name'] ?? ''); ?>">
                        <div class="product-category"><?php echo h($product['category_name'] ?? 'General'); ?></div>
                        <div class="product-name"><?php echo h($product['name']); ?></div>
                        <div class="product-manufacturer"><?php echo h($product['manufacturer'] ?? ''); ?></div>
                        <div class="product-price">₦<?php echo number_format($product['selling_price'], 2); ?></div>
                        <div class="product-actions">
                            <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo h($product['name']); ?>', <?php echo $product['selling_price']; ?>)">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Recent Orders Section -->
            <div class="section-card" id="orders">
                <div class="section-header">
                    <h2><i class="fas fa-shopping-cart"></i> Recent Orders</h2>
                </div>
                
                <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>No Orders Yet</h3>
                    <p>Start ordering products to see your order history.</p>
                </div>
                <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?php echo h($order['order_number']); ?></strong></td>
                            <td><?php echo $order['item_count']; ?> items</td>
                            <td>₦<?php echo number_format($order['total'], 2); ?></td>
                            <td><span class="badge <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            <td><span class="badge <?php echo $order['payment_status']; ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-outline btn-sm" onclick="viewOrder('<?php echo $order['id']; ?>')">View</button>
                                <?php if ($order['payment_status'] !== 'paid'): ?>
                                <button class="btn btn-primary btn-sm" onclick="payOrder('<?php echo $order['id']; ?>', <?php echo $order['total']; ?>)">Pay</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Cart Modal -->
    <div class="modal-overlay" id="cartModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Shopping Cart</h3>
                <button class="modal-close" onclick="closeCartModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="cartItems"></div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--light-gray);">
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 600;">
                        <span>Total:</span>
                        <span id="cartTotal">₦0.00</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeCartModal()">Continue Shopping</button>
                <button class="btn btn-primary" onclick="checkout()">Proceed to Checkout</button>
            </div>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Make Payment</h3>
                <button class="modal-close" onclick="closePaymentModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Amount to Pay</label>
                    <input type="text" id="paymentAmount" readonly>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select id="paymentMethod">
                        <option value="transfer">Bank Transfer</option>
                        <option value="pos">POS</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Transaction Reference (Optional)</label>
                    <input type="text" id="transactionRef" placeholder="Enter transaction reference">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closePaymentModal()">Cancel</button>
                <button class="btn btn-primary" onclick="processPayment()">Confirm Payment</button>
            </div>
        </div>
    </div>
    
    <script>
        let cart = JSON.parse(localStorage.getItem('rxhub_cart') || '[]');
        updateCartDisplay();
        
        function addToCart(productId, name, price) {
            const existing = cart.find(item => item.id === productId);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({ id: productId, name: name, price: price, quantity: 1 });
            }
            localStorage.setItem('rxhub_cart', JSON.stringify(cart));
            updateCartDisplay();
            alert(name + ' added to cart!');
        }
        
        function updateCartDisplay() {
            document.getElementById('cartCount').textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            const cartItemsDiv = document.getElementById('cartItems');
            if (cart.length === 0) {
                cartItemsDiv.innerHTML = '<div class="empty-state"><i class="fas fa-shopping-cart"></i><p>Your cart is empty</p></div>';
            } else {
                cartItemsDiv.innerHTML = cart.map((item, index) => `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--light-gray);">
                        <div>
                            <div style="font-weight: 600;">${item.name}</div>
                            <div style="color: var(--gray); font-size: 0.9rem;">₦${item.price.toLocaleString()} x ${item.quantity}</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-weight: 600;">₦${(item.price * item.quantity).toLocaleString()}</span>
                            <button onclick="removeFromCart(${index})" style="background: none; border: none; color: var(--danger); cursor: pointer;"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `).join('');
            }
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('cartTotal').textContent = '₦' + total.toLocaleString();
        }
        
        function removeFromCart(index) {
            cart.splice(index, 1);
            localStorage.setItem('rxhub_cart', JSON.stringify(cart));
            updateCartDisplay();
        }
        
        function openCartModal() {
            document.getElementById('cartModal').classList.add('active');
        }
        
        function closeCartModal() {
            document.getElementById('cartModal').classList.remove('active');
        }
        
        function checkout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            fetch('../api/process_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ items: cart })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cart = [];
                    localStorage.setItem('rxhub_cart', '[]');
                    updateCartDisplay();
                    closeCartModal();
                    alert('Order placed successfully! Order #' + data.order_number);
                    location.reload();
                } else {
                    alert(data.message || 'Failed to place order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
        
        let currentOrderId = null;
        
        function payOrder(orderId, amount) {
            currentOrderId = orderId;
            document.getElementById('paymentAmount').value = '₦' + amount.toLocaleString();
            document.getElementById('paymentModal').classList.add('active');
        }
        
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('active');
            currentOrderId = null;
        }
        
        function processPayment() {
            const method = document.getElementById('paymentMethod').value;
            const reference = document.getElementById('transactionRef').value;
            
            fetch('../api/process_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    order_id: currentOrderId,
                    payment_method: method,
                    reference: reference
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closePaymentModal();
                    alert('Payment recorded successfully!');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to process payment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
        
        function filterProducts(category) {
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                if (!category || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        function viewOrder(orderId) {
            alert('View order details - Coming soon!');
        }
        
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
