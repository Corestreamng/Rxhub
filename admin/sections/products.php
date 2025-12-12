<!-- Admin Products Section -->
<div class="quick-actions">
    <button class="btn btn-primary" onclick="openModal('addProductModal')"><i class="fas fa-plus"></i> Add Product</button>
    <button class="btn btn-outline" onclick="downloadProductTemplate()"><i class="fas fa-download"></i> Download Template</button>
    <button class="btn btn-outline" onclick="openModal('bulkUploadModal')"><i class="fas fa-upload"></i> Bulk Upload</button>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-pills"></i> Products</h2>
        <div style="display: flex; gap: 10px;">
            <input type="text" placeholder="Search products..." class="btn btn-outline btn-sm" style="border: 1px solid var(--light-gray); padding: 8px 12px;" onkeyup="filterTable('productsTable', this.value)">
            <select class="btn btn-outline btn-sm" onchange="filterByCategory(this.value)">
                <option value="">All Categories</option>
                <?php foreach ($section_data['categories'] ?? [] as $cat): ?>
                <option value="<?php echo h($cat['name']); ?>"><?php echo h($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($section_data['products'])): ?>
        <div class="empty-state">
            <i class="fas fa-pills"></i>
            <h3>No Products Yet</h3>
            <p>Add your first product to get started.</p>
            <button class="btn btn-primary" onclick="openModal('addProductModal')"><i class="fas fa-plus"></i> Add Product</button>
        </div>
        <?php else: ?>
        <table class="data-table" id="productsTable">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Manufacturer</th>
                    <th>Purchase Price</th>
                    <th>Selling Price</th>
                    <th>Profit Margin</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($section_data['products'] as $product): 
                    $margin = $product['selling_price'] > 0 ? (($product['selling_price'] - $product['purchase_price']) / $product['selling_price']) * 100 : 0;
                ?>
                <tr data-category="<?php echo h($product['category_name'] ?? ''); ?>">
                    <td><strong><?php echo h($product['sku']); ?></strong></td>
                    <td><?php echo h($product['name']); ?></td>
                    <td><span class="badge info"><?php echo h($product['category_name'] ?? 'Uncategorized'); ?></span></td>
                    <td><?php echo h($product['manufacturer'] ?? 'N/A'); ?></td>
                    <td>₦<?php echo number_format($product['purchase_price'], 2); ?></td>
                    <td>₦<?php echo number_format($product['selling_price'], 2); ?></td>
                    <td><span class="badge <?php echo $margin >= 20 ? 'success' : ($margin >= 10 ? 'warning' : 'danger'); ?>"><?php echo number_format($margin, 1); ?>%</span></td>
                    <td>
                        <span class="badge <?php echo $product['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="editProduct(<?php echo $product['id']; ?>)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="addStock(<?php echo $product['id']; ?>)"><i class="fas fa-boxes"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="toggleProductStatus(<?php echo $product['id']; ?>, <?php echo $product['is_active'] ? 0 : 1; ?>)">
                            <i class="fas fa-<?php echo $product['is_active'] ? 'ban' : 'check'; ?>"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal-overlay" id="addProductModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add New Product</h3>
            <button class="modal-close" onclick="closeModal('addProductModal')">&times;</button>
        </div>
        <form id="addProductForm">
            <input type="hidden" name="action" value="create">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>SKU *</label>
                        <input type="text" name="sku" required placeholder="e.g., AMX-500">
                    </div>
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id">
                            <option value="">Select Category</option>
                            <?php foreach ($section_data['categories'] ?? [] as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo h($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Manufacturer</label>
                        <input type="text" name="manufacturer">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Purchase Price (₦) *</label>
                        <input type="number" name="purchase_price" step="0.01" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Selling Price (₦) *</label>
                        <input type="number" name="selling_price" step="0.01" required min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit">
                            <option value="pack">Pack</option>
                            <option value="bottle">Bottle</option>
                            <option value="box">Box</option>
                            <option value="piece">Piece</option>
                            <option value="carton">Carton</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Min Stock Level</label>
                        <input type="number" name="min_stock_level" value="10" min="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addProductModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Product</button>
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

function filterByCategory(category) {
    const table = document.getElementById('productsTable');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!category || row.dataset.category === category) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function editProduct(productId) {
    alert('Edit product ' + productId + ' - Feature coming soon');
}

function addStock(productId) {
    alert('Add stock for product ' + productId + ' - Feature coming soon');
}

function toggleProductStatus(productId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus ? 'activate' : 'deactivate') + ' this product?')) {
        fetch('../api/admin/products.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'toggle_status', product_id: productId, is_active: newStatus })
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

function downloadProductTemplate() {
    const headers = 'sku,name,description,category_id,manufacturer,purchase_price,selling_price,unit,min_stock_level';
    const blob = new Blob([headers], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'products_template.csv';
    a.click();
}

document.getElementById('addProductForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('../api/admin/products.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product created successfully!');
            closeModal('addProductModal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});
</script>
