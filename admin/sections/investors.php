<!-- Admin Investors Section -->
<div class="quick-actions">
    <button class="btn btn-primary" onclick="openModal('addInvestorModal')"><i class="fas fa-user-plus"></i> Add Investor</button>
    <button class="btn btn-outline" onclick="downloadInvestorTemplate()"><i class="fas fa-download"></i> Download Template</button>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-hand-holding-usd"></i> Investors</h2>
        <input type="text" placeholder="Search investors..." class="btn btn-outline btn-sm" style="border: 1px solid var(--light-gray); padding: 8px 12px;" onkeyup="filterTable('investorsTable', this.value)">
    </div>
    <div class="card-body">
        <?php if (empty($section_data['investors'])): ?>
        <div class="empty-state">
            <i class="fas fa-hand-holding-usd"></i>
            <h3>No Investors Yet</h3>
            <p>Investors will appear here once they register.</p>
        </div>
        <?php else: ?>
        <table class="data-table" id="investorsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Investment Range</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($section_data['investors'] as $investor): ?>
                <tr>
                    <td><strong><?php echo h($investor['full_name']); ?></strong></td>
                    <td><?php echo h($investor['email']); ?></td>
                    <td><?php echo h($investor['company_name'] ?? 'N/A'); ?></td>
                    <td><span class="badge primary"><?php echo ucfirst(str_replace('_', ' ', h($investor['investor_type']))); ?></span></td>
                    <td><?php echo ucfirst(str_replace('_', ' ', h($investor['investment_range']))); ?></td>
                    <td>
                        <span class="badge <?php echo $investor['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $investor['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($investor['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="viewInvestor(<?php echo $investor['id']; ?>)"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="editInvestor(<?php echo $investor['id']; ?>)"><i class="fas fa-edit"></i></button>
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

function viewInvestor(investorId) {
    alert('View investor details - Feature coming soon');
}

function editInvestor(investorId) {
    alert('Edit investor - Feature coming soon');
}

function downloadInvestorTemplate() {
    const headers = 'full_name,email,phone,company_name,investor_type,investment_range,password';
    const blob = new Blob([headers], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'investors_template.csv';
    a.click();
}
</script>
