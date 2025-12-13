<?php
/**
 * RxHub Admin Dashboard
 * Full administrative control panel with section-based navigation
 */

require_once dirname(__DIR__) . '/includes/init.php';

// Check if admin is logged in
if (!Session::isLoggedIn('admin')) {
    header('Location: login.php');
    exit();
}

$admin_id = Session::get('admin_id');
$admin_name = h(Session::get('admin_name'));
$admin_role = Session::get('admin_role') ?? 'admin';

// Get database connection
$db = Database::getInstance();
$pdo = $db->getConnection();

// Get current section from URL

// Sanitize section input
$allowed_sections = ['dashboard', 'users', 'investors', 'products', 'orders', 'investment_options', 'settings', 'stock', 'invoices'];
$section = isset($_GET['section']) && in_array($_GET['section'], $allowed_sections) ? $_GET['section'] : 'dashboard';

// Initialize stats
$stats = [
    'total_users' => 0,
    'total_investors' => 0,
    'total_orders' => 0,
    'total_revenue' => 0,
    'total_products' => 0,
    'pending_orders' => 0,
    'monthly_profit' => 0,
    'total_investments' => 0
];

$recent_orders = [];
$recent_users = [];

if ($pdo) {
    try {
        $stats['total_users'] = $db->fetchColumn("SELECT COUNT(*) FROM users");
        $stats['total_investors'] = $db->fetchColumn("SELECT COUNT(*) FROM investors");
        $stats['total_orders'] = $db->fetchColumn("SELECT COUNT(*) FROM orders");
        $stats['total_products'] = $db->fetchColumn("SELECT COUNT(*) FROM products WHERE is_active = 1");
        $stats['pending_orders'] = $db->fetchColumn("SELECT COUNT(*) FROM orders WHERE status IN ('pending', 'confirmed', 'processing')");
        $stats['total_revenue'] = $db->fetchColumn("SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid'");
        
        $current_month = date('Y-m');
        $stats['monthly_profit'] = $db->fetchColumn("SELECT COALESCE(SUM(profit), 0) FROM sales WHERE sales_cycle = ?", [$current_month]);
        $stats['total_investments'] = $db->fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM investor_investments WHERE status = 'confirmed'");
        
        $recent_orders = $db->fetchAll("SELECT o.*, u.full_name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
        $recent_users = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
        
    } catch (PDOException $e) {
        error_log("Admin Dashboard error: " . $e->getMessage());
    }
}


// Section-specific data loading (moved to section includes for maintainability)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RxHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
        <link rel="stylesheet" href="../assets/css/rxhub-theme.css">
    
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
        
        .btn { padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; border: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; border: 1px solid var(--light-gray); color: var(--dark); }
        .btn-outline:hover { background: var(--light); }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; }
        
        .quick-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 25px; }
        
        .chart-placeholder {
            height: 250px;
            background: linear-gradient(135deg, var(--light) 0%, var(--light-gray) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
        }
        
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 12px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px; border-bottom: 1px solid var(--light-gray); display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 1.2rem; }
        .modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray); }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 20px; border-top: 1px solid var(--light-gray); display: flex; gap: 10px; justify-content: flex-end; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--light-gray); border-radius: 6px; font-size: 0.9rem; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        .alert { padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert.success { background: rgba(34,197,94,0.1); color: var(--success); border: 1px solid var(--success); }
        .alert.error { background: rgba(239,68,68,0.1); color: var(--danger); border: 1px solid var(--danger); }
        
        .empty-state { text-align: center; padding: 60px 20px; color: var(--gray); }
        .empty-state i { font-size: 4rem; margin-bottom: 15px; color: var(--light-gray); }
        
        .settings-form { max-width: 800px; }
        .settings-group { background: var(--light); padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .settings-group h3 { margin-bottom: 15px; font-size: 1rem; color: var(--dark); display: flex; align-items: center; gap: 10px; }
        .settings-group h3 i { color: var(--primary); }
        
        @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .grid-2, .grid-3 { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="../index.php" class="sidebar-logo">
            <i class="fas fa-clinic-medical"></i>
            <span>RxHub</span>
            <span class="sidebar-badge">Admin</span>
        </a>
        
        <div class="menu-section">Overview</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="<?php echo $section === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="dashboard.php?section=analytics" class="<?php echo $section === 'analytics' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
        
        <div class="menu-section">User Management</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=users" class="<?php echo $section === 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Healthcare Users</a></li>
            <li><a href="dashboard.php?section=investors" class="<?php echo $section === 'investors' ? 'active' : ''; ?>"><i class="fas fa-hand-holding-usd"></i> Investors</a></li>
            <li><a href="dashboard.php?section=admins" class="<?php echo $section === 'admins' ? 'active' : ''; ?>"><i class="fas fa-user-shield"></i> Admin Users</a></li>
            <li><a href="dashboard.php?section=roles" class="<?php echo $section === 'roles' ? 'active' : ''; ?>"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
        </ul>
        
        <div class="menu-section">Products & Inventory</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=products" class="<?php echo $section === 'products' ? 'active' : ''; ?>"><i class="fas fa-pills"></i> Products</a></li>
            <li><a href="dashboard.php?section=categories" class="<?php echo $section === 'categories' ? 'active' : ''; ?>"><i class="fas fa-tags"></i> Categories</a></li>
            <li><a href="dashboard.php?section=stock" class="<?php echo $section === 'stock' ? 'active' : ''; ?>"><i class="fas fa-boxes"></i> Stock Management</a></li>
        </ul>
        
        <div class="menu-section">Orders & Sales</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=orders" class="<?php echo $section === 'orders' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="dashboard.php?section=invoices" class="<?php echo $section === 'invoices' ? 'active' : ''; ?>"><i class="fas fa-file-invoice"></i> Invoices</a></li>
            <li><a href="dashboard.php?section=payments" class="<?php echo $section === 'payments' ? 'active' : ''; ?>"><i class="fas fa-credit-card"></i> Payments</a></li>
        </ul>
        
        <div class="menu-section">Investments</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=investment_options" class="<?php echo $section === 'investment_options' ? 'active' : ''; ?>"><i class="fas fa-rocket"></i> Investment Options</a></li>
            <li><a href="dashboard.php?section=investments" class="<?php echo $section === 'investments' ? 'active' : ''; ?>"><i class="fas fa-chart-pie"></i> Investor Portfolios</a></li>
            <li><a href="dashboard.php?section=roi" class="<?php echo $section === 'roi' ? 'active' : ''; ?>"><i class="fas fa-percentage"></i> ROI Management</a></li>
        </ul>
        
        <div class="menu-section">Reports</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=sales_report" class="<?php echo $section === 'sales_report' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Sales Reports</a></li>
            <li><a href="dashboard.php?section=profit_report" class="<?php echo $section === 'profit_report' ? 'active' : ''; ?>"><i class="fas fa-coins"></i> Profit Analysis</a></li>
            <li><a href="dashboard.php?section=export" class="<?php echo $section === 'export' ? 'active' : ''; ?>"><i class="fas fa-download"></i> Export Data</a></li>
        </ul>
        
        <div class="menu-section">System</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=settings" class="<?php echo $section === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> System Settings</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left"><h1><?php echo ucwords(str_replace('_', ' ', $section)); ?></h1></div>
            <div class="topbar-right">
                <button class="topbar-btn" onclick="openModal('bulkUploadModal')"><i class="fas fa-upload"></i> Bulk Upload</button>
                <button class="topbar-btn" onclick="exportData()"><i class="fas fa-download"></i> Export</button>
                <div class="user-dropdown">
                    <div class="user-avatar"><?php echo getInitial($admin_name); ?></div>
                    <span><?php echo $admin_name; ?></span>
                </div>
            </div>
        </div>
        
        <div class="content">
            <?php
            // Include section-specific content
            switch ($section) {
                case 'settings':
                    include 'sections/settings.php';
                    break;
                case 'users':
                    include 'sections/users.php';
                    break;
                case 'investors':
                    include 'sections/investors.php';
                    break;
                case 'products':
                    include 'sections/products.php';
                    break;
                case 'orders':
                    include 'sections/orders.php';
                    break;
                case 'investment_options':
                    include 'sections/investment_options.php';
                    break;
                case 'stock':
                    include 'sections/stock.php';
                    break;
                case 'users':
                    include 'sections/users.php';
                    break;
                case 'investors':
                    include 'sections/investors.php';
                    break;
                case 'settings':
                    include 'sections/settings.php';
                    break;
                case 'invoices':
                    include 'sections/invoices.php';
                    break;
                default:
                    include 'sections/overview.php';
                    break;
            }
            ?>
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
            
            fetch('../api/bulk_upload.php', {
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
            window.location.href = '../api/export.php?type=all';
        }
    </script>
</body>
</html>
