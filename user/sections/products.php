<?php
// Products Section for User Dashboard
if (!isset($products, $categories)) exit('Restricted');
?>
<div class="section-card modern-ui" id="products">
    <div class="section-header">
        <h2><i class="fas fa-pills"></i> Available Products</h2>
        <select id="categoryFilter" class="btn btn-outline btn-sm" onchange="filterProducts(this.value)">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?php echo h($cat['name']); ?>"><?php echo h($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if (empty($products)): ?>
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h3>No Products Available</h3>
        <p>Check back later for available products.</p>
    </div>
    <?php else: ?>
    <div class="products-grid" id="productsGrid">
        <?php foreach ($products as $product): ?>
        <div class="product-card" data-category="<?php echo h($product['category_name'] ?? ''); ?>">
            <div class="product-category"><?php echo h($product['category_name'] ?? 'General'); ?></div>
            <div class="product-name"><?php echo h($product['name']); ?></div>
            <div class="product-manufacturer"><?php echo h($product['manufacturer'] ?? ''); ?></div>
            <div class="product-price">₦<?php echo number_format($product['selling_price'], 2); ?></div>
            <div class="product-actions">
                <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo h($product['name']); ?>', <?php echo $product['selling_price']; ?>)">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
