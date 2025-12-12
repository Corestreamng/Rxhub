<?php
/**
 * Redirect to new admin dashboard location
 */
header('Location: admin/dashboard.php');
exit();
?>

    'total_orders' => 0,
    'total_revenue' => 0,
    'total_products' => 0,
    'pending_orders' => 0,
    'monthly_profit' => 0,
    'total_investments' => 0
];

// Get data for dashboard
$recent_orders = [];
$recent_users = [];
$sales_data = [];

if ($pdo) {
    try {
        // Get counts
        $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['total_investors'] = $pdo->query("SELECT COUNT(*) FROM investors")->fetchColumn();
        $stats['total_orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $stats['total_products'] = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
        $stats['pending_orders'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending', 'confirmed', 'processing')")->fetchColumn();
        $stats['total_revenue'] = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
        
        // Use prepared statement for monthly profit to prevent SQL injection
        $current_month = date('Y-m');
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(profit), 0) FROM sales WHERE sales_cycle = ?");
        $stmt->execute([$current_month]);
        $stats['monthly_profit'] = $stmt->fetchColumn();
        
        $stats['total_investments'] = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM investor_investments WHERE status = 'confirmed'")->fetchColumn();
        
        // Recent orders
        $stmt = $pdo->query("SELECT o.*, u.full_name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
        $recent_orders = $stmt->fetchAll();
        
        // Recent users
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
        $recent_users = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Admin Dashboard error: " . $e->getMessage());
    }
}

// Get current page section
$section = $_GET['section'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RxHub</title>
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
            --info: #3b82f6;
            --sidebar-width: 260px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f1f5f9; color: var(--dark); }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
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
        .sidebar-badge { background: var(--danger); color: white; font-size: 0.65rem; padding: 3px 8px; border-radius: 10px; margin-left: auto; }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 3px; }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-size: 0.95rem;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--primary);
        }
        
        .sidebar-menu a i { width: 20px; text-align: center; }
        
        .menu-section {
            padding: 15px 20px 10px;
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        
        /* Top Bar */
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
        
        .topbar-left h1 { font-size: 1.4rem; color: var(--dark); }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        
        .topbar-btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .topbar-btn:hover { background: var(--light); }
        .topbar-btn.primary { background: var(--primary); color: white; border-color: var(--primary); }
        .topbar-btn.primary:hover { background: var(--primary-dark); }
        
        .user-dropdown { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        
        .content { padding: 25px 30px; }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .stat-icon.purple { background: rgba(153,0,204,0.1); color: var(--primary); }
        .stat-icon.green { background: rgba(34,197,94,0.1); color: var(--success); }
        .stat-icon.orange { background: rgba(245,158,11,0.1); color: var(--warning); }
        .stat-icon.blue { background: rgba(59,130,246,0.1); color: var(--info); }
        .stat-icon.red { background: rgba(239,68,68,0.1); color: var(--danger); }
        .stat-icon.teal { background: rgba(0,102,102,0.1); color: var(--accent); }
        
        .stat-info h3 { font-size: 1.5rem; font-weight: 700; margin-bottom: 2px; }
        .stat-info p { color: var(--gray); font-size: 0.85rem; }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        
        .card-header {
            padding: 20px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 { font-size: 1.1rem; display: flex; align-items: center; gap: 10px; }
        .card-header h2 i { color: var(--primary); }
        .card-body { padding: 20px; }
        
        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--light-gray); font-size: 0.9rem; }
        .data-table th { background: var(--light); font-weight: 600; color: var(--dark); }
        .data-table tr:hover { background: var(--light); }
        
        .badge { display: inline-block; padding: 4px 10px; border-radius: 15px; font-size: 0.75rem; font-weight: 500; }
        .badge.success { background: rgba(34,197,94,0.1); color: var(--success); }
        .badge.warning { background: rgba(245,158,11,0.1); color: var(--warning); }
        .badge.danger { background: rgba(239,68,68,0.1); color: var(--danger); }
        .badge.info { background: rgba(59,130,246,0.1); color: var(--info); }
        .badge.primary { background: rgba(153,0,204,0.1); color: var(--primary); }
        
        /* Buttons */
        .btn { padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; border: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; border: 1px solid var(--light-gray); color: var(--dark); }
        .btn-outline:hover { background: var(--light); }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        
        /* Grid Layout */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; }
        
        /* Quick Actions */
        .quick-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 25px; }
        
        /* Charts placeholder */
        .chart-placeholder {
            height: 250px;
            background: linear-gradient(135deg, var(--light) 0%, var(--light-gray) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
        }
        
        /* Modal */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 12px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px; border-bottom: 1px solid var(--light-gray); display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 1.2rem; }
        .modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray); }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 20px; border-top: 1px solid var(--light-gray); display: flex; gap: 10px; justify-content: flex-end; }
        
        /* Forms */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--light-gray); border-radius: 6px; font-size: 0.9rem; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        /* Alerts */
        .alert { padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert.success { background: rgba(34,197,94,0.1); color: var(--success); border: 1px solid var(--success); }
        .alert.error { background: rgba(239,68,68,0.1); color: var(--danger); border: 1px solid var(--danger); }
        
        /* Responsive */
        @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .grid-2, .grid-3 { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="index.html" class="sidebar-logo">
            <i class="fas fa-clinic-medical"></i>
            <span>RxHub</span>
            <span class="sidebar-badge">Admin</span>
        </a>
        
        <div class="menu-section">Overview</div>
        <ul class="sidebar-menu">
            <li><a href="?section=dashboard" class="<?php echo $section === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="?section=analytics"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
        
        <div class="menu-section">User Management</div>
        <ul class="sidebar-menu">
            <li><a href="?section=users" class="<?php echo $section === 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Healthcare Users</a></li>
            <li><a href="?section=investors" class="<?php echo $section === 'investors' ? 'active' : ''; ?>"><i class="fas fa-hand-holding-usd"></i> Investors</a></li>
            <li><a href="?section=admins"><i class="fas fa-user-shield"></i> Admin Users</a></li>
            <li><a href="?section=roles"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
        </ul>
        
        <div class="menu-section">Products & Inventory</div>
        <ul class="sidebar-menu">
            <li><a href="?section=products" class="<?php echo $section === 'products' ? 'active' : ''; ?>"><i class="fas fa-pills"></i> Products</a></li>
            <li><a href="?section=categories"><i class="fas fa-tags"></i> Categories</a></li>
            <li><a href="?section=stock"><i class="fas fa-boxes"></i> Stock Management</a></li>
        </ul>
        
        <div class="menu-section">Orders & Sales</div>
        <ul class="sidebar-menu">
            <li><a href="?section=orders" class="<?php echo $section === 'orders' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="?section=invoices"><i class="fas fa-file-invoice"></i> Invoices</a></li>
            <li><a href="?section=payments"><i class="fas fa-credit-card"></i> Payments</a></li>
        </ul>
        
        <div class="menu-section">Investments</div>
        <ul class="sidebar-menu">
            <li><a href="?section=investment_options"><i class="fas fa-rocket"></i> Investment Options</a></li>
            <li><a href="?section=investments"><i class="fas fa-chart-pie"></i> Investor Portfolios</a></li>
            <li><a href="?section=roi"><i class="fas fa-percentage"></i> ROI Management</a></li>
        </ul>
        
        <div class="menu-section">Reports</div>
        <ul class="sidebar-menu">
            <li><a href="?section=sales_report"><i class="fas fa-chart-bar"></i> Sales Reports</a></li>
            <li><a href="?section=profit_report"><i class="fas fa-coins"></i> Profit Analysis</a></li>
            <li><a href="?section=export"><i class="fas fa-download"></i> Export Data</a></li>
        </ul>
        
        <div class="menu-section">Settings</div>
        <ul class="sidebar-menu">
            <li><a href="?section=settings"><i class="fas fa-cog"></i> System Settings</a></li>
            <li><a href="logout.php?redirect=admin_login.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left"><h1>Admin Dashboard</h1></div>
            <div class="topbar-right">
                <button class="topbar-btn" onclick="openModal('bulkUploadModal')"><i class="fas fa-upload"></i> Bulk Upload</button>
                <button class="topbar-btn" onclick="exportData()"><i class="fas fa-download"></i> Export</button>
                <div class="user-dropdown">
                    <div class="user-avatar"><?php echo strtoupper(substr($admin_name, 0, 1)); ?></div>
                    <span><?php echo $admin_name; ?></span>
                </div>
            </div>
        </div>
        
        <div class="content">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="btn btn-primary" onclick="openModal('addUserModal')"><i class="fas fa-user-plus"></i> Add User</button>
                <button class="btn btn-primary" onclick="openModal('addProductModal')"><i class="fas fa-plus"></i> Add Product</button>
                <button class="btn btn-primary" onclick="openModal('addInvestmentModal')"><i class="fas fa-chart-line"></i> Create Investment</button>
                <button class="btn btn-outline" onclick="openModal('createInvoiceModal')"><i class="fas fa-file-invoice"></i> Create Invoice</button>
            </div>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['total_users']); ?></h3>
                        <p>Healthcare Users</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['total_investors']); ?></h3>
                        <p>Investors</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['total_orders']); ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-naira-sign"></i></div>
                    <div class="stat-info">
                        <h3>₦<?php echo number_format($stats['total_revenue'], 0); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon teal"><i class="fas fa-pills"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['total_products']); ?></h3>
                        <p>Products</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['pending_orders']); ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-coins"></i></div>
                    <div class="stat-info">
                        <h3>₦<?php echo number_format($stats['monthly_profit'], 0); ?></h3>
                        <p>Monthly Profit</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-chart-pie"></i></div>
                    <div class="stat-info">
                        <h3>$<?php echo number_format($stats['total_investments'], 0); ?></h3>
                        <p>Total Investments</p>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Grid -->
            <div class="grid-2">
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-shopping-cart"></i> Recent Orders</h2>
                        <a href="?section=orders" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
                            <thead>
                                <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_orders)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--gray);">No orders yet</td></tr>
                                <?php else: ?>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>₦<?php echo number_format($order['total'], 0); ?></td>
                                    <td><span class="badge <?php echo $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info'); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Recent Users -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-users"></i> Recent Users</h2>
                        <a href="?section=users" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
                            <thead>
                                <tr><th>Name</th><th>Facility</th><th>Type</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_users)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--gray);">No users yet</td></tr>
                                <?php else: ?>
                                <?php foreach ($recent_users as $user): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['facility_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge primary"><?php echo ucfirst($user['facility_type'] ?? 'N/A'); ?></span></td>
                                    <td><?php echo date('M d', strtotime($user['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sales Chart -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Sales Overview</h2>
                    <select class="btn btn-outline btn-sm">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>Last 3 Months</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-placeholder">
                        <i class="fas fa-chart-area" style="font-size: 48px; margin-right: 15px;"></i>
                        Sales chart will be displayed here (integrate Chart.js)
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add User Modal -->
    <div class="modal-overlay" id="addUserModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Add New User</h3>
                <button class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone">
                        </div>
                        <div class="form-group">
                            <label>User Type *</label>
                            <select name="user_type" required>
                                <option value="user">Healthcare Provider</option>
                                <option value="investor">Investor</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Facility Name</label>
                            <input type="text" name="facility_name">
                        </div>
                        <div class="form-group">
                            <label>Facility Type</label>
                            <select name="facility_type">
                                <option value="pharmacy">Pharmacy</option>
                                <option value="hospital">Hospital</option>
                                <option value="clinic">Clinic</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addUserModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Product Modal -->
    <div class="modal-overlay" id="addProductModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Add New Product</h3>
                <button class="modal-close" onclick="closeModal('addProductModal')">&times;</button>
            </div>
            <form id="addProductForm">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>SKU *</label>
                            <input type="text" name="sku" required>
                        </div>
                        <div class="form-group">
                            <label>Product Name *</label>
                            <input type="text" name="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Manufacturer</label>
                            <input type="text" name="manufacturer">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Purchase Price *</label>
                            <input type="number" name="purchase_price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Selling Price *</label>
                            <input type="number" name="selling_price" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addProductModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Investment Modal -->
    <div class="modal-overlay" id="addInvestmentModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Create Investment Option</h3>
                <button class="modal-close" onclick="closeModal('addInvestmentModal')">&times;</button>
            </div>
            <form id="addInvestmentForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" required>
                                <option value="equity">Equity</option>
                                <option value="debt">Debt</option>
                                <option value="convertible">Convertible</option>
                                <option value="profit_sharing">Profit Sharing</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Risk Level *</label>
                            <select name="risk_level" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Min Investment ($) *</label>
                            <input type="number" name="min_investment" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Max Investment ($)</label>
                            <input type="number" name="max_investment" step="0.01">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Target Amount ($) *</label>
                            <input type="number" name="target_amount" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Expected ROI *</label>
                            <input type="text" name="expected_roi" placeholder="e.g., 15-20% annually" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Duration (months) *</label>
                        <input type="number" name="duration_months" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addInvestmentModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Investment</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bulk Upload Modal -->
    <div class="modal-overlay" id="bulkUploadModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Bulk Upload</h3>
                <button class="modal-close" onclick="closeModal('bulkUploadModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Upload Type</label>
                    <select id="uploadType">
                        <option value="users">Users</option>
                        <option value="products">Products</option>
                        <option value="stock">Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Download Template</label>
                    <button class="btn btn-outline" onclick="downloadTemplate()"><i class="fas fa-download"></i> Download CSV Template</button>
                </div>
                <div class="form-group">
                    <label>Upload File (CSV)</label>
                    <input type="file" id="bulkFile" accept=".csv">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('bulkUploadModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="processBulkUpload()">Upload</button>
            </div>
        </div>
    </div>
    
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }
        
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }
        
        // Close modal on outside click
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('active');
            });
        });
        
        function downloadTemplate() {
            const type = document.getElementById('uploadType').value;
            let headers = '';
            if (type === 'users') {
                headers = 'full_name,email,phone,facility_name,facility_type,password';
            } else if (type === 'products') {
                headers = 'sku,name,description,category_id,manufacturer,purchase_price,selling_price';
            } else if (type === 'stock') {
                headers = 'product_id,quantity,batch_number,expiry_date,purchase_price';
            }
            
            const blob = new Blob([headers], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = type + '_template.csv';
            a.click();
        }
        
        function processBulkUpload() {
            const file = document.getElementById('bulkFile').files[0];
            if (!file) {
                alert('Please select a file');
                return;
            }
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', document.getElementById('uploadType').value);
            
            fetch('admin_bulk_upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Upload successful! ' + data.count + ' records imported.');
                    closeModal('bulkUploadModal');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
        
        function exportData() {
            window.location.href = 'admin_export.php?type=all';
        }
        
        // Form submissions
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('admin_api.php?action=create_user', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User created successfully!');
                    closeModal('addUserModal');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
        
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('admin_api.php?action=create_product', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product created successfully!');
                    closeModal('addProductModal');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
        
        document.getElementById('addInvestmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('admin_api.php?action=create_investment', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Investment option created successfully!');
                    closeModal('addInvestmentModal');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
    </script>
</body>
</html>
