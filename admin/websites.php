<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Websites';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
    <div class="top-header">
        <h1>Website Management</h1>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        </div>
    </div>
    
    <div class="content-area">
        <!-- Add Website Form -->
        <div class="card">
            <div class="card-header">
                <h3>Add New Website</h3>
            </div>
            <div class="card-body">
                <form id="addWebsiteForm">
                    <div class="flex gap-10">
                        <div class="form-group" style="flex: 1;">
                            <label for="website_name">Website Name *</label>
                            <input type="text" id="website_name" name="website_name" required placeholder="e.g., My Travel Blog">
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="website_type">Website Type *</label>
                            <input type="text" id="website_type" name="website_type" required placeholder="e.g., travel, hostel, ecommerce">
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="owner_name">Owner Name *</label>
                            <input type="text" id="owner_name" name="owner_name" required placeholder="e.g., John Doe">
                        </div>
                        
                        <div class="form-group" style="flex: 0 0 auto; align-self: flex-end;">
                            <button type="submit" class="btn btn-primary">Add Website</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Websites List -->
        <div class="card">
            <div class="card-header">
                <h3>All Websites</h3>
            </div>
            <div class="card-body">
                <div id="websitesContainer">
                    <p class="text-center">Loading websites...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
// Load websites on page load
document.addEventListener('DOMContentLoaded', function() {
    loadWebsites();
    
    // Handle add website form
    document.getElementById('addWebsiteForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            website_name: document.getElementById('website_name').value,
            website_type: document.getElementById('website_type').value,
            owner_name: document.getElementById('owner_name').value
        };
        
        showLoading();
        const result = await fetchData('ajax/add-website.php', 'POST', formData);
        hideLoading();
        
        if (result.success) {
            showAlert(result.message, 'success');
            this.reset();
            loadWebsites();
        } else {
            showAlert(result.message, 'error');
        }
    });
});

// Load websites function
async function loadWebsites() {
    const container = document.getElementById('websitesContainer');
    
    const result = await fetchData('ajax/fetch-websites.php');
    
    if (result.success && result.data.length > 0) {
        let html = `
            <div class="table-responsive">
                <table class="advanced-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Website Name</th>
                            <th>Type</th>
                            <th>Owner</th>
                            <th>API Key</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        result.data.forEach(website => {
            // Create badge class based on website type
            const typeClass = `badge badge-type-${website.website_type.toLowerCase().replace(/\s+/g, '-')}`;
            
            html += `
                <tr>
                    <td><strong>#${website.id}</strong></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem;">
                                ${escapeHtml(website.website_name.charAt(0).toUpperCase())}
                            </div>
                            <strong>${escapeHtml(website.website_name)}</strong>
                        </div>
                    </td>
                    <td><span class="${typeClass}">${escapeHtml(website.website_type)}</span></td>
                    <td>${escapeHtml(website.owner_name)}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <code style="font-size: 11px; background: #f1f5f9; padding: 4px 8px; border-radius: 4px; color: #475569;">${website.website_key.substring(0, 20)}...</code>
                            <button onclick="copyToClipboard('${website.website_key}')" class="btn btn-sm btn-secondary" title="Copy Full API Key">📋</button>
                        </div>
                    </td>
                    <td style="color: #64748b; font-size: 0.8rem;">${formatDate(website.created_at)}</td>
                    <td>
                        <button onclick="deleteWebsite(${website.id})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        container.innerHTML = html;
    } else {
        container.innerHTML = '<p class="text-center text-muted">No websites found. Add your first website above.</p>';
    }
}

// Delete website function
async function deleteWebsite(id) {
    if (!confirmAction('Are you sure you want to delete this website? This action cannot be undone.')) {
        return;
    }
    
    showLoading();
    const result = await fetchData('ajax/delete-website.php', 'POST', { id: id });
    hideLoading();
    
    if (result.success) {
        showAlert(result.message, 'success');
        loadWebsites();
    } else {
        showAlert(result.message, 'error');
    }
}

// Escape HTML helper
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
