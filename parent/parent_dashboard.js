document.addEventListener('DOMContentLoaded', function() {
    // Main Tab Switching
    const mainTabs = document.querySelectorAll('.sidebar nav p');
    const tabContents = document.querySelectorAll('.tab-content');
    const tabTitle = document.getElementById('tab-title');

    mainTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.getAttribute('data-tab');
            
            // Update Sidebar
            mainTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Update Content
            tabContents.forEach(content => content.classList.remove('active'));
            document.getElementById(targetTab + '-tab').classList.add('active');

            // Update Header Title
            const titleMap = {
                'dashboard': 'Parent & Child Overview',
                'children': 'My Children',
                'activities': 'Daily Activities',
                'notifications': 'Notifications & Announcements',
                'billing': 'Billing & Payments',
                'settings': 'Account Settings'
            };
            tabTitle.textContent = titleMap[targetTab] || 'Dashboard';
        });
    });

    // Settings Tab Switching
    const settingsTabs = document.querySelectorAll('.settings-nav-item');
    const settingsContents = document.querySelectorAll('.settings-content');

    settingsTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-settings-tab');
            
            settingsTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            settingsContents.forEach(content => content.style.display = 'none');
            document.getElementById('settings-' + target).style.display = 'block';
        });
    });
});
