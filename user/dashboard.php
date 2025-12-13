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

// Sanitize section input
$allowed_sections = ['dashboard', 'products', 'orders', 'payments', 'invoices', 'reports', 'analytics', 'profile', 'settings'];
$section = isset($_GET['section']) && in_array($_GET['section'], $allowed_sections) ? $_GET['section'] : 'dashboard';

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
    <link rel="stylesheet" href="../assets/css/rxhub-theme.css">
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
            <?php
            // Section router
            switch ($section) {
                case 'products':
                    include __DIR__ . '/sections/products.php';
                    break;
                case 'orders':
                    include __DIR__ . '/sections/orders.php';
                    break;
                case 'invoices':
                    include __DIR__ . '/sections/invoices.php';
                    break;
                // Add more cases for other sections as needed
                case 'dashboard':
                default:
                    // Show dashboard stats only
            ?>
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
            <?php
                    break;
            }
            ?>
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
