<?php
// Invoices Section for User Dashboard
// Fetch and display user invoices

if (!isset($db, $user_id)) exit('Restricted');

try {
    $invoices = $db->fetchAll("SELECT * FROM invoices WHERE user_id = ? ORDER BY created_at DESC", [$user_id]);
} catch (Exception $e) {
    $invoices = [];
}
?>
<div class="section-card modern-ui">
    <div class="section-header">
        <h2><i class="fas fa-file-invoice"></i> My Invoices</h2>
    </div>
    <?php if (empty($invoices)): ?>
        <div class="empty-state">
            <i class="fas fa-file-invoice"></i>
            <h3>No Invoices Found</h3>
            <p>You have not received any invoices yet.</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Order #</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><strong><?php echo h($invoice['invoice_number']); ?></strong></td>
                    <td><?php echo h($invoice['order_number']); ?></td>
                    <td>₦<?php echo number_format($invoice['amount'], 2); ?></td>
                    <td><span class="badge <?php echo h($invoice['status']); ?>"><?php echo ucfirst($invoice['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($invoice['created_at'])); ?></td>
                    <td><a href="#" class="btn btn-outline btn-sm">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
