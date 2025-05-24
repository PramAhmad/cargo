/**
 * UI Helpers Module
 * Handles UI-related functionality and feedback
 */

import { FormatUtils } from './format-utils.js';

export const UIHelpers = {
    initSelect2() {
        $('.select2').select2({
            width: '100%',
            theme: document.documentElement.classList.contains('dark') ? 'classic' : 'default'
        });
    },
    
    initMoneyInputs() {
        FormatUtils.initMoneyInputs();
    },
    
    showToast(message, type = 'info') {
        // Remove existing toasts to prevent stacking
        $('.toast-notification').remove();
        
        // Create toast element
        const toast = $(`
            <div class="toast-notification fixed bottom-4 right-4 z-50 p-3 rounded-md shadow-lg min-w-[300px] 
                        ${type === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                          type === 'error' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 
                          'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'}">
                <div class="flex items-center">
                    <div class="mr-2">
                        ${type === 'success' ? '<i class="fas fa-check-circle text-lg"></i>' : 
                          type === 'error' ? '<i class="fas fa-exclamation-circle text-lg"></i>' : 
                          '<i class="fas fa-info-circle text-lg"></i>'}
                    </div>
                    <div>${message}</div>
                </div>
            </div>
        `);
        
        // Add to body
        $('body').append(toast);
        
        // Animate in
        toast.css('opacity', 0).animate({opacity: 1}, 300);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.animate({opacity: 0}, 300, function() {
                $(this).remove();
            });
        }, 3000);
    },
    
    showMitraLoading() {
        $('#mitra_loading').remove();
        
        const loadingHTML = `
            <div id="mitra_loading" class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-100 dark:border-blue-800/30">
                <div class="flex items-center justify-center">
                    <div class="animate-spin mr-3 h-5 w-5 text-blue-500">
                        <i class="fas fa-circle-notch"></i>
                    </div>
                    <p class="text-sm text-blue-600 dark:text-blue-400">Memuat data mitra...</p>
                </div>
            </div>
        `;
        
        $('#mitra_id').closest('.col-span-6').after(loadingHTML);
    },
    
    hideMitraLoading() {
        $('#mitra_loading').remove();
    },
    
    handleImageUploadButtonClick(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('td').find('.product-image-upload').trigger('click');
    },
    
    handleImageFileChange() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            const imgElement = $(this).closest('td').find('img');
            
            reader.onload = function(e) {
                imgElement.attr('src', e.target.result);
            };
            
            reader.readAsDataURL(file);
        }
    },
    
    showLoading() {
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                <div id="loadingOverlay" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-lg text-center">
                        <div class="animate-spin w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-3"></div>
                        <p class="text-gray-700 dark:text-gray-300">Menyimpan data...</p>
                    </div>
                </div>
            `);
        } else {
            $('#loadingOverlay').removeClass('hidden');
        }
    },
    
    hideLoading() {
        $('#loadingOverlay').addClass('hidden');
    },
    
    showSuccessMessage(message, redirectUrl) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            showCancelButton: true,
            confirmButtonText: 'Lihat Detail',
            cancelButtonText: 'Tetap di Halaman Ini',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#64748b'
        }).then((result) => {
            if (result.isConfirmed && redirectUrl) {
                window.location.href = redirectUrl;
            }
        });
    },
    
    showErrorMessage(title, message = '') {
        Swal.fire({
            icon: 'error',
            title: title,
            html: message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444'
        });
    }
};