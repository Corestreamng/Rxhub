<?php
// Analytics Section for User Dashboard
// Display advanced analytics and insights

if (!isset($db, $user_id)) exit('Restricted');

try {
    // Monthly spending trend (last 6 months)
    $monthly_trend = $db->fetchAll("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as order_count,
            SUM(total) as total_spent
        FROM orders
        WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month DESC
    ", [$user_id]);
    
    // Category distribution
    $category_stats = $db->fetchAll("
        SELECT 
            c.name as category,
            COUNT(DISTINCT o.id) as order_count,
            SUM(oi.subtotal) as total_spent
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_categories c ON p.category_id = c.id
        WHERE o.user_id = ?
        GROUP BY c.id
        ORDER BY total_spent DESC
    ", [$user_id]);
    
    // Order status distribution
    $status_stats = $db->fetchAll("
        SELECT 
            status,
            COUNT(*) as count,
            SUM(total) as total_amount
        FROM orders
        WHERE user_id = ?
        GROUP BY status
    ", [$user_id]);
    
} catch (Exception $e) {
    error_log("Analytics section error: " . $e->getMessage());
    $monthly_trend = [];
    $category_stats = [];
    $status_stats = [];
}
?>
<div class="section-card modern-ui">
    <div class="section-header">
        <h2><i class="fas fa-chart-line"></i> Analytics & Insights</h2>
        <button class="btn btn-outline btn-sm" onclick="exportAnalytics()">
            <i class="fas fa-download"></i> Export Report
        </button>
    </div>
    
    <!-- Monthly Trend -->
    <h3 style="margin-bottom: 20px;"><i class="fas fa-calendar-alt"></i> Monthly Spending Trend (Last 6 Months)</h3>
    <?php if (empty($monthly_trend)): ?>
        <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <p>No data available yet. Start ordering to see trends.</p>
        </div>
    <?php else: ?>
        <table class="data-table" style="margin-bottom: 40px;">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Avg per Order</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($monthly_trend as $trend): ?>
                <tr>
                    <td><?php echo date('F Y', strtotime($trend['month'] . '-01')); ?></td>
                    <td><?php echo $trend['order_count']; ?></td>
                    <td>₦<?php echo number_format($trend['total_spent'], 2); ?></td>
                    <td>₦<?php echo number_format($trend['total_spent'] / $trend['order_count'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <!-- Category Distribution -->
    <h3 style="margin-bottom: 20px;"><i class="fas fa-tags"></i> Spending by Category</h3>
    <?php if (empty($category_stats)): ?>
        <div class="empty-state">
            <i class="fas fa-tags"></i>
            <p>No category data available.</p>
        </div>
    <?php else: ?>
        <table class="data-table" style="margin-bottom: 40px;">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_all = array_sum(array_column($category_stats, 'total_spent'));
                foreach ($category_stats as $cat): 
                    $percentage = $total_all > 0 ? ($cat['total_spent'] / $total_all) * 100 : 0;
                ?>
                <tr>
                    <td><strong><?php echo h($cat['category'] ?? 'Uncategorized'); ?></strong></td>
                    <td><?php echo $cat['order_count']; ?></td>
                    <td>₦<?php echo number_format($cat['total_spent'], 2); ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="flex: 1; height: 8px; background: var(--light-gray); border-radius: 4px; overflow: hidden;">
                                <div style="width: <?php echo $percentage; ?>%; height: 100%; background: var(--primary);"></div>
                            </div>
                            <span><?php echo number_format($percentage, 1); ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <!-- Order Status Distribution -->
    <h3 style="margin-bottom: 20px;"><i class="fas fa-tasks"></i> Order Status Overview</h3>
    <?php if (empty($status_stats)): ?>
        <div class="empty-state">
            <i class="fas fa-tasks"></i>
            <p>No order data available.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <?php foreach ($status_stats as $status): ?>
            <div class="stat-card <?php 
                echo $status['status'] === 'completed' ? 'green' : 
                    ($status['status'] === 'pending' ? 'orange' : 
                    ($status['status'] === 'cancelled' ? 'red' : 'blue')); 
            ?>">
                <div class="icon"><i class="fas fa-<?php 
                    echo $status['status'] === 'completed' ? 'check-circle' : 
                        ($status['status'] === 'pending' ? 'clock' : 
                        ($status['status'] === 'cancelled' ? 'times-circle' : 'spinner')); 
                ?>"></i></div>
                <h3><?php echo $status['count']; ?></h3>
                <p><?php echo ucfirst($status['status']); ?></p>
                <small>₦<?php echo number_format($status['total_amount'], 2); ?></small>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function exportAnalytics() {
    window.location.href = '../api/export_analytics.php?user_id=<?php echo $user_id; ?>';
}
</script>
