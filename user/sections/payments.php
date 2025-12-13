<?php
// Payments Section for User Dashboard
// Display payment history and options

if (!isset($db, $user_id)) exit('Restricted');

try {
    // Fetch payments for this user
    $payments = $db->fetchAll("
        SELECT p.*, o.order_number, o.total as order_total
        FROM payments p
        LEFT JOIN orders o ON p.order_id = o.id
        WHERE o.user_id = ?
        ORDER BY p.payment_date DESC
    ", [$user_id]);
} catch (Exception $e) {
    $payments = [];
    error_log("Payments section error: " . $e->getMessage());
}
?>
<div class="section-card modern-ui">
    <div class="section-header">
        <h2><i class="fas fa-credit-card"></i> Payment History</h2>
    </div>
    <?php if (empty($payments)): ?>
        <div class="empty-state">
            <i class="fas fa-credit-card"></i>
            <h3>No Payment Records</h3>
            <p>Your payment history will appear here once you make payments.</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Payment Ref</th>
                    <th>Order #</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><strong><?php echo h($payment['payment_reference'] ?? 'N/A'); ?></strong></td>
                    <td><?php echo h($payment['order_number']); ?></td>
                    <td>₦<?php echo number_format($payment['amount'], 2); ?></td>
                    <td><?php echo ucfirst(h($payment['payment_method'])); ?></td>
                    <td><span class="badge <?php echo h($payment['status']); ?>"><?php echo ucfirst(h($payment['status'])); ?></span></td>
                    <td><?php echo date('M d, Y H:i', strtotime($payment['payment_date'])); ?></td>
                    <td>
                        <button class="btn btn-outline btn-sm" onclick="viewPaymentDetails(<?php echo $payment['id']; ?>)">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function viewPaymentDetails(paymentId) {
    // Fetch and display payment details
    fetch(`../api/get_payment_details.php?id=${paymentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment Details:\n' + JSON.stringify(data.payment, null, 2));
            } else {
                alert('Failed to load payment details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading payment details');
        });
}
</script>
