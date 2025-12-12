<!-- Admin Settings Section -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-cog"></i> System Settings</h2>
    </div>
    <div class="card-body">
        <form id="settingsForm" class="settings-form">
            <!-- Company Settings -->
            <div class="settings-group">
                <h3><i class="fas fa-building"></i> Company Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" value="<?php echo h($section_data['settings'][array_search('company_name', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? 'RxHub'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Company Email</label>
                        <input type="email" name="company_email" value="<?php echo h($section_data['settings'][array_search('company_email', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Company Phone</label>
                    <input type="text" name="company_phone" value="<?php echo h($section_data['settings'][array_search('company_phone', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? ''); ?>">
                </div>
            </div>
            
            <!-- Currency Settings -->
            <div class="settings-group">
                <h3><i class="fas fa-money-bill"></i> Currency Settings</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Currency Code</label>
                        <select name="currency">
                            <option value="NGN" <?php echo (($section_data['settings'][array_search('currency', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? 'NGN') === 'NGN') ? 'selected' : ''; ?>>NGN - Nigerian Naira</option>
                            <option value="USD" <?php echo (($section_data['settings'][array_search('currency', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? 'NGN') === 'USD') ? 'selected' : ''; ?>>USD - US Dollar</option>
                            <option value="GBP" <?php echo (($section_data['settings'][array_search('currency', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? 'NGN') === 'GBP') ? 'selected' : ''; ?>>GBP - British Pound</option>
                            <option value="EUR" <?php echo (($section_data['settings'][array_search('currency', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? 'NGN') === 'EUR') ? 'selected' : ''; ?>>EUR - Euro</option>
                            <option value="GHS" <?php echo (($section_data['settings'][array_search('currency', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? 'NGN') === 'GHS') ? 'selected' : ''; ?>>GHS - Ghanaian Cedi</option>
                            <option value="KES" <?php echo (($section_data['settings'][array_search('currency', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? 'NGN') === 'KES') ? 'selected' : ''; ?>>KES - Kenyan Shilling</option>
                            <option value="ZAR" <?php echo (($section_data['settings'][array_search('currency', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? 'NGN') === 'ZAR') ? 'selected' : ''; ?>>ZAR - South African Rand</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="<?php echo h($section_data['settings'][array_search('currency_symbol', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? '₦'); ?>" maxlength="5">
                    </div>
                </div>
            </div>
            
            <!-- Sales Cycle Settings -->
            <div class="settings-group">
                <h3><i class="fas fa-calendar-alt"></i> Sales Cycle Settings</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Sales Cycle Duration (Days)</label>
                        <input type="number" name="sales_cycle_days" value="<?php echo h($section_data['settings'][array_search('sales_cycle_days', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? '30'); ?>" min="1" max="365">
                    </div>
                    <div class="form-group">
                        <label>Tax Rate (%)</label>
                        <input type="number" name="tax_rate" value="<?php echo h($section_data['settings'][array_search('tax_rate', array_column($section_data['settings'], 'setting_key'))]['setting_value'] ?? '7.5'); ?>" min="0" max="100" step="0.1">
                    </div>
                </div>
            </div>
            
            <!-- Investment Settings -->
            <div class="settings-group">
                <h3><i class="fas fa-chart-line"></i> Investment Settings</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Default ROI Calculation Period</label>
                        <select name="roi_period">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annually">Annually</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Interest Calculation Method</label>
                        <select name="interest_method">
                            <option value="simple">Simple Interest</option>
                            <option value="compound">Compound Interest</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Notification Settings -->
            <div class="settings-group">
                <h3><i class="fas fa-bell"></i> Notification Settings</h3>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="email_notifications" value="1" checked>
                        Enable Email Notifications
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="low_stock_alerts" value="1" checked>
                        Enable Low Stock Alerts
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="order_notifications" value="1" checked>
                        Enable Order Notifications
                    </label>
                </div>
            </div>
            
            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'update_settings');
    
    fetch('../api/admin/settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving settings.');
    });
});
</script>
