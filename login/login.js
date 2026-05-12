/**
 * Login Page Scripts
 * Little Haven Daycare Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('form');
    const emailInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('username-error');
    const passwordError = document.getElementById('password-error');

    // --- Validation Functions ---

    const validateEmail = (email) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) return "Email address is required.";
        if (!regex.test(email)) return "Please enter a valid email address.";
        return "";
    };

    const validatePassword = (password) => {
        if (!password) return "Password is required.";
        if (password.length < 4) return "Password must be at least 4 characters.";
        
        // Example of special character check (Rule 6)
        // If you want to ensure at least one special char, uncomment below:
        // const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;
        // if (!specialCharRegex.test(password)) return "Password should contain at least one special character.";
        
        return "";
    };

    const showError = (input, errorElement, message) => {
        if (message) {
            input.classList.add('error');
            errorElement.textContent = message;
            errorElement.classList.add('show');
        } else {
            input.classList.remove('error');
            errorElement.classList.remove('show');
            errorElement.textContent = "";
        }
    };

    // --- Real-time Validation (Rule 5) ---

    emailInput.addEventListener('input', () => {
        const error = validateEmail(emailInput.value);
        showError(emailInput, emailError, error);
    });

    passwordInput.addEventListener('input', () => {
        const error = validatePassword(passwordInput.value);
        showError(passwordInput, passwordError, error);
    });

    // --- Input Focus Animations ---

    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        const icon = input.parentElement.querySelector('i');
        input.addEventListener('focus', () => {
            if (icon) {
                icon.style.color = 'var(--primary-dark)';
                icon.style.transform = 'translateY(-50%) scale(1.1)';
            }
        });
        
        input.addEventListener('blur', () => {
            if (icon) {
                icon.style.color = 'var(--primary)';
                icon.style.transform = 'translateY(-50%) scale(1)';
            }
        });
    });

    // --- Form Submission (Rule 1, 2, 3, 6) ---

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            const emailErr = validateEmail(emailInput.value);
            const passErr = validatePassword(passwordInput.value);

            if (emailErr || passErr) {
                e.preventDefault();
                showError(emailInput, emailError, emailErr);
                showError(passwordInput, passwordError, passErr);
                
                // Show a main notification as well
                showNotification("Please fix the errors in the form.", "error");
                return;
            }

            // Show loading state
            const btn = loginForm.querySelector('.btn-login');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Signing In...';
            btn.style.opacity = '0.8';
        });
    }

    // --- Handle URL Errors/Success ---
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const success = urlParams.get('success');

    if (error) {
        showNotification(decodeURIComponent(error), 'error');
    }
    if (success) {
        showNotification(decodeURIComponent(success), 'success');
    }
});

/**
 * Show Premium Notification
 */
function showNotification(message, type = 'error') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Notification Styles
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
                box-shadow: 0 15px 35px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                gap: 12px;
                z-index: 9999;
                transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border-left: 5px solid var(--primary);
            }
            .notification.error { border-left-color: #ff4d4d; }
            .notification.success { border-left-color: #2ecc71; }
            .notification.show { transform: translateX(-50%) translateY(0); }
            .notification i { font-size: 1.3rem; }
            .notification.error i { color: #ff4d4d; }
            .notification.success i { color: #2ecc71; }
            .notification span { font-weight: 600; color: var(--secondary); font-size: 0.95rem; }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 100);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500);
    }, 4000);
}
