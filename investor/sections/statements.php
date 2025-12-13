<?php
// Statements Section for Investor Dashboard
if (!isset($db, $investor_id)) exit('Restricted');
try {
    $statements = $db->fetchAll("SELECT * FROM statements WHERE investor_id = ? ORDER BY created_at DESC", [$investor_id]);
} catch (Exception $e) {
    $statements = [];
}
?>
<div class="section-card modern-ui">
    <div class="section-header">
        <h2><i class="fas fa-file-invoice-dollar"></i> Statements</h2>
    </div>
    <?php if (empty($statements)): ?>
        <div class="empty-state">
            <i class="fas fa-file-invoice-dollar"></i>
            <h3>No Statements Found</h3>
            <p>You have not received any statements yet.</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Statement #</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statements as $statement): ?>
                <tr>
                    <td><strong><?php echo h($statement['statement_number']); ?></strong></td>
                    <td>$<?php echo number_format($statement['amount'], 2); ?></td>
                    <td><span class="badge <?php echo h($statement['status']); ?>"><?php echo ucfirst($statement['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($statement['created_at'])); ?></td>
                    <td><a href="#" class="btn btn-outline btn-sm">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
