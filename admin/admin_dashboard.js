// Admin Dashboard JavaScript
console.log("Admin Dashboard Loaded");

// Add active class to sidebar items on click
document.querySelectorAll('.sidebar nav p').forEach(item => {
    item.addEventListener('click', () => {
        document.querySelectorAll('.sidebar nav p').forEach(p => p.classList.remove('active'));
        item.classList.add('active');
    });
});
