<?php
// Reports Section for User Dashboard
// Display sales reports and analytics for the facility

if (!isset($db, $user_id)) exit('Restricted');

// Get date range from query params or default to current month
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

try {
    // Get order statistics
    $total_orders = $db->fetchColumn("
        SELECT COUNT(*) FROM orders 
        WHERE user_id = ? AND created_at BETWEEN ? AND ?
    ", [$user_id, $start_date, $end_date]);
    
    $total_spent = $db->fetchColumn("
        SELECT COALESCE(SUM(total), 0) FROM orders 
        WHERE user_id = ? AND payment_status = 'paid' AND created_at BETWEEN ? AND ?
    ", [$user_id, $start_date, $end_date]);
    
    $pending_payments = $db->fetchColumn("
        SELECT COALESCE(SUM(total), 0) FROM orders 
        WHERE user_id = ? AND payment_status != 'paid' AND created_at BETWEEN ? AND ?
    ", [$user_id, $start_date, $end_date]);
    
    // Get product breakdown
    $product_breakdown = $db->fetchAll("
        SELECT p.name, p.category_id, c.name as category_name,
               SUM(oi.quantity) as total_quantity,
               SUM(oi.subtotal) as total_spent
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_categories c ON p.category_id = c.id
        WHERE o.user_id = ? AND o.created_at BETWEEN ? AND ?
        GROUP BY p.id
        ORDER BY total_spent DESC
        LIMIT 10
    ", [$user_id, $start_date, $end_date]);
    
} catch (Exception $e) {
    error_log("Reports section error: " . $e->getMessage());
    $total_orders = 0;
    $total_spent = 0;
    $pending_payments = 0;
    $product_breakdown = [];
}
?>
<div class="section-card modern-ui">
    <div class="section-header">
        <h2><i class="fas fa-chart-bar"></i> Sales Reports</h2>
        <form method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="hidden" name="section" value="reports">
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="btn btn-outline btn-sm">
            <span>to</span>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="btn btn-outline btn-sm">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>
    
    <!-- Summary Stats -->
    <div class="stats-grid" style="margin-bottom: 30px;">
        <div class="stat-card purple">
            <div class="icon"><i class="fas fa-shopping-bag"></i></div>
            <h3><?php echo $total_orders; ?></h3>
            <p>Total Orders</p>
        </div>
        <div class="stat-card green">
            <div class="icon"><i class="fas fa-naira-sign"></i></div>
            <h3>₦<?php echo number_format($total_spent, 2); ?></h3>
            <p>Total Paid</p>
        </div>
        <div class="stat-card orange">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <h3>₦<?php echo number_format($pending_payments, 2); ?></h3>
            <p>Pending Payments</p>
        </div>
        <div class="stat-card blue">
            <div class="icon"><i class="fas fa-chart-line"></i></div>
            <h3>₦<?php echo $total_orders > 0 ? number_format($total_spent / $total_orders, 2) : '0.00'; ?></h3>
            <p>Average Order Value</p>
        </div>
    </div>
    
    <!-- Product Breakdown -->
    <h3 style="margin-bottom: 20px;"><i class="fas fa-pills"></i> Top Products</h3>
    <?php if (empty($product_breakdown)): ?>
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <p>No product data available for the selected period.</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Quantity Ordered</th>
                    <th>Total Spent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($product_breakdown as $product): ?>
                <tr>
                    <td><strong><?php echo h($product['name']); ?></strong></td>
                    <td><?php echo h($product['category_name'] ?? 'N/A'); ?></td>
                    <td><?php echo $product['total_quantity']; ?></td>
                    <td>₦<?php echo number_format($product['total_spent'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
