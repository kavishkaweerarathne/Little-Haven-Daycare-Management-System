// Admin Dashboard JavaScript
console.log("Admin Dashboard Loaded");

document.querySelectorAll('.sidebar nav p').forEach(item => {
    item.addEventListener('click', () => {
        const targetTab = item.getAttribute('data-tab');
        
        // Update active class in sidebar
        document.querySelectorAll('.sidebar nav p').forEach(p => p.classList.remove('active'));
        item.classList.add('active');

        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.style.display = 'none';
        });

        // Show the target tab content
        const activeTab = document.getElementById(targetTab + '-tab');
        if (activeTab) {
            activeTab.style.display = 'block';
        }

        // Update Header Title
        const titles = {
            'dashboard': 'Admin Overview',
            'staff': 'Manage Staff Members',
            'parents': 'Manage Parents',
            'children': 'Manage Children',
            'billing': 'Billing and Payments',
            'inventory': 'Inventory Management',
            'settings': 'System Settings'
        };
        document.getElementById('tab-title').textContent = titles[targetTab] || 'Admin Dashboard';
    });
});

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
        window.location.href = 'delete_user.php?id=' + id;
    }
}

// Search Filtering Logic
function initSearch(inputId, tableId) {
    const searchInput = document.getElementById(inputId);
    const table = document.querySelector(`#${tableId} table`);
    
    if (searchInput && table) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

// Initialize searches
document.addEventListener('DOMContentLoaded', () => {
    initSearch('staff-search', 'staff-tab');
    initSearch('parents-search', 'parents-tab');
    initSearch('children-search', 'children-tab');
});
