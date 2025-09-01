document.addEventListener('DOMContentLoaded', function() {
    // Character counter for testimonial content
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('charCount');
    
    if (contentTextarea && charCount) {
        contentTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Update color based on length
            if (length > 900) {
                charCount.className = 'danger';
            } else if (length > 800) {
                charCount.className = 'warning';
            } else {
                charCount.className = '';
            }
        });
        
        // Trigger initial count
        contentTextarea.dispatchEvent(new Event('input'));
    }
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate name
            const nameInput = document.getElementById('author_name');
            if (nameInput.value.trim().length < 2) {
                isValid = false;
                showError(nameInput, 'Name must be at least 2 characters');
            }
            
            // Validate email
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                isValid = false;
                showError(emailInput, 'Please enter a valid email address');
            }
            
            // Validate content
            const contentInput = document.getElementById('content');
            if (contentInput.value.trim().length < 20) {
                isValid = false;
                showError(contentInput, 'Testimonial should be at least 20 characters');
            } else if (contentInput.value.trim().length > 1000) {
                isValid = false;
                showError(contentInput, 'Testimonial should not exceed 1000 characters');
            }
            
            // Validate consent
            const consentInput = document.getElementById('consent');
            if (!consentInput.checked) {
                isValid = false;
                showError(consentInput, 'Please agree to share your testimonial');
            }
            
            // Validate file upload
            const fileInput = document.getElementById('author_image');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!allowedTypes.includes(file.type)) {
                    isValid = false;
                    showError(fileInput, 'Please upload a valid image (JPEG, PNG, GIF, or WEBP)');
                } else if (file.size > maxSize) {
                    isValid = false;
                    showError(fileInput, 'Image size must be less than 2MB');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });
    }
    
    function showError(input, message) {
        input.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = input.nextElementSibling;
        if (existingError && existingError.classList.contains('invalid-feedback')) {
            existingError.remove();
        }
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }
    
    // Real-time validation
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const error = this.nextElementSibling;
            if (error && error.classList.contains('invalid-feedback')) {
                error.remove();
            }
        });
    });
});