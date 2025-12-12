<!-- Admin Users Section -->
<div class="quick-actions">
    <button class="btn btn-primary" onclick="openModal('addUserModal')"><i class="fas fa-user-plus"></i> Add User</button>
    <button class="btn btn-outline" onclick="downloadTemplate()"><i class="fas fa-download"></i> Download Template</button>
    <button class="btn btn-outline" onclick="openModal('bulkUploadModal')"><i class="fas fa-upload"></i> Bulk Upload</button>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-users"></i> Healthcare Users</h2>
        <div style="display: flex; gap: 10px;">
            <input type="text" id="userSearch" placeholder="Search users..." class="btn btn-outline btn-sm" style="border: 1px solid var(--light-gray); padding: 8px 12px;" onkeyup="filterTable('usersTable', this.value)">
            <select class="btn btn-outline btn-sm" onchange="filterByType('usersTable', this.value)">
                <option value="">All Types</option>
                <option value="pharmacy">Pharmacy</option>
                <option value="hospital">Hospital</option>
                <option value="clinic">Clinic</option>
                <option value="other">Other</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($section_data['users'])): ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>No Users Yet</h3>
            <p>Add your first healthcare user to get started.</p>
            <button class="btn btn-primary" onclick="openModal('addUserModal')"><i class="fas fa-plus"></i> Add User</button>
        </div>
        <?php else: ?>
        <table class="data-table" id="usersTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Facility</th>
                    <th>Type</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($section_data['users'] as $user): ?>
                <tr data-type="<?php echo h($user['facility_type']); ?>">
                    <td><strong><?php echo h($user['full_name']); ?></strong></td>
                    <td><?php echo h($user['email']); ?></td>
                    <td><?php echo h($user['facility_name'] ?? 'N/A'); ?></td>
                    <td><span class="badge primary"><?php echo ucfirst(h($user['facility_type'] ?? 'N/A')); ?></span></td>
                    <td><?php echo h($user['phone'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="badge <?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="editUser(<?php echo $user['id']; ?>)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? 0 : 1; ?>)">
                            <i class="fas fa-<?php echo $user['is_active'] ? 'ban' : 'check'; ?>"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal-overlay" id="addUserModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add New User</h3>
            <button class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
        </div>
        <form id="addUserForm">
            <input type="hidden" name="action" value="create">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Facility Type *</label>
                        <select name="facility_type" required>
                            <option value="pharmacy">Pharmacy</option>
                            <option value="hospital">Hospital</option>
                            <option value="clinic">Clinic</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Facility Name</label>
                        <input type="text" name="facility_name">
                    </div>
                    <div class="form-group">
                        <label>License Number</label>
                        <input type="text" name="license_number">
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="2"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city">
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state">
                    </div>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required minlength="8">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
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

function filterByType(tableId, type) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!type || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function editUser(userId) {
    // Load user data and open edit modal
    alert('Edit user ' + userId + ' - Feature coming soon');
}

function toggleUserStatus(userId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus ? 'activate' : 'deactivate') + ' this user?')) {
        fetch('../api/admin/users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'toggle_status', user_id: userId, is_active: newStatus })
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

document.getElementById('addUserForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('../api/admin/users.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User created successfully!');
            closeModal('addUserModal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});
</script>
