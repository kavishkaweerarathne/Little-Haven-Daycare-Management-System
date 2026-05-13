/**
 * Add User Form Validation
 * Little Haven Daycare Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    const addUserForm = document.querySelector('form');
    const fullnameInput = document.getElementById('fullname');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');

    const fullnameError = document.getElementById('fullname-error');
    const emailError = document.getElementById('email-error');
    const phoneError = document.getElementById('phone-error');
    const passwordError = document.getElementById('password-error');

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
        if (password.length < 4) return "Temporary password must be at least 4 characters.";
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
        // Remove numbers and special characters (Keep only letters and spaces)
        fullnameInput.value = fullnameInput.value.replace(/[^a-zA-Z\s]/g, '');
        showError(fullnameInput, fullnameError, validateFullname(fullnameInput.value));
    });

    emailInput.addEventListener('input', () => {
        showError(emailInput, emailError, validateEmail(emailInput.value));
    });

    phoneInput.addEventListener('input', () => {
        // Remove non-numeric and limit to 10
        let val = phoneInput.value.replace(/\D/g, '');
        if (val.length > 10) val = val.substring(0, 10);
        phoneInput.value = val;
        
        showError(phoneInput, phoneError, validatePhone(phoneInput.value));
    });

    passwordInput.addEventListener('input', () => {
        showError(passwordInput, passwordError, validatePassword(passwordInput.value));
    });

    // --- Form Submission ---

    addUserForm.addEventListener('submit', (e) => {
        const nameErr = validateFullname(fullnameInput.value);
        const emailErr = validateEmail(emailInput.value);
        const phoneErr = validatePhone(phoneInput.value);
        const passErr = validatePassword(passwordInput.value);

        if (nameErr || emailErr || phoneErr || passErr) {
            e.preventDefault();
            showError(fullnameInput, fullnameError, nameErr);
            showError(emailInput, emailError, emailErr);
            showError(phoneInput, phoneError, phoneErr);
            showError(passwordInput, passwordError, passErr);
            
            // Simple alert if errors exist on submit
            alert("Please fix the errors in the form before submitting.");
            return;
        }

        // Show loading state
        const btn = addUserForm.querySelector('.btn-save');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Creating User...';
        btn.style.opacity = '0.8';
    });
});
