<!-- Admin Dashboard Overview Section -->
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
            <a href="dashboard.php?section=orders" class="btn btn-sm btn-outline">View All</a>
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
                        <td><strong><?php echo h($order['order_number']); ?></strong></td>
                        <td><?php echo h($order['customer_name'] ?? 'N/A'); ?></td>
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
            <a href="dashboard.php?section=users" class="btn btn-sm btn-outline">View All</a>
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
                        <td><strong><?php echo h($user['full_name']); ?></strong></td>
                        <td><?php echo h($user['facility_name'] ?? 'N/A'); ?></td>
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

<!-- Add User Modal -->
<div class="modal-overlay" id="addUserModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add New User</h3>
            <button class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
        </div>
        <form id="addUserForm" action="../api/admin/users.php" method="POST">
            <input type="hidden" name="action" value="create">
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
        <form id="addProductForm" action="../api/admin/products.php" method="POST">
            <input type="hidden" name="action" value="create">
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
        <form id="addInvestmentForm" action="../api/admin/investments.php" method="POST">
            <input type="hidden" name="action" value="create">
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

<script>
// Form submissions
document.getElementById('addUserForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../api/admin/users.php', {
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

document.getElementById('addProductForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../api/admin/products.php', {
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

document.getElementById('addInvestmentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../api/admin/investments.php', {
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
