<!-- Admin Stock Management Section -->
<div class="quick-actions">
    <button class="btn btn-primary" onclick="openModal('addStockModal')"><i class="fas fa-plus"></i> Add Stock</button>
    <button class="btn btn-outline" onclick="downloadStockTemplate()"><i class="fas fa-download"></i> Download Template</button>
    <button class="btn btn-outline" onclick="openModal('bulkUploadModal')"><i class="fas fa-upload"></i> Bulk Upload</button>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-boxes"></i> Stock Management</h2>
        <input type="text" placeholder="Search stock..." class="btn btn-outline btn-sm" style="border: 1px solid var(--light-gray); padding: 8px 12px;" onkeyup="filterTable('stockTable', this.value)">
    </div>
    <div class="card-body">
        <?php if (empty($section_data['stock'])): ?>
        <div class="empty-state">
            <i class="fas fa-boxes"></i>
            <h3>No Stock Records Yet</h3>
            <p>Add stock for your products to start tracking inventory.</p>
            <button class="btn btn-primary" onclick="openModal('addStockModal')"><i class="fas fa-plus"></i> Add Stock</button>
        </div>
        <?php else: ?>
        <table class="data-table" id="stockTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Batch Number</th>
                    <th>Expiry Date</th>
                    <th>Purchase Price</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($section_data['stock'] as $stock): ?>
                <tr>
                    <td><strong><?php echo h($stock['product_name']); ?></strong></td>
                    <td><?php echo h($stock['sku']); ?></td>
                    <td>
                        <span class="badge <?php echo $stock['quantity'] > 10 ? 'success' : ($stock['quantity'] > 0 ? 'warning' : 'danger'); ?>">
                            <?php echo number_format($stock['quantity']); ?> units
                        </span>
                    </td>
                    <td><?php echo h($stock['batch_number'] ?? 'N/A'); ?></td>
                    <td>
                        <?php if ($stock['expiry_date']): ?>
                            <?php 
                            $expiry = strtotime($stock['expiry_date']);
                            $now = time();
                            $days_until_expiry = ($expiry - $now) / (60 * 60 * 24);
                            ?>
                            <span class="badge <?php echo $days_until_expiry < 30 ? 'danger' : ($days_until_expiry < 90 ? 'warning' : 'success'); ?>">
                                <?php echo date('M d, Y', $expiry); ?>
                            </span>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>₦<?php echo number_format($stock['purchase_price'] ?? 0, 2); ?></td>
                    <td><?php echo date('M d, Y', strtotime($stock['date_added'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="editStock(<?php echo $stock['id']; ?>)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="adjustStock(<?php echo $stock['id']; ?>)"><i class="fas fa-plus-minus"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Add Stock Modal -->
<div class="modal-overlay" id="addStockModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add Stock</h3>
            <button class="modal-close" onclick="closeModal('addStockModal')">&times;</button>
        </div>
        <form id="addStockForm">
            <input type="hidden" name="action" value="create">
            <div class="modal-body">
                <div class="form-group">
                    <label>Product *</label>
                    <select name="product_id" required>
                        <option value="">Select Product</option>
                        <?php 
                        $products = $db->fetchAll("SELECT id, name, sku FROM products WHERE is_active = 1 ORDER BY name");
                        foreach ($products as $product): 
                        ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo h($product['name']); ?> (<?php echo h($product['sku']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Purchase Price (₦)</label>
                        <input type="number" name="purchase_price" step="0.01" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number">
                    </div>
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date">
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addStockModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Stock</button>
            </div>
        </form>
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

function editStock(stockId) {
    alert('Edit stock ' + stockId + ' - Feature coming soon');
}

function adjustStock(stockId) {
    const adjustment = prompt('Enter quantity adjustment (positive to add, negative to subtract):');
    if (adjustment !== null && !isNaN(adjustment)) {
        fetch('../api/admin/stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'adjust', stock_id: stockId, adjustment: parseInt(adjustment) })
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

function downloadStockTemplate() {
    const headers = 'product_id,quantity,batch_number,expiry_date,purchase_price,notes';
    const blob = new Blob([headers], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'stock_template.csv';
    a.click();
}

document.getElementById('addStockForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('../api/admin/stock.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Stock added successfully!');
            closeModal('addStockModal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});
</script>
