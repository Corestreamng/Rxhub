<!-- Admin Orders Section -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-shopping-cart"></i> Orders</h2>
        <div style="display: flex; gap: 10px;">
            <input type="text" placeholder="Search orders..." class="btn btn-outline btn-sm" style="border: 1px solid var(--light-gray); padding: 8px 12px;" onkeyup="filterTable('ordersTable', this.value)">
            <select class="btn btn-outline btn-sm" onchange="filterByStatus(this.value)">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($section_data['orders'])): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <h3>No Orders Yet</h3>
            <p>Orders will appear here once customers place them.</p>
        </div>
        <?php else: ?>
        <table class="data-table" id="ordersTable">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Subtotal</th>
                    <th>Tax</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($section_data['orders'] as $order): ?>
                <tr data-status="<?php echo h($order['status']); ?>">
                    <td><strong><?php echo h($order['order_number']); ?></strong></td>
                    <td><?php echo h($order['customer_name'] ?? 'N/A'); ?></td>
                    <td>₦<?php echo number_format($order['subtotal'], 2); ?></td>
                    <td>₦<?php echo number_format($order['tax'], 2); ?></td>
                    <td><strong>₦<?php echo number_format($order['total'], 2); ?></strong></td>
                    <td>
                        <span class="badge <?php 
                            echo $order['status'] === 'delivered' ? 'success' : 
                                 ($order['status'] === 'cancelled' ? 'danger' : 
                                 ($order['status'] === 'pending' ? 'warning' : 'info')); 
                        ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo $order['payment_status'] === 'paid' ? 'success' : ($order['payment_status'] === 'partial' ? 'warning' : 'danger'); ?>">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="viewOrder(<?php echo $order['id']; ?>)"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="updateOrderStatus(<?php echo $order['id']; ?>)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="generateInvoice(<?php echo $order['id']; ?>)"><i class="fas fa-file-invoice"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
function filterTable(tableId, searchText) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tbody tr');
    const search = searchText.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
}

function filterByStatus(status) {
    const table = document.getElementById('ordersTable');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!status || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function viewOrder(orderId) {
    alert('View order details - Feature coming soon');
}

function updateOrderStatus(orderId) {
    const newStatus = prompt('Enter new status (pending, confirmed, processing, shipped, delivered, cancelled):');
    if (newStatus && ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'].includes(newStatus)) {
        fetch('../api/admin/orders.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update_status', order_id: orderId, status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function generateInvoice(orderId) {
    window.open('../api/admin/invoice.php?order_id=' + orderId, '_blank');
}
</script>
