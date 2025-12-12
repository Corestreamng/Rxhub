<?php
/**
 * RxHub Investor Dashboard
 * Display investment opportunities and investor portfolio
 */

require_once dirname(__DIR__) . '/includes/init.php';

// Check if investor is logged in
if (!Session::isLoggedIn('investor')) {
    header('Location: login.php');
    exit();
}

$investor_id = Session::get('investor_id');
$investor_name = h(Session::get('investor_name'));

// Get database connection
$db = Database::getInstance();

// Get current section
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Fetch data
$investment_options = [];
$investor_investments = [];
$stats = [
    'total_invested' => 0,
    'active_investments' => 0,
    'pending_returns' => 0,
    'total_returns' => 0
];

try {
    // Get active investment options
    $investment_options = $db->fetchAll("SELECT * FROM investment_options WHERE is_active = 1 AND status = 'open' ORDER BY created_at DESC");
    
    // Get investor's current investments
    $investor_investments = $db->fetchAll("
        SELECT ii.*, io.title, io.expected_roi, io.duration_months, io.risk_level, io.category
        FROM investor_investments ii
        JOIN investment_options io ON ii.investment_option_id = io.id
        WHERE ii.investor_id = ?
        ORDER BY ii.investment_date DESC
    ", [$investor_id]);
    
    // Calculate stats
    foreach ($investor_investments as $inv) {
        if ($inv['status'] === 'confirmed' || $inv['status'] === 'completed') {
            $stats['total_invested'] += $inv['amount'];
            $stats['active_investments']++;
        }
        if ($inv['status'] === 'pending') {
            $stats['pending_returns'] += $inv['amount'];
        }
    }
    
    // Calculate estimated returns (simplified - 20% average)
    $stats['total_returns'] = $stats['total_invested'] * 0.20;
    
} catch (Exception $e) {
    error_log("Investor Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Dashboard - RxHub</title>
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
        .sidebar-badge { background: var(--primary); color: white; font-size: 0.65rem; padding: 3px 8px; border-radius: 10px; margin-left: auto; }
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
        
        /* Stats Cards - 4 Colored Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
        .stat-card.purple::before { background: linear-gradient(to right, var(--primary), #c026d3); }
        .stat-card.green::before { background: linear-gradient(to right, var(--success), #4ade80); }
        .stat-card.orange::before { background: linear-gradient(to right, var(--warning), #fbbf24); }
        .stat-card.blue::before { background: linear-gradient(to right, var(--info), #60a5fa); }
        
        .stat-card .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px; }
        .stat-card.purple .stat-icon { background: rgba(153,0,204,0.1); color: var(--primary); }
        .stat-card.green .stat-icon { background: rgba(34,197,94,0.1); color: var(--success); }
        .stat-card.orange .stat-icon { background: rgba(245,158,11,0.1); color: var(--warning); }
        .stat-card.blue .stat-icon { background: rgba(59,130,246,0.1); color: var(--info); }
        
        .stat-card h3 { font-size: 2rem; margin-bottom: 5px; font-weight: 700; }
        .stat-card.purple h3 { color: var(--primary); }
        .stat-card.green h3 { color: var(--success); }
        .stat-card.orange h3 { color: var(--warning); }
        .stat-card.blue h3 { color: var(--info); }
        .stat-card p { color: var(--gray); font-size: 0.9rem; }
        
        .stat-card .trend { position: absolute; top: 20px; right: 20px; font-size: 0.85rem; display: flex; align-items: center; gap: 5px; }
        .stat-card .trend.up { color: var(--success); }
        .stat-card .trend.down { color: var(--danger); }
        
        /* Section Cards */
        .section-card { background: white; border-radius: 16px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--light-gray); }
        .section-header h2 { font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
        .section-header h2 i { color: var(--primary); }
        
        /* Investment Cards */
        .investment-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
        
        .investment-card {
            border: 1px solid var(--light-gray);
            border-radius: 16px;
            padding: 25px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .investment-card:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 30px rgba(153, 0, 204, 0.15);
            transform: translateY(-5px);
        }
        
        .investment-card .category-badge { position: absolute; top: 15px; right: 15px; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .category-badge.equity { background: rgba(153,0,204,0.1); color: var(--primary); }
        .category-badge.debt { background: rgba(0,102,102,0.1); color: var(--accent); }
        .category-badge.convertible { background: rgba(245,158,11,0.1); color: var(--warning); }
        .category-badge.profit_sharing { background: rgba(34,197,94,0.1); color: var(--success); }
        
        .investment-card h3 { font-size: 1.2rem; margin-bottom: 10px; padding-right: 100px; }
        .investment-card .description { color: var(--gray); font-size: 0.9rem; margin-bottom: 20px; line-height: 1.6; }
        
        .investment-details { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
        .detail-item { display: flex; flex-direction: column; }
        .detail-label { font-size: 0.75rem; color: var(--gray); text-transform: uppercase; margin-bottom: 3px; }
        .detail-value { font-weight: 600; color: var(--dark); }
        
        .risk-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
        .risk-badge.low { background: rgba(34,197,94,0.1); color: var(--success); }
        .risk-badge.medium { background: rgba(245,158,11,0.1); color: var(--warning); }
        .risk-badge.high { background: rgba(239,68,68,0.1); color: var(--danger); }
        
        .progress-bar { background: var(--light-gray); height: 10px; border-radius: 5px; margin-bottom: 10px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(to right, var(--primary), var(--accent)); border-radius: 5px; transition: width 0.5s ease; }
        .progress-text { display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--gray); margin-bottom: 20px; }
        
        .invest-btn {
            width: 100%;
            background: linear-gradient(to right, var(--primary), var(--accent));
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .invest-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(153,0,204,0.3); }
        
        /* Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid var(--light-gray); }
        .data-table th { background: var(--light); font-weight: 600; color: var(--dark); font-size: 0.9rem; }
        .data-table tr:hover { background: var(--light); }
        
        .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
        .badge.pending { background: rgba(245,158,11,0.1); color: var(--warning); }
        .badge.confirmed, .badge.success { background: rgba(34,197,94,0.1); color: var(--success); }
        .badge.completed, .badge.primary { background: rgba(153,0,204,0.1); color: var(--primary); }
        .badge.cancelled, .badge.danger { background: rgba(239,68,68,0.1); color: var(--danger); }
        
        .empty-state { text-align: center; padding: 60px 20px; color: var(--gray); }
        .empty-state i { font-size: 4rem; margin-bottom: 20px; color: var(--light-gray); }
        .empty-state h3 { margin-bottom: 10px; color: var(--dark); }
        
        /* Modal */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 16px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .modal-header { padding: 20px 25px; border-bottom: 1px solid var(--light-gray); display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 1.3rem; }
        .modal-close { background: none; border: none; font-size: 28px; cursor: pointer; color: var(--gray); }
        .modal-close:hover { color: var(--dark); }
        .modal-body { padding: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--dark); }
        .form-group input, .form-group select { width: 100%; padding: 14px 15px; border: 2px solid var(--light-gray); border-radius: 10px; font-size: 1rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
        .modal-footer { padding: 20px 25px; border-top: 1px solid var(--light-gray); display: flex; gap: 15px; justify-content: flex-end; }
        .btn { padding: 12px 24px; border-radius: 10px; font-weight: 500; cursor: pointer; transition: all 0.3s; font-size: 1rem; border: none; }
        .btn-secondary { background: var(--light); color: var(--dark); border: 1px solid var(--light-gray); }
        .btn-secondary:hover { background: var(--light-gray); }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        
        .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: none; }
        .alert.success { background: rgba(34,197,94,0.1); color: var(--success); border: 1px solid var(--success); }
        .alert.error { background: rgba(239,68,68,0.1); color: var(--danger); border: 1px solid var(--danger); }
        
        @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .stats-grid { grid-template-columns: 1fr; } .investment-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="../index.php" class="sidebar-logo">
            <i class="fas fa-clinic-medical"></i>
            <span>RxHub</span>
            <span class="sidebar-badge">Investor</span>
        </a>
        
        <div class="menu-section">Main Menu</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="<?php echo $section === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="dashboard.php?section=opportunities" class="<?php echo $section === 'opportunities' ? 'active' : ''; ?>"><i class="fas fa-rocket"></i> Opportunities</a></li>
            <li><a href="dashboard.php?section=portfolio" class="<?php echo $section === 'portfolio' ? 'active' : ''; ?>"><i class="fas fa-briefcase"></i> My Portfolio</a></li>
        </ul>
        
        <div class="menu-section">Finance</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=returns" class="<?php echo $section === 'returns' ? 'active' : ''; ?>"><i class="fas fa-chart-pie"></i> Returns</a></li>
            <li><a href="dashboard.php?section=statements" class="<?php echo $section === 'statements' ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i> Statements</a></li>
            <li><a href="dashboard.php?section=history" class="<?php echo $section === 'history' ? 'active' : ''; ?>"><i class="fas fa-history"></i> Transaction History</a></li>
        </ul>
        
        <div class="menu-section">Support</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php?section=support" class="<?php echo $section === 'support' ? 'active' : ''; ?>"><i class="fas fa-headset"></i> Contact Support</a></li>
            <li><a href="dashboard.php?section=faq" class="<?php echo $section === 'faq' ? 'active' : ''; ?>"><i class="fas fa-question-circle"></i> FAQs</a></li>
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
            <div class="topbar-left"><h1><?php echo ucwords(str_replace('_', ' ', $section)); ?></h1></div>
            <div class="topbar-right">
                <div class="user-dropdown">
                    <div class="user-avatar"><?php echo getInitial($investor_name); ?></div>
                    <span>Welcome, <?php echo $investor_name; ?></span>
                </div>
            </div>
        </div>
        
        <div class="content">
            <div class="alert success" id="successAlert"></div>
            <div class="alert error" id="errorAlert"></div>
            
            <!-- Stats Cards - 4 Different Colors -->
            <div class="stats-grid">
                <div class="stat-card purple">
                    <div class="trend up"><i class="fas fa-arrow-up"></i> 12%</div>
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                    <h3>$<?php echo number_format($stats['total_invested'], 0); ?></h3>
                    <p>Total Invested</p>
                </div>
                <div class="stat-card green">
                    <div class="trend up"><i class="fas fa-arrow-up"></i> 8%</div>
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>$<?php echo number_format($stats['total_returns'], 0); ?></h3>
                    <p>Estimated Returns</p>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <h3><?php echo $stats['active_investments']; ?></h3>
                    <p>Active Investments</p>
                </div>
                <div class="stat-card blue">
                    <div class="stat-icon"><i class="fas fa-bullseye"></i></div>
                    <h3><?php echo count($investment_options); ?></h3>
                    <p>Open Opportunities</p>
                </div>
            </div>
            
            <!-- Investment Opportunities -->
            <div class="section-card">
                <div class="section-header"><h2><i class="fas fa-rocket"></i> Investment Opportunities</h2></div>
                <?php if (empty($investment_options)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Investment Opportunities Available</h3>
                    <p>Check back later for new investment opportunities.</p>
                </div>
                <?php else: ?>
                <div class="investment-grid">
                    <?php foreach ($investment_options as $option): 
                        $progress = $option['target_amount'] > 0 ? ($option['current_amount'] / $option['target_amount']) * 100 : 0;
                        $remaining = $option['target_amount'] - $option['current_amount'];
                    ?>
                    <div class="investment-card">
                        <span class="category-badge <?php echo h($option['category']); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', h($option['category']))); ?>
                        </span>
                        <h3><?php echo h($option['title']); ?></h3>
                        <p class="description"><?php echo h($option['description']); ?></p>
                        <div class="investment-details">
                            <div class="detail-item">
                                <span class="detail-label">Min Investment</span>
                                <span class="detail-value">$<?php echo number_format($option['min_investment'], 0); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Expected ROI</span>
                                <span class="detail-value"><?php echo h($option['expected_roi']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Duration</span>
                                <span class="detail-value"><?php echo $option['duration_months']; ?> months</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Risk Level</span>
                                <span class="risk-badge <?php echo h($option['risk_level']); ?>">
                                    <?php echo ucfirst(h($option['risk_level'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="progress-bar"><div class="progress-fill" style="width: <?php echo min($progress, 100); ?>%"></div></div>
                        <div class="progress-text">
                            <span><?php echo number_format($progress, 1); ?>% Funded</span>
                            <span>$<?php echo number_format($remaining, 0); ?> remaining</span>
                        </div>
                        <button class="invest-btn" onclick="openInvestModal(<?php echo $option['id']; ?>, '<?php echo h($option['title']); ?>', <?php echo $option['min_investment']; ?>, <?php echo $option['max_investment'] ?? $option['target_amount']; ?>)">
                            <i class="fas fa-hand-holding-usd"></i> Invest Now
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- My Investments -->
            <div class="section-card">
                <div class="section-header"><h2><i class="fas fa-briefcase"></i> My Portfolio</h2></div>
                <?php if (empty($investor_investments)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Investments Yet</h3>
                    <p>Start investing in the opportunities above to build your portfolio.</p>
                </div>
                <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr><th>Investment</th><th>Category</th><th>Amount</th><th>Expected ROI</th><th>Duration</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($investor_investments as $investment): ?>
                        <tr>
                            <td><strong><?php echo h($investment['title']); ?></strong></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', h($investment['category']))); ?></td>
                            <td>$<?php echo number_format($investment['amount'], 2); ?></td>
                            <td><?php echo h($investment['expected_roi']); ?></td>
                            <td><?php echo $investment['duration_months']; ?> months</td>
                            <td><span class="badge <?php echo h($investment['status']); ?>"><?php echo ucfirst(h($investment['status'])); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($investment['investment_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Investment Modal -->
    <div class="modal-overlay" id="investModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Make an Investment</h3>
                <button class="modal-close" onclick="closeInvestModal()">&times;</button>
            </div>
            <form id="investForm">
                <div class="modal-body">
                    <input type="hidden" name="investment_option_id" id="modalInvestmentId">
                    <div class="form-group">
                        <label>Investment Opportunity</label>
                        <input type="text" id="modalInvestmentTitle" readonly style="background: var(--light);">
                    </div>
                    <div class="form-group">
                        <label for="investAmount">Investment Amount ($)</label>
                        <input type="number" name="amount" id="investAmount" required min="0" step="0.01">
                        <small style="color: var(--gray); display: block; margin-top: 8px;">
                            Min: $<span id="minAmount">0</span> - Max: $<span id="maxAmount">0</span>
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="investNotes">Notes (Optional)</label>
                        <input type="text" name="notes" id="investNotes" placeholder="Any additional information">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeInvestModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Investment</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openInvestModal(id, title, min, max) {
            document.getElementById('modalInvestmentId').value = id;
            document.getElementById('modalInvestmentTitle').value = title;
            document.getElementById('investAmount').min = min;
            document.getElementById('investAmount').max = max;
            document.getElementById('investAmount').value = min;
            document.getElementById('minAmount').textContent = min.toLocaleString();
            document.getElementById('maxAmount').textContent = max.toLocaleString();
            document.getElementById('investModal').classList.add('active');
        }
        
        function closeInvestModal() {
            document.getElementById('investModal').classList.remove('active');
            document.getElementById('investForm').reset();
        }
        
        document.getElementById('investModal').addEventListener('click', function(e) {
            if (e.target === this) closeInvestModal();
        });
        
        document.getElementById('investForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../api/process_investment.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                closeInvestModal();
                if (data.success) {
                    document.getElementById('successAlert').textContent = data.message;
                    document.getElementById('successAlert').style.display = 'block';
                    setTimeout(() => { window.location.reload(); }, 2000);
                } else {
                    document.getElementById('errorAlert').textContent = data.message;
                    document.getElementById('errorAlert').style.display = 'block';
                    setTimeout(() => { document.getElementById('errorAlert').style.display = 'none'; }, 5000);
                }
            })
            .catch(error => {
                document.getElementById('errorAlert').textContent = 'An error occurred. Please try again.';
                document.getElementById('errorAlert').style.display = 'block';
            });
        });
    </script>
</body>
</html>
