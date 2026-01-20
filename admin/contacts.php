<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Contacts';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
    <div class="top-header">
        <h1>Contact Messages</h1>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        </div>
    </div>
    
    <div class="content-area">
        <div class="card">
            <div class="card-header">
                <h3>All Contact Messages</h3>
                <div class="header-actions">
                    <span id="totalCount" class="badge badge-success">0 Total</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-controls">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search by name, email, message...">
                    </div>
                    <div class="entries-control">
                        <label>Show 
                            <select id="entriesPerPage">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </label>
                    </div>
                </div>
                
                <div id="contactsContainer">
                    <p class="text-center">Loading contacts...</p>
                </div>
                
                <div id="paginationContainer" class="pagination-wrapper"></div>
            </div>
        </div>
    </div>
</div>

<div id="messageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Message Details</h3>
            <button class="modal-close" onclick="closeModal('messageModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="modalMessageContent"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('messageModal')">Close</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/main.js"></script>

<script>
let allContacts = [];
let filteredContacts = [];
let currentPage = 1;
let entriesPerPage = 10;

// Load contacts on page load
document.addEventListener('DOMContentLoaded', function() {
    loadContacts();
    
    // Search input
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterContacts(this.value);
    });
    
    // Entries per page
    document.getElementById('entriesPerPage').addEventListener('change', function() {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        renderTable();
    });
});

// Load contacts function
async function loadContacts() {
    if(typeof showLoading === 'function') showLoading();
    
    const result = await fetchData('ajax/fetch-contacts.php');
    
    if (result.success) {
        // Sort by ID descending (newest first)
        allContacts = result.data.sort((a, b) => b.id - a.id);
        filteredContacts = [...allContacts];
        
        // Update total count
        document.getElementById('totalCount').textContent = allContacts.length + ' Total';
        
        renderTable();
    } else {
        document.getElementById('contactsContainer').innerHTML = 
            '<div class="text-center py-4"><p class="text-muted">Failed to load contacts.</p></div>';
        
        if(typeof showAlert === 'function') showAlert('Failed to load data', 'danger');
    }

    if(typeof hideLoading === 'function') hideLoading();
}

// Filter contacts
function filterContacts(searchTerm) {
    searchTerm = searchTerm.toLowerCase();
    
    if (searchTerm === '') {
        filteredContacts = [...allContacts];
    } else {
        filteredContacts = allContacts.filter(contact => {
            return (
                contact.name.toLowerCase().includes(searchTerm) ||
                contact.email.toLowerCase().includes(searchTerm) ||
                contact.message.toLowerCase().includes(searchTerm) ||
                (contact.country && contact.country.toLowerCase().includes(searchTerm)) ||
                contact.website_name.toLowerCase().includes(searchTerm) ||
                contact.website_type.toLowerCase().includes(searchTerm)
            );
        });
    }
    
    currentPage = 1;
    renderTable();
}

// Render Table (Advanced Style)
function renderTable() {
    const container = document.getElementById('contactsContainer');
    
    if (filteredContacts.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <div style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;">🔍</div>
                <h4 class="text-muted">No messages found</h4>
                <p class="text-muted small">Try adjusting your search criteria</p>
            </div>`;
        document.getElementById('paginationContainer').innerHTML = '';
        return;
    }
    
    // Calculate pagination
    const totalPages = Math.ceil(filteredContacts.length / entriesPerPage);
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = Math.min(startIndex + entriesPerPage, filteredContacts.length);
    const pageData = filteredContacts.slice(startIndex, endIndex);
    
    // Build table HTML
    let html = `
        <div class="table-info">
            Showing <b>${startIndex + 1}</b> to <b>${endIndex}</b> of <b>${filteredContacts.length}</b> entries
        </div>
        <div class="table-responsive">
            <table class="advanced-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">User</th>
                        <th width="25%">Message</th>
                        <th width="20%">Website</th>
                        <th width="10%">Type</th>
                        <th width="15%">Date</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    pageData.forEach((contact, index) => {
        // Message Truncation
        const limit = 40;
        const isLong = contact.message.length > limit;
        const shortMessage = isLong ? contact.message.substring(0, limit) + '...' : contact.message;
        
        // Avatar Initial
        const initial = contact.name.charAt(0).toUpperCase();

        // Badge Logic
        let typeClass = 'badge-type-blog';
        const typeLower = contact.website_type.toLowerCase();
        if(typeLower.includes('shop') || typeLower.includes('commerce')) typeClass = 'badge-type-ecommerce';
        if(typeLower.includes('port')) typeClass = 'badge-type-portfolio';

        // Date Formatting
        let dateStr = contact.created_at;
        if(typeof formatDate === 'function') dateStr = formatDate(contact.created_at);

        html += `
            <tr>
                <td style="color: var(--text-light);">${startIndex + index + 1}</td>
                <td>
                    <div class="email-cell">
                        <div class="email-avatar" style="background: var(--primary-light); color: var(--primary-color);">
                            ${initial}
                        </div>
                        <div>
                            <div style="font-weight: 600;">${escapeHtml(contact.name)}</div>
                            <div style="font-size: 0.8rem; color: var(--text-light);">${escapeHtml(contact.email)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="message-preview">
                        ${escapeHtml(contact.message)}
                    </div>
                    ${isLong ? `
                        <div style="margin-top: 6px;">
                            <a href="javascript:void(0)" 
                               style="color: var(--primary-color); font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"
                               onclick='showFullMessage(${JSON.stringify(contact).replace(/'/g, "&#39;")})'>
                               <span>Read full message</span>
                               <span style="font-size: 0.7rem;">→</span>
                            </a>
                        </div>` : ''}
                </td>
                <td>
                    <a href="#" class="website-link">${escapeHtml(contact.website_name)}</a>
                </td>
                <td>
                    <span class="badge ${typeClass}">${escapeHtml(contact.website_type)}</span>
                </td>
                <td>
                    <span style="color: var(--text-light); font-size: 0.85rem;">${dateStr}</span>
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
    
    // Render pagination
    renderPagination(totalPages);
}

// Show full message in modal
function showFullMessage(contact) {
    const content = `
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="background: var(--bg-hover); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <strong>From:</strong> 
                    <span>${escapeHtml(contact.name)} &lt;${escapeHtml(contact.email)}&gt;</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <strong>Website:</strong> 
                    <span>${escapeHtml(contact.website_name)} (${escapeHtml(contact.website_type)})</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <strong>Sent:</strong> 
                    <span>${formatDate(contact.created_at)}</span>
                </div>
            </div>
            
            <div style="padding: 0 0.5rem;">
                <h5 style="margin-bottom: 0.5rem; color: var(--text-light); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px;">Message Content</h5>
                <div style="line-height: 1.6; white-space: pre-wrap; color: var(--text-dark);">${escapeHtml(contact.message)}</div>
            </div>
        </div>
    `;
    
    document.getElementById('modalMessageContent').innerHTML = content;
    
    // Open Modal (using main.js utility if available, else manual)
    if(typeof openModal === 'function') {
        openModal('messageModal');
    } else {
        document.getElementById('messageModal').classList.add('active');
    }
}

// Render pagination controls
function renderPagination(totalPages) {
    const container = document.getElementById('paginationContainer');
    
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '<div class="pagination">';
    
    // Previous button
    html += `
        <button class="page-btn ${currentPage === 1 ? 'disabled' : ''}" 
                onclick="changePage(${currentPage - 1})"
                ${currentPage === 1 ? 'disabled' : ''}>
            ← Previous
        </button>
    `;
    
    // Page logic
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (endPage - startPage < maxVisible - 1) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }
    
    if (startPage > 1) {
        html += `<button class="page-btn" onclick="changePage(1)">1</button>`;
        if (startPage > 2) html += `<span class="page-dots">...</span>`;
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <button class="page-btn ${i === currentPage ? 'active' : ''}" 
                    onclick="changePage(${i})">
                ${i}
            </button>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) html += `<span class="page-dots">...</span>`;
        html += `<button class="page-btn" onclick="changePage(${totalPages})">${totalPages}</button>`;
    }
    
    // Next button
    html += `
        <button class="page-btn ${currentPage === totalPages ? 'disabled' : ''}" 
                onclick="changePage(${currentPage + 1})"
                ${currentPage === totalPages ? 'disabled' : ''}>
            Next →
        </button>
    `;
    
    html += '</div>';
    
    container.innerHTML = html;
}

// Change page
function changePage(page) {
    if (page < 1 || page > Math.ceil(filteredContacts.length / entriesPerPage)) {
        return;
    }
    currentPage = page;
    renderTable();
    document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
}

// Local helper if main.js fails
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>