/**
 * Login Page Scripts
 * Little Haven Daycare Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('form');
    const passwordInput = document.getElementById('password');

    // 2. Input Focus Animations
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.querySelector('i').style.color = 'var(--primary-dark)';
            input.parentElement.querySelector('i').style.transform = 'translateY(-50%) scale(1.1)';
        });
        
        input.addEventListener('blur', () => {
            input.parentElement.querySelector('i').style.color = 'var(--primary)';
            input.parentElement.querySelector('i').style.transform = 'translateY(-50%) scale(1)';
        });
    });

    // 3. Form Submission Loading State
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            const btn = loginForm.querySelector('.btn-login');
            const originalText = btn.innerHTML;
            
            // Basic validation
            const username = document.getElementById('username').value;
            const password = passwordInput.value;

            if (!username || !password) {
                e.preventDefault();
                return;
            }

            // Show loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Signing In...';
            btn.style.opacity = '0.8';
        });
    }


    // 5. Handle Error Messages (if passed via URL)
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    
    if (error) {
        showNotification(decodeURIComponent(error), 'error');
    }
});

/**
 * Show Premium Notification
 * @param {string} message 
 * @param {string} type 
 */
function showNotification(message, type = 'error') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add styles if not present in CSS
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%) translateY(-100px);
                background: white;
                padding: 15px 25px;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                gap: 12px;
                z-index: 9999;
                transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border-left: 4px solid var(--primary);
            }
            .notification.error { border-left-color: #ff4d4d; }
            .notification.show { transform: translateX(-50%) translateY(0); }
            .notification i { font-size: 1.2rem; }
            .notification.error i { color: #ff4d4d; }
            .notification span { font-weight: 500; color: var(--secondary); }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500);
    }, 4000);
}
