<?php
// Orders Section for User Dashboard
if (!isset($orders)) exit('Restricted');
?>
<div class="section-card modern-ui" id="orders">
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
