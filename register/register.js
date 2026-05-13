/**
 * Registration Page Scripts
 * Little Haven Daycare Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    const emailError = document.getElementById('email-error');
    const phoneError = document.getElementById('phone-error');
    const passwordError = document.getElementById('password-error');
    const confirmPasswordError = document.getElementById('confirm_password-error');
    const fullnameInput = document.getElementById('fullname');
    const fullnameError = document.getElementById('fullname-error');

    // --- Validation Functions ---

    const validateFullname = (name) => {
        if (!name.trim()) return "Full name is required.";
        if (name.trim().length < 3) return "Name must be at least 3 characters.";
        if (!/^[a-zA-Z\s]+$/.test(name.trim())) return "Name can only contain letters and spaces.";
        return "";
    };

    const validateEmail = (email) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) return "Email address is required.";
        if (!regex.test(email)) return "Please enter a valid email address.";
        return "";
    };

    const validatePhone = (phone) => {
        if (!phone) return "Phone number is required.";
        if (!/^\d+$/.test(phone)) return "Phone number must contain only digits.";
        if (phone.length !== 10) return "Phone number must be exactly 10 digits.";
        return "";
    };

    const validatePassword = (password) => {
        if (!password) return "Password is required.";
        if (password.length < 4) return "Password must be at least 4 characters.";
        return "";
    };

    const validateConfirmPassword = (password, confirmPassword) => {
        if (!confirmPassword) return "Please confirm your password.";
        if (password !== confirmPassword) return "Passwords do not match.";
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

    // --- Real-time Validation ---

    fullnameInput.addEventListener('input', () => {
        showError(fullnameInput, fullnameError, validateFullname(fullnameInput.value));
    });

    emailInput.addEventListener('input', () => {
        showError(emailInput, emailError, validateEmail(emailInput.value));
    });

    phoneInput.addEventListener('input', (e) => {
        // Remove non-numeric and limit to 10 chars
        let val = phoneInput.value.replace(/\D/g, '');
        if (val.length > 10) val = val.substring(0, 10);
        phoneInput.value = val;
        
        showError(phoneInput, phoneError, validatePhone(phoneInput.value));
    });

    passwordInput.addEventListener('input', () => {
        showError(passwordInput, passwordError, validatePassword(passwordInput.value));
        if (confirmPasswordInput.value) {
            showError(confirmPasswordInput, confirmPasswordError, validateConfirmPassword(passwordInput.value, confirmPasswordInput.value));
        }
    });

    confirmPasswordInput.addEventListener('input', () => {
        showError(confirmPasswordInput, confirmPasswordError, validateConfirmPassword(passwordInput.value, confirmPasswordInput.value));
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

    // --- Form Submission ---

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            const nameErr = validateFullname(fullnameInput.value);
            const emailErr = validateEmail(emailInput.value);
            const phoneErr = validatePhone(phoneInput.value);
            const passErr = validatePassword(passwordInput.value);
            const confErr = validateConfirmPassword(passwordInput.value, confirmPasswordInput.value);

            if (nameErr || emailErr || phoneErr || passErr || confErr) {
                e.preventDefault();
                showError(fullnameInput, fullnameError, nameErr);
                showError(emailInput, emailError, emailErr);
                showError(phoneInput, phoneError, phoneErr);
                showError(passwordInput, passwordError, passErr);
                showError(confirmPasswordInput, confirmPasswordError, confErr);
                
                showNotification("Please fix the errors in the form.", "error");
                return;
            }

            // Show loading state
            const btn = registerForm.querySelector('.btn-register');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Creating Account...';
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
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
        <span>${message}</span>
    `;
    
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
