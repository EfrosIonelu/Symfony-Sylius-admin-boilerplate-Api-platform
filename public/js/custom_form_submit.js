document.addEventListener('DOMContentLoaded', function() {
    const customForms = document.querySelectorAll('.custom-form');

    customForms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();

            const formCode = form.getAttribute('data-custom-form-code');
            const formData = new FormData(form);
            const fieldsData = {};

            // Collect all field values
            for (const [key, value] of formData.entries()) {
                if (key.startsWith('fields[')) {
                    const fieldId = key.match(/fields\[(\d+)\]/)[1];

                    // Handle multiple values (checkboxes, multi-selects)
                    if (fieldsData[fieldId]) {
                        if (Array.isArray(fieldsData[fieldId])) {
                            fieldsData[fieldId].push(value);
                        } else {
                            fieldsData[fieldId] = [fieldsData[fieldId], value];
                        }
                    } else {
                        fieldsData[fieldId] = value;
                    }
                }
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';

            // Clear previous errors
            clearFieldErrors(form);

            // Send AJAX request
            fetch('/custom-form/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    code: formCode,
                    fields: fieldsData
                })
            })
            .then(response => {
                return response.json().then(data => ({
                    status: response.status,
                    ok: response.ok,
                    data: data
                }));
            })
            .then(({status, ok, data}) => {
                if (ok && data.success) {
                    showMessage('success', data.message || 'Form submitted successfully!');
                    form.reset();
                } else if (status === 400 && data.fieldErrors) {
                    // Display field-specific errors
                    displayFieldErrors(form, data.fieldErrors);
                    showMessage('error', data.message || 'Please correct the errors below.');
                } else {
                    showMessage('error', data.message || 'Failed to submit form.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'An error occurred while submitting the form.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            });
        });
    });

    function clearFieldErrors(form) {
        // Remove all existing error messages
        const errorElements = form.querySelectorAll('.invalid-feedback');
        errorElements.forEach(el => el.remove());

        // Remove invalid classes
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => field.classList.remove('is-invalid'));
    }

    function displayFieldErrors(form, fieldErrors) {
        Object.keys(fieldErrors).forEach(fieldId => {
            const errorMessage = fieldErrors[fieldId];

            // Find the field by ID
            const field = form.querySelector(`#field_${fieldId}, [name="fields[${fieldId}]"]`);

            if (field) {
                // Add invalid class
                field.classList.add('is-invalid');

                // Create error message element
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = errorMessage;

                // Insert error message after the field or its parent container
                const formGroup = field.closest('.form-group, .form-check, fieldset');
                if (formGroup) {
                    formGroup.appendChild(errorDiv);
                } else {
                    field.parentNode.insertBefore(errorDiv, field.nextSibling);
                }
            }
        });
    }

    function showMessage(type, message) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Insert alert at the top of the form or page
        const targetForm = document.querySelector('.custom-form');
        if (targetForm && targetForm.parentNode) {
            targetForm.parentNode.insertBefore(alertDiv, targetForm);
        } else {
            document.body.insertBefore(alertDiv, document.body.firstChild);
        }

        // Auto-remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
