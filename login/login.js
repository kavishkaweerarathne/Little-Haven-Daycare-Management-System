/**
 * Login Page Scripts
 * Little Haven Daycare Management System
 */
// DOMContentLoaded Event
document.addEventListener('DOMContentLoaded', () => {
    //DOM Element References
    const loginForm = document.querySelector('form');                
    const emailInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('username-error');
    const passwordError = document.getElementById('password-error');
    

    // --- Validation Functions ---

    //  Email Validation Function
    const validateEmail = (email) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;  
        if (!email) return "Email address is required.";
        if (!regex.test(email)) return "Please enter a valid email address.";
        return "";
    };

    // Password Validation Function
    const validatePassword = (password) => {
        if (!password) return "Password is required."; 
        if (password.length < 4) return "Password must be at least 4 characters.";
        return "";
    };

    // Show Error Function
    const showError = (input, errorElement, message) => {
        if (message) {
            input.classList.add('error');         // Add red border to input field
            errorElement.textContent = message;   //sets error msg
            errorElement.classList.add('show');   //shows the error msg
        } else {
            input.classList.remove('error');      // Removes the red border 
            errorElement.classList.remove('show');  // Removes the error message
            errorElement.textContent = "";        // Clears the error message
        }
    };


    // --- Real-time Validation (Input Events) ---

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


    // --- Form Submission Handler ---

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            const emailErr = validateEmail(emailInput.value);
            const passErr = validatePassword(passwordInput.value);

            if (emailErr || passErr) {
                e.preventDefault();           //// Stops form from submitting
                showError(emailInput, emailError, emailErr);  //shows email error
                showError(passwordInput, passwordError, passErr);  //shows password error
                
                // Show a main notification as well
                showNotification("Please fix the errors in the form.", "error");
                return;
            }

            // Show loading state
            const btn = loginForm.querySelector('.btn-login');  //get the sign in button
            btn.disabled = true;  //disable the sign in button
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Signing In...';  //change the sign in button to a loading spinner
            btn.style.opacity = '0.8';  //reduce the opacity of the sign in button
        });
    }


    // --- Handle URL Errors/Success ---
    const urlParams = new URLSearchParams(window.location.search);  //get URL parameters
    const error = urlParams.get('error');  //get error from URL parameters
    const success = urlParams.get('success');  //get success from URL parameters

    if (error) {
        showNotification(decodeURIComponent(error), 'error');  //show error notification
    }
    if (success) {
        showNotification(decodeURIComponent(success), 'success');  //show success notification
    }
});


/**
 * Show Premium Notification Function
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
        document.head.appendChild(style);  //add styles to the head
    }
    
    document.body.appendChild(notification);  //add notification to the body
    setTimeout(() => notification.classList.add('show'), 100);  //add show class to notification after 100ms
    setTimeout(() => {
        notification.classList.remove('show');  //remove show class from notification after 4 seconds
        setTimeout(() => notification.remove(), 500);  //remove notification after 500ms
    }, 4000);
}
