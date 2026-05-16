
// Contact Form Validation
document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        const firstNameInput = document.getElementById('firstName');
        const lastNameInput = document.getElementById('lastName');

        // Real-time restriction: Prevent numbers and special characters as they type
        if (firstNameInput && lastNameInput) {
            [firstNameInput, lastNameInput].forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                });
            });
        }

        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();

            // Name Regex: Only letters and spaces
            const nameRegex = /^[a-zA-Z\s]+$/;
            // Email Regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!nameRegex.test(firstName)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid First Name',
                    text: 'First name should only contain letters and spaces. No numbers or special characters allowed.',
                    confirmButtonColor: '#26c6da'
                });
                return;
            }

            if (!nameRegex.test(lastName)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Last Name',
                    text: 'Last name should only contain letters and spaces. No numbers or special characters allowed.',
                    confirmButtonColor: '#26c6da'
                });
                return;
            }

            if (!emailRegex.test(email)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.',
                    confirmButtonColor: '#26c6da'
                });
                return;
            }

            // If all validations pass
            Swal.fire({
                icon: 'success',
                title: 'Message Sent!',
                text: 'Thank you for reaching out. We will get back to you soon.',
                confirmButtonColor: '#26c6da',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                contactForm.reset();
            });
        });
    }
});
