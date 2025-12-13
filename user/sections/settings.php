<?php
// Settings Section for User Dashboard
// Display and manage user preferences and settings

if (!isset($db, $user_id)) exit('Restricted');

$success_message = '';
$error_message = '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    try {
        // Get notification preferences
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $order_notifications = isset($_POST['order_notifications']) ? 1 : 0;
        $payment_notifications = isset($_POST['payment_notifications']) ? 1 : 0;
        $marketing_emails = isset($_POST['marketing_emails']) ? 1 : 0;
        
        // Update user preferences (you may need to create a user_preferences table)
        // For now, we'll store in the users table or create a JSON preferences field
        $preferences = json_encode([
            'email_notifications' => $email_notifications,
            'order_notifications' => $order_notifications,
            'payment_notifications' => $payment_notifications,
            'marketing_emails' => $marketing_emails
        ]);
        
        $db->execute("
            UPDATE users SET preferences = ? WHERE id = ?
        ", [$preferences, $user_id]);
        
        $success_message = 'Settings updated successfully!';
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Fetch current settings
try {
    $user_data = $db->fetchOne("SELECT preferences FROM users WHERE id = ?", [$user_id]);
    $preferences = json_decode($user_data['preferences'] ?? '{}', true);
} catch (Exception $e) {
    error_log("Settings section error: " . $e->getMessage());
    $preferences = [];
}
?>
<div class="section-card modern-ui">
    <div class="section-header">
        <h2><i class="fas fa-cog"></i> Settings</h2>
    </div>
    
    <?php if ($success_message): ?>
        <div class="alert" style="background: rgba(34,197,94,0.1); color: var(--success); border: 1px solid var(--success); padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> <?php echo h($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert" style="background: rgba(239,68,68,0.1); color: var(--danger); border: 1px solid var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> <?php echo h($error_message); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <!-- Notification Preferences -->
        <div style="background: var(--light); padding: 25px; border-radius: 12px; margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px;"><i class="fas fa-bell"></i> Notification Preferences</h3>
            
            <div class="setting-item" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 15px; cursor: pointer;">
                    <input type="checkbox" name="email_notifications" value="1" 
                           <?php echo ($preferences['email_notifications'] ?? 1) ? 'checked' : ''; ?>
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <div>
                        <div style="font-weight: 600; color: var(--dark);">Email Notifications</div>
                        <small style="color: var(--gray);">Receive important updates via email</small>
                    </div>
                </label>
            </div>
            
            <div class="setting-item" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 15px; cursor: pointer;">
                    <input type="checkbox" name="order_notifications" value="1" 
                           <?php echo ($preferences['order_notifications'] ?? 1) ? 'checked' : ''; ?>
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <div>
                        <div style="font-weight: 600; color: var(--dark);">Order Updates</div>
                        <small style="color: var(--gray);">Get notified about order status changes</small>
                    </div>
                </label>
            </div>
            
            <div class="setting-item" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 15px; cursor: pointer;">
                    <input type="checkbox" name="payment_notifications" value="1" 
                           <?php echo ($preferences['payment_notifications'] ?? 1) ? 'checked' : ''; ?>
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <div>
                        <div style="font-weight: 600; color: var(--dark);">Payment Reminders</div>
                        <small style="color: var(--gray);">Receive reminders for pending payments</small>
                    </div>
                </label>
            </div>
            
            <div class="setting-item">
                <label style="display: flex; align-items: center; gap: 15px; cursor: pointer;">
                    <input type="checkbox" name="marketing_emails" value="1" 
                           <?php echo ($preferences['marketing_emails'] ?? 0) ? 'checked' : ''; ?>
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <div>
                        <div style="font-weight: 600; color: var(--dark);">Marketing Emails</div>
                        <small style="color: var(--gray);">Receive promotional offers and newsletters</small>
                    </div>
                </label>
            </div>
        </div>
        
        <!-- Account Information -->
        <div style="background: var(--light); padding: 25px; border-radius: 12px; margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px;"><i class="fas fa-user-circle"></i> Account Information</h3>
            
            <div style="margin-bottom: 15px;">
                <strong>Account ID:</strong> 
                <span style="color: var(--gray);"><?php echo h($user_id); ?></span>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Member Since:</strong> 
                <span style="color: var(--gray);">
                    <?php 
                    try {
                        $created = $db->fetchColumn("SELECT created_at FROM users WHERE id = ?", [$user_id]);
                        echo date('F j, Y', strtotime($created));
                    } catch (Exception $e) {
                        echo 'N/A';
                    }
                    ?>
                </span>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Account Status:</strong> 
                <span class="badge success">Active</span>
            </div>
        </div>
        
        <!-- Privacy & Security -->
        <div style="background: var(--light); padding: 25px; border-radius: 12px; margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px;"><i class="fas fa-shield-alt"></i> Privacy & Security</h3>
            
            <div style="margin-bottom: 15px;">
                <a href="dashboard.php?section=profile" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 10px;">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
            
            <div style="margin-bottom: 15px;">
                <button type="button" onclick="enable2FA()" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 10px;">
                    <i class="fas fa-mobile-alt"></i> Enable Two-Factor Authentication
                </button>
            </div>
            
            <div>
                <button type="button" onclick="viewActivityLog()" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 10px;">
                    <i class="fas fa-history"></i> View Activity Log
                </button>
            </div>
        </div>
        
        <!-- Data & Privacy -->
        <div style="background: var(--light); padding: 25px; border-radius: 12px; margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px;"><i class="fas fa-database"></i> Data & Privacy</h3>
            
            <div style="margin-bottom: 15px;">
                <button type="button" onclick="exportData()" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 10px;">
                    <i class="fas fa-download"></i> Download My Data
                </button>
                <small style="display: block; margin-top: 8px; color: var(--gray);">
                    Request a copy of your personal data
                </small>
            </div>
            
            <div>
                <button type="button" onclick="confirmDeleteAccount()" class="btn" style="background: var(--danger); display: inline-flex; align-items: center; gap: 10px;">
                    <i class="fas fa-trash-alt"></i> Delete Account
                </button>
                <small style="display: block; margin-top: 8px; color: var(--gray);">
                    Permanently delete your account and all associated data
                </small>
            </div>
        </div>
        
        <button type="submit" name="update_settings" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Settings
        </button>
    </form>
</div>

<script>
function enable2FA() {
    alert('Two-Factor Authentication setup is coming soon!\n\nThis feature will add an extra layer of security to your account.');
}

function viewActivityLog() {
    alert('Activity Log feature coming soon!\n\nYou will be able to view all account activities including login history and actions.');
}

function exportData() {
    if (confirm('Do you want to download all your data?\n\nThis will include your profile, orders, and payment history.')) {
        window.location.href = '../api/export_user_data.php';
    }
}

function confirmDeleteAccount() {
    if (confirm('WARNING: This action is permanent!\n\nAre you sure you want to delete your account?\n\nAll your data will be permanently removed and cannot be recovered.')) {
        if (confirm('Final confirmation: Type DELETE to confirm account deletion')) {
            // Handle account deletion
            window.location.href = '../api/delete_account.php';
        }
    }
}
</script>
