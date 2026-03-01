/**
 * Add Property Form JavaScript
 * Enhanced interactivity and validation
 */

(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        
        const form = document.getElementById('add-property-form');
        if (!form) return;
        
        // Form elements
        const submitBtn = form.querySelector('button[type="submit"]');
        const featuredImageInput = document.getElementById('featured_image');
        const galleryImagesInput = document.getElementById('gallery_images');
        
        // Initialize features
        initFormValidation();
        initImagePreviews();
        initCharacterCounters();
        initFormProgress();
        initTooltips();
        
        /**
         * Form Validation
         */
        function initFormValidation() {
            // Real-time validation
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    validateField(this);
                });
                
                field.addEventListener('input', function() {
                    if (this.classList.contains('error')) {
                        validateField(this);
                    }
                });
            });
            
            // Form submission
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    showNotification('Please fill in all required fields correctly.', 'error');
                    
                    // Scroll to first error
                    const firstError = form.querySelector('.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
                
                // Show loading state
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });
        }
        
        /**
         * Validate individual field
         */
        function validateField(field) {
            const value = field.value.trim();
            const fieldGroup = field.closest('.form-group');
            
            // Remove previous error
            field.classList.remove('error');
            if (fieldGroup) {
                fieldGroup.classList.remove('has-error');
                const errorMsg = fieldGroup.querySelector('.error-message');
                if (errorMsg) errorMsg.remove();
            }
            
            // Check if required and empty
            if (field.hasAttribute('required') && !value) {
                markFieldError(field, 'This field is required');
                return false;
            }
            
            // Validate email
            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    markFieldError(field, 'Please enter a valid email address');
                    return false;
                }
            }
            
            // Validate numbers
            if (field.type === 'number' && value) {
                const num = parseFloat(value);
                const min = parseFloat(field.getAttribute('min'));
                const max = parseFloat(field.getAttribute('max'));
                
                if (min !== null && num < min) {
                    markFieldError(field, `Value must be at least ${min}`);
                    return false;
                }
                
                if (max !== null && num > max) {
                    markFieldError(field, `Value must be at most ${max}`);
                    return false;
                }
            }
            
            return true;
        }
        
        /**
         * Mark field as error
         */
        function markFieldError(field, message) {
            field.classList.add('error');
            const fieldGroup = field.closest('.form-group');
            
            if (fieldGroup) {
                fieldGroup.classList.add('has-error');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = message;
                fieldGroup.appendChild(errorDiv);
            }
        }
        
        /**
         * Image Previews
         */
        function initImagePreviews() {
            // Featured image preview
            if (featuredImageInput) {
                featuredImageInput.addEventListener('change', function(e) {
                    handleImagePreview(e.target.files, 'featured');
                });
            }
            
            // Gallery images preview
            if (galleryImagesInput) {
                galleryImagesInput.addEventListener('change', function(e) {
                    handleImagePreview(e.target.files, 'gallery');
                });
            }
        }
        
        /**
         * Handle image preview
         */
        function handleImagePreview(files, type) {
            if (!files.length) return;
            
            const inputElement = type === 'featured' ? featuredImageInput : galleryImagesInput;
            const container = inputElement.closest('.form-group');
            
            // Remove existing preview
            let previewContainer = container.querySelector('.image-preview-container');
            if (previewContainer) {
                previewContainer.remove();
            }
            
            // Create new preview container
            previewContainer = document.createElement('div');
            previewContainer.className = 'image-preview-container';
            
            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'image-preview';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = file.name;
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'remove-image';
                        removeBtn.innerHTML = '×';
                        removeBtn.type = 'button';
                        removeBtn.onclick = function() {
                            previewDiv.remove();
                            if (!previewContainer.children.length) {
                                previewContainer.remove();
                            }
                        };
                        
                        previewDiv.appendChild(img);
                        previewDiv.appendChild(removeBtn);
                        previewContainer.appendChild(previewDiv);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
            
            container.appendChild(previewContainer);
        }
        
        /**
         * Character Counters
         */
        function initCharacterCounters() {
            const textareas = form.querySelectorAll('textarea');
            
            textareas.forEach(textarea => {
                const maxLength = textarea.getAttribute('maxlength');
                if (!maxLength) return;
                
                const counter = document.createElement('div');
                counter.className = 'char-counter';
                textarea.parentNode.appendChild(counter);
                
                const updateCounter = () => {
                    const remaining = maxLength - textarea.value.length;
                    counter.textContent = `${remaining} characters remaining`;
                    
                    if (remaining < 50) {
                        counter.classList.add('warning');
                    } else {
                        counter.classList.remove('warning');
                    }
                    
                    if (remaining < 0) {
                        counter.classList.add('error');
                    } else {
                        counter.classList.remove('error');
                    }
                };
                
                textarea.addEventListener('input', updateCounter);
                updateCounter();
            });
        }
        
        /**
         * Form Progress Indicator
         */
        function initFormProgress() {
            const sections = form.querySelectorAll('.form-section');
            const totalSections = sections.length;
            
            // Create progress bar
            const progressDiv = document.createElement('div');
            progressDiv.className = 'form-progress';
            progressDiv.innerHTML = `
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
                <div class="progress-text">0% Complete</div>
            `;
            
            form.insertBefore(progressDiv, form.firstChild);
            
            const progressFill = progressDiv.querySelector('.progress-fill');
            const progressText = progressDiv.querySelector('.progress-text');
            
            // Update progress on input
            const updateProgress = () => {
                const requiredFields = form.querySelectorAll('[required]');
                let filledFields = 0;
                
                requiredFields.forEach(field => {
                    if (field.value.trim()) {
                        filledFields++;
                    }
                });
                
                const percentage = Math.round((filledFields / requiredFields.length) * 100);
                progressFill.style.width = percentage + '%';
                progressText.textContent = percentage + '% Complete';
            };
            
            form.addEventListener('input', updateProgress);
            form.addEventListener('change', updateProgress);
        }
        
        /**
         * Tooltips
         */
        function initTooltips() {
            // Add tooltips to labels with data-tooltip attribute
            const labels = form.querySelectorAll('label[data-tooltip]');
            
            labels.forEach(label => {
                const tooltipText = label.getAttribute('data-tooltip');
                const tooltip = document.createElement('span');
                tooltip.className = 'tooltip';
                tooltip.innerHTML = `
                    <span class="tooltip-icon">?</span>
                    <span class="tooltip-text">${tooltipText}</span>
                `;
                label.appendChild(tooltip);
            });
        }
        
        /**
         * Show notification
         */
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `form-message ${type}`;
            notification.textContent = message;
            
            form.insertBefore(notification, form.firstChild);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
        
        /**
         * Auto-save draft (optional)
         */
        let autoSaveTimer;
        function autoSaveDraft() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                const formData = new FormData(form);
                localStorage.setItem('property_draft', JSON.stringify(Object.fromEntries(formData)));
                console.log('Draft saved');
            }, 2000);
        }
        
        // Restore draft on load
        const savedDraft = localStorage.getItem('property_draft');
        if (savedDraft) {
            try {
                const draftData = JSON.parse(savedDraft);
                Object.keys(draftData).forEach(key => {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field && field.type !== 'file') {
                        field.value = draftData[key];
                    }
                });
            } catch (e) {
                console.error('Error restoring draft:', e);
            }
        }
        
        // Enable auto-save
        form.addEventListener('input', autoSaveDraft);
        
        /**
         * Smooth scroll to sections
         */
        const sectionTitles = form.querySelectorAll('.section-title');
        sectionTitles.forEach(title => {
            title.style.cursor = 'pointer';
            title.addEventListener('click', function() {
                this.closest('.form-section').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            });
        });
        
        /**
         * Price formatting
         */
        const priceInputs = form.querySelectorAll('input[name*="price"], input[name*="fee"]');
        priceInputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value) {
                    const value = parseFloat(this.value);
                    if (!isNaN(value)) {
                        this.value = value.toFixed(2);
                    }
                }
            });
        });
        
    });
    
})();
