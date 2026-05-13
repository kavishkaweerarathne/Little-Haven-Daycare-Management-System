/**
 * Edit User Form Validation
 * Little Haven Daycare Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    const editUserForm = document.querySelector('form');
    const fullnameInput = document.getElementById('fullname');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    const fullnameError = document.getElementById('fullname-error');
    const emailError = document.getElementById('email-error');
    const phoneError = document.getElementById('phone-error');

    // --- Validation Functions ---

    const validateFullname = (name) => {
        if (!name.trim()) return "Full name is required.";
        if (name.trim().length < 3) return "Name must be at least 3 characters.";
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

    phoneInput.addEventListener('input', () => {
        // Remove non-numeric and limit to 10
        let val = phoneInput.value.replace(/\D/g, '');
        if (val.length > 10) val = val.substring(0, 10);
        phoneInput.value = val;
        
        showError(phoneInput, phoneError, validatePhone(phoneInput.value));
    });

    // --- Form Submission ---

    editUserForm.addEventListener('submit', (e) => {
        const nameErr = validateFullname(fullnameInput.value);
        const emailErr = validateEmail(emailInput.value);
        const phoneErr = validatePhone(phoneInput.value);

        if (nameErr || emailErr || phoneErr) {
            e.preventDefault();
            showError(fullnameInput, fullnameError, nameErr);
            showError(emailInput, emailError, emailErr);
            showError(phoneInput, phoneError, phoneErr);
            
            alert("Please fix the errors in the form before saving changes.");
            return;
        }

        // Show loading state
        const btn = editUserForm.querySelector('.btn-save');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Saving...';
        btn.style.opacity = '0.8';
    });
});
