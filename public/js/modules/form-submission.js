
/**
 * Form Submission Handler Module
 * Handles form validation and submission
 */

import { FormatUtils } from './format-utils.js';
import { UIHelpers } from './ui-helpers.js';

export const FormSubmissionHandler = {
    state: null,
    
    init(shippingState) {
        this.state = shippingState;
    },
    
    handleFormSubmit(e) {
        e.preventDefault();
        
        // Validasi dasar
        if (!FormSubmissionHandler.validateForm()) {
            return false;
        }
        
        // Persiapkan data untuk dikirim dengan FormData untuk mendukung file upload
        const formData = new FormData(this);
        
        // Add key summary data
        formData.append('summary[total_carton]', FormatUtils.parseNumberFromFormatted($('#carton_display').val()));
        formData.append('summary[total_weight]', FormatUtils.parseNumberFromFormatted($('#gw_display').val()));
        formData.append('summary[total_volume]', FormatUtils.parseNumberFromFormatted($('#volume_display').val()));
        formData.append('summary[grand_total]', FormatUtils.parseNumberFromFormatted($('#grand_total').val()));
        
        // Add state information
        formData.append('mitra_id', FormSubmissionHandler.state.selectedMitraId);
        formData.append('warehouse_id', FormSubmissionHandler.state.selectedWarehouseId);
        formData.append('category_id', FormSubmissionHandler.state.selectedCategoryId);
        formData.append('detail_counter', FormSubmissionHandler.state.detailCounter);
        
        // Submit the form
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success(response) {
                if (response.success) {
                    UIHelpers.showToast('Form submitted successfully!', 'success');
                    // Redirect to the shipping list page or detail page
                    window.location.href = response.redirect || '/shippings';
                } else {
                    UIHelpers.showToast(response.message || 'An error occurred while submitting the form.', 'error');
                }
            },
            error(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Show validation errors
                    const errorMessages = Object.values(xhr.responseJSON.errors).flat();
                    errorMessages.forEach(message => {
                        UIHelpers.showToast(message, 'error');
                    });
                } else {
                    UIHelpers.showToast('An unexpected error occurred. Please try again.', 'error');
                }
            }
        });
    },
    
    validateForm() {
        let isValid = true;
        
        // Validate required inputs
        $('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
                
                // Show error message
                const label = $(`label[for="${$(this).attr('id')}"]`).text();
                UIHelpers.showToast(`Field "${label}" is required.`, 'error');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Validate at least one product is added
        if ($('.product-row').length === 0) {
            UIHelpers.showToast('Please add at least one product to the transaction.', 'error');
            isValid = false;
        }
        
        return isValid;
    }
};