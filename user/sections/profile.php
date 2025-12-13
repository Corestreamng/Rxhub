<?php
// Profile Section for User Dashboard
// Display and edit user profile information

if (!isset($db, $user_id)) exit('Restricted');

$success_message = '';
$error_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        $facility_name = trim($_POST['facility_name']);
        $facility_type = trim($_POST['facility_type']);
        $address = trim($_POST['address']);
        
        // Validate inputs
        if (empty($full_name) || empty($phone)) {
            throw new Exception('Full name and phone are required');
        }
        
        $db->execute("
            UPDATE users SET 
                full_name = ?,
                phone = ?,
                facility_name = ?,
                facility_type = ?,
                address = ?
            WHERE id = ?
        ", [$full_name, $phone, $facility_name, $facility_type, $address, $user_id]);
        
        // Update session
        Session::set('user_name', $full_name);
        Session::set('facility_name', $facility_name);
        
        $success_message = 'Profile updated successfully!';
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    try {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            throw new Exception('All password fields are required');
        }
        
        if ($new_password !== $confirm_password) {
            throw new Exception('New passwords do not match');
        }
        
        if (strlen($new_password) < 8) {
            throw new Exception('Password must be at least 8 characters');
        }
        
        // Verify current password
        $user = $db->fetchOne("SELECT password FROM users WHERE id = ?", [$user_id]);
        if (!password_verify($current_password, $user['password'])) {
            throw new Exception('Current password is incorrect');
        }
        
        // Update password
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $db->execute("UPDATE users SET password = ? WHERE id = ?", [$hashed, $user_id]);
        
        $success_message = 'Password changed successfully!';
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Fetch user data
try {
    $user_data = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$user_id]);
} catch (Exception $e) {
    error_log("Profile section error: " . $e->getMessage());
    $user_data = [];
}
?>
<div class="section-card modern-ui">
    <div class="section-header">
        <h2><i class="fas fa-user"></i> My Profile</h2>
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
    
    <!-- Profile Information -->
    <form method="POST" style="margin-bottom: 40px;">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-info-circle"></i> Personal Information</h3>
        
        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo h($user_data['full_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" value="<?php echo h($user_data['email'] ?? ''); ?>" readonly style="background: var(--light-gray);">
                <small style="color: var(--gray);">Email cannot be changed</small>
            </div>
        </div>
        
        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" value="<?php echo h($user_data['phone'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="facility_type">Facility Type</label>
                <select id="facility_type" name="facility_type">
                    <option value="">Select Type</option>
                    <option value="pharmacy" <?php echo ($user_data['facility_type'] ?? '') === 'pharmacy' ? 'selected' : ''; ?>>Pharmacy</option>
                    <option value="hospital" <?php echo ($user_data['facility_type'] ?? '') === 'hospital' ? 'selected' : ''; ?>>Hospital</option>
                    <option value="clinic" <?php echo ($user_data['facility_type'] ?? '') === 'clinic' ? 'selected' : ''; ?>>Clinic</option>
                    <option value="lab" <?php echo ($user_data['facility_type'] ?? '') === 'lab' ? 'selected' : ''; ?>>Laboratory</option>
                    <option value="other" <?php echo ($user_data['facility_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="facility_name">Facility Name</label>
            <input type="text" id="facility_name" name="facility_name" value="<?php echo h($user_data['facility_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3" style="width: 100%; padding: 12px; border: 1px solid var(--light-gray); border-radius: 8px;"><?php echo h($user_data['address'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit" name="update_profile" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Profile
        </button>
    </form>
    
    <!-- Change Password -->
    <form method="POST" style="max-width: 600px;">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-lock"></i> Change Password</h3>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="current_password">Current Password *</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="new_password">New Password * (min 8 characters)</label>
            <input type="password" id="new_password" name="new_password" required minlength="8">
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label for="confirm_password">Confirm New Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
        </div>
        
        <button type="submit" name="change_password" class="btn btn-primary">
            <i class="fas fa-key"></i> Change Password
        </button>
    </form>
</div>

<style>
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--dark); }
.form-group input, .form-group select, .form-group textarea { 
    width: 100%; 
    padding: 12px 15px; 
    border: 2px solid var(--light-gray); 
    border-radius: 8px; 
    font-size: 1rem; 
    transition: border 0.3s;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { 
    outline: none; 
    border-color: var(--primary); 
}
.form-group small { font-size: 0.85rem; display: block; margin-top: 5px; }
</style>
