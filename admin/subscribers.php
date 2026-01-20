<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Subscribers';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
    <div class="top-header">
        <h1>Subscribers</h1>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        </div>
    </div>
    
    <div class="content-area">
        <div class="card">
            <div class="card-header">
                <h3>All Subscribers</h3>
                <div class="header-actions">
                    <span id="totalCount" class="badge badge-success">0 Total</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-controls">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search by email, country, website...">
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
                
                <div id="subscribersContainer">
                    <p class="text-center">Loading subscribers...</p>
                </div>
                
                <div id="paginationContainer" class="pagination-wrapper"></div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/main.js"></script>

<script>
let allSubscribers = [];
let filteredSubscribers = [];
let currentPage = 1;
let entriesPerPage = 10;

// Load subscribers on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSubscribers();
    
    // Search input
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterSubscribers(this.value);
    });
    
    // Entries per page
    document.getElementById('entriesPerPage').addEventListener('change', function() {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        renderTable();
    });
});

// Load subscribers function
async function loadSubscribers() {
    // Show loading spinner (from main.js)
    if(typeof showLoading === 'function') showLoading();
    
    const result = await fetchData('ajax/fetch-subscribers.php');
    
    if (result.success) {
        // Sort by ID descending (newest first)
        allSubscribers = result.data.sort((a, b) => b.id - a.id);
        filteredSubscribers = [...allSubscribers];
        
        // Update total count
        document.getElementById('totalCount').textContent = allSubscribers.length + ' Total';
        
        renderTable();
    } else {
        document.getElementById('subscribersContainer').innerHTML = 
            '<div class="text-center py-4"><p class="text-muted">Failed to load subscribers.</p></div>';
        
        if(typeof showAlert === 'function') showAlert('Failed to load data', 'danger');
    }

    // Hide loading spinner (from main.js)
    if(typeof hideLoading === 'function') hideLoading();
}

// Filter subscribers
function filterSubscribers(searchTerm) {
    searchTerm = searchTerm.toLowerCase();
    
    if (searchTerm === '') {
        filteredSubscribers = [...allSubscribers];
    } else {
        filteredSubscribers = allSubscribers.filter(sub => {
            return (
                sub.email.toLowerCase().includes(searchTerm) ||
                (sub.country && sub.country.toLowerCase().includes(searchTerm)) ||
                sub.website_name.toLowerCase().includes(searchTerm) ||
                sub.website_type.toLowerCase().includes(searchTerm)
            );
        });
    }
    
    currentPage = 1;
    renderTable();
}

// Render Table (Updated for Advanced Look)
function renderTable() {
    const container = document.getElementById('subscribersContainer');
    
    if (filteredSubscribers.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <div style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;">🔍</div>
                <h4 class="text-muted">No subscribers found</h4>
                <p class="text-muted small">Try adjusting your search criteria</p>
            </div>`;
        document.getElementById('paginationContainer').innerHTML = '';
        return;
    }
    
    // Calculate pagination
    const totalPages = Math.ceil(filteredSubscribers.length / entriesPerPage);
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = Math.min(startIndex + entriesPerPage, filteredSubscribers.length);
    const pageData = filteredSubscribers.slice(startIndex, endIndex);
    
    // Build table HTML
    let html = `
        <div class="table-info">
            Showing <b>${startIndex + 1}</b> to <b>${endIndex}</b> of <b>${filteredSubscribers.length}</b> entries
        </div>
        <div class="table-responsive">
            <table class="advanced-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="35%">Subscriber</th>
                        <th width="25%">Website</th>
                        <th width="20%">Type</th>
                        <th width="15%">Date</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    pageData.forEach((subscriber, index) => {
        // Generate Avatar Initial (First letter of email)
        const initial = subscriber.email ? subscriber.email.charAt(0).toUpperCase() : '?';
        
        // Determine badge class based on website type
        let typeClass = 'badge-type-blog'; // default
        const typeLower = subscriber.website_type ? subscriber.website_type.toLowerCase() : '';
        
        if(typeLower.includes('shop') || typeLower.includes('commerce')) {
            typeClass = 'badge-type-ecommerce';
        } else if(typeLower.includes('port')) {
            typeClass = 'badge-type-portfolio';
        }

        // Safe Date formatting
        let dateStr = subscriber.created_at;
        if(typeof formatDate === 'function') {
            dateStr = formatDate(subscriber.created_at);
        }

        html += `
            <tr>
                <td style="color: var(--text-light);">${startIndex + index + 1}</td>
                <td>
                    <div class="email-cell">
                        <div class="email-avatar">${initial}</div>
                        <div>
                            <div style="font-weight: 600;">${escapeHtml(subscriber.email)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="#" class="website-link">${escapeHtml(subscriber.website_name)}</a>
                </td>
                <td>
                    <span class="badge ${typeClass}">${escapeHtml(subscriber.website_type)}</span>
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
    
    // First Page Link
    if (startPage > 1) {
        html += `<button class="page-btn" onclick="changePage(1)">1</button>`;
        if (startPage > 2) {
            html += `<span class="page-dots">...</span>`;
        }
    }
    
    // Numbered Pages
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <button class="page-btn ${i === currentPage ? 'active' : ''}" 
                    onclick="changePage(${i})">
                ${i}
            </button>
        `;
    }
    
    // Last Page Link
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<span class="page-dots">...</span>`;
        }
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
    if (page < 1 || page > Math.ceil(filteredSubscribers.length / entriesPerPage)) {
        return;
    }
    currentPage = page;
    renderTable();
    
    // Scroll to top of table
    document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
}

// Escape HTML helper
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>