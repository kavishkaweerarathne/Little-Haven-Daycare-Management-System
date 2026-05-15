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
            'finance': 'Finance Management',
            'inventory': 'Inventory Management',
            'settings': 'System Settings'
        };
        document.getElementById('tab-title').textContent = titles[targetTab] || 'Admin Dashboard';
    });
});

function confirmDelete(id, tab) {
    if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
        window.location.href = 'delete_user.php?id=' + id + '&tab=' + tab;
    }
}

// Function to get URL parameters
function getUrlParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Check for tab parameter on load
document.addEventListener('DOMContentLoaded', () => {
    const initialTab = getUrlParam('tab');
    if (initialTab) {
        const tabElement = document.querySelector(`.sidebar nav p[data-tab="${initialTab}"]`);
        if (tabElement) {
            tabElement.click();
        }
    }
});

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
    initSearch('finance-search', 'finance-tab');
    initSearch('inventory-search', 'inventory-tab');

    // Settings Sub-tab Switching
    const settingsNavItems = document.querySelectorAll('.settings-nav-item');
    settingsNavItems.forEach(item => {
        item.addEventListener('click', () => {
            const target = item.getAttribute('data-settings-tab');
            
            // Update active state in settings sidebar
            settingsNavItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');

            // Hide all settings content
            document.querySelectorAll('.settings-content').forEach(content => {
                content.style.display = 'none';
                content.classList.remove('active');
            });

            // Show target settings content
            const targetContent = document.getElementById('settings-' + target);
            if (targetContent) {
                targetContent.style.display = 'block';
                setTimeout(() => targetContent.classList.add('active'), 10);
            }
        });
    });

    // Settings form logic removed as it's now handled by PHP



    // Feedback Rating Stars
    const stars = document.querySelectorAll('.rating-stars i');
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.getAttribute('data-rating');
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                } else {
                    s.classList.remove('fas');
                    s.classList.add('far');
                }
            });
        });
    });


});
