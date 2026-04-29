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
