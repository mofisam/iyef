document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate full name
            const fullName = document.getElementById('full_name');
            if (fullName.value.trim().length < 3) {
                isValid = false;
                fullName.classList.add('is-invalid');
                const feedback = fullName.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback';
                    div.textContent = 'Full name must be at least 3 characters';
                    fullName.parentNode.insertBefore(div, fullName.nextSibling);
                }
            } else {
                fullName.classList.remove('is-invalid');
            }
            
            // Validate email
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                isValid = false;
                email.classList.add('is-invalid');
                const feedback = email.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback';
                    div.textContent = 'Please enter a valid email address';
                    email.parentNode.insertBefore(div, email.nextSibling);
                }
            } else {
                email.classList.remove('is-invalid');
            }
            
            // Validate password
            const password = document.getElementById('password');
            if (password.value.length < 8) {
                isValid = false;
                password.classList.add('is-invalid');
                const feedback = password.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback';
                    div.textContent = 'Password must be at least 8 characters';
                    password.parentNode.insertBefore(div, password.nextSibling);
                }
            } else {
                password.classList.remove('is-invalid');
            }
            
            // Validate password confirmation
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value !== password.value) {
                isValid = false;
                confirmPassword.classList.add('is-invalid');
                const feedback = confirmPassword.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback';
                    div.textContent = 'Passwords do not match';
                    confirmPassword.parentNode.insertBefore(div, confirmPassword.nextSibling);
                }
            } else {
                confirmPassword.classList.remove('is-invalid');
            }
            
            // Validate terms checkbox
            const terms = document.getElementById('terms');
            if (!terms.checked) {
                isValid = false;
                terms.classList.add('is-invalid');
                const feedback = terms.nextElementSibling.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback';
                    div.textContent = 'You must agree to the terms';
                    terms.parentNode.insertBefore(div, terms.nextSibling.nextSibling);
                }
            } else {
                terms.classList.remove('is-invalid');
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Scroll to first error
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });
        
        // Real-time validation for password match
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        function validatePasswordMatch() {
            if (password.value && confirmPassword.value && password.value !== confirmPassword.value) {
                confirmPassword.classList.add('is-invalid');
                const feedback = confirmPassword.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback';
                    div.textContent = 'Passwords do not match';
                    confirmPassword.parentNode.insertBefore(div, confirmPassword.nextSibling);
                }
            } else {
                confirmPassword.classList.remove('is-invalid');
            }
        }
        
        password.addEventListener('input', validatePasswordMatch);
        confirmPassword.addEventListener('input', validatePasswordMatch);
    }
});