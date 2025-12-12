<!-- Admin Investment Options Section -->
<div class="quick-actions">
    <button class="btn btn-primary" onclick="openModal('addInvestmentModal')"><i class="fas fa-plus"></i> Create Investment Option</button>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-rocket"></i> Investment Options</h2>
        <select class="btn btn-outline btn-sm" onchange="filterByStatus('investmentsTable', this.value)">
            <option value="">All Status</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
            <option value="fully_funded">Fully Funded</option>
        </select>
    </div>
    <div class="card-body">
        <?php if (empty($section_data['investment_options'])): ?>
        <div class="empty-state">
            <i class="fas fa-rocket"></i>
            <h3>No Investment Options Yet</h3>
            <p>Create your first investment option to attract investors.</p>
            <button class="btn btn-primary" onclick="openModal('addInvestmentModal')"><i class="fas fa-plus"></i> Create Investment</button>
        </div>
        <?php else: ?>
        <table class="data-table" id="investmentsTable">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Min Investment</th>
                    <th>Target</th>
                    <th>Current</th>
                    <th>ROI</th>
                    <th>Duration</th>
                    <th>Risk</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($section_data['investment_options'] as $option): 
                    $progress = $option['target_amount'] > 0 ? ($option['current_amount'] / $option['target_amount']) * 100 : 0;
                ?>
                <tr data-status="<?php echo h($option['status']); ?>">
                    <td><strong><?php echo h($option['title']); ?></strong></td>
                    <td><span class="badge primary"><?php echo ucfirst(str_replace('_', ' ', h($option['category']))); ?></span></td>
                    <td>$<?php echo number_format($option['min_investment'], 0); ?></td>
                    <td>$<?php echo number_format($option['target_amount'], 0); ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="flex: 1; background: var(--light-gray); height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="width: <?php echo min($progress, 100); ?>%; height: 100%; background: var(--primary);"></div>
                            </div>
                            <span style="font-size: 0.85rem;"><?php echo number_format($progress, 0); ?>%</span>
                        </div>
                    </td>
                    <td><?php echo h($option['expected_roi']); ?></td>
                    <td><?php echo $option['duration_months']; ?> months</td>
                    <td>
                        <span class="badge <?php echo $option['risk_level'] === 'low' ? 'success' : ($option['risk_level'] === 'medium' ? 'warning' : 'danger'); ?>">
                            <?php echo ucfirst($option['risk_level']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo $option['status'] === 'open' ? 'success' : ($option['status'] === 'fully_funded' ? 'info' : 'danger'); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $option['status'])); ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="editInvestment(<?php echo $option['id']; ?>)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="viewInvestors(<?php echo $option['id']; ?>)"><i class="fas fa-users"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Add Investment Modal -->
<div class="modal-overlay" id="addInvestmentModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Create Investment Option</h3>
            <button class="modal-close" onclick="closeModal('addInvestmentModal')">&times;</button>
        </div>
        <form id="addInvestmentForm">
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
                        <input type="number" name="min_investment" step="0.01" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Max Investment ($)</label>
                        <input type="number" name="max_investment" step="0.01" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Target Amount ($) *</label>
                        <input type="number" name="target_amount" step="0.01" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Expected ROI *</label>
                        <input type="text" name="expected_roi" placeholder="e.g., 15-20% annually" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Duration (months) *</label>
                        <input type="number" name="duration_months" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
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
function filterByStatus(tableId, status) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!status || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function editInvestment(investmentId) {
    alert('Edit investment option ' + investmentId + ' - Feature coming soon');
}

function viewInvestors(investmentId) {
    alert('View investors for option ' + investmentId + ' - Feature coming soon');
}

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
