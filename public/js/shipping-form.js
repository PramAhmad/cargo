/**
 * Shipping Form Handler
 * Menangani interaksi form shipping - dibuat oleh AstaCode
 */

// Global variables
let detailCounter = 0;
let warehouseProducts = [];
let currentPage = 1;
let itemsPerPage = 10;
let filteredProducts = [];
let mitraOngkirCbm = 0;
let mitraOngkirWg = 0;

// Helper functions
function formatNumber(number) {
    if (!number) return '0,00';
    return parseFloat(number).toLocaleString('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function parseNumberFromFormatted(formattedNumber) {
    if (!formattedNumber) return 0;
    return parseFloat(formattedNumber.replace(/\./g, '').replace(',', '.'));
}

function initMoneyInputs() {
    $('.money-mask').each(function() {
        new Cleave(this, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.'
        });
    });
}

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%',
        theme: document.documentElement.classList.contains('dark') ? 'classic' : 'default'
    });
    
    // Initialize money inputs for formatting
    initMoneyInputs();
    
    // Customer selection change
// Customer selection change
$('#customer_id').on('change', function() {
    const customerId = $(this).val();
    const marketingId = $(this).find('option:selected').data('marketing-id');
    
    if (marketingId) {
        $('#marketing_id').val(marketingId).prop('disabled', false);
        $('#marketing_id').trigger('change');
    } else {
        $('#marketing_id').val('').prop('disabled', true);
        updateMarkingCode();
    }
    
    if (customerId) {
        $.get(`/api/customers/${customerId}/banks`, function(data) {
            const bankSelect = $('#bank_id');
            bankSelect.empty().append('<option value="">Pilih Bank</option>');
            
            if (data.length > 0) {
                data.forEach(bank => {
                    const isDefault = bank.is_default ? ' (Default)' : '';
                    bankSelect.append(`
                        <option value="${bank.id}" 
                                data-rek-no="${bank.rek_no || ''}" 
                                data-rek-name="${bank.rek_name || ''}"
                                ${bank.is_default ? 'selected' : ''}>
                            ${bank.bank.name} - ${bank.rek_name} - ${bank.rek_no}${isDefault}
                        </option>
                    `);
                });
                
                // Trigger change to populate rek_no and rek_name
                bankSelect.trigger('change');
            } else {
                bankSelect.append('<option value="" disabled>Customer tidak memiliki rekening bank</option>');
            }
        });
    } else {
        $('#bank_id').empty().append('<option value="">Pilih Bank</option>');
        $('#rek_no').val('');
        $('#rek_name').val('');
    }
});

// Improve bank selection change to use data attributes
$('#bank_id').on('change', function() {
    const bankId = $(this).val();
    const selectedOption = $(this).find('option:selected');
    
    if (bankId) {
        // Get values from data attributes
        const rekNo = selectedOption.data('rek-no') || '';
        const rekName = selectedOption.data('rek-name') || '';
        
        // Set values to hidden fields
        $('#rek_no').val(rekNo);
        $('#rek_name').val(rekName);
    } else {
        // Reset values
        $('#rek_no').val('');
        $('#rek_name').val('');
    }
});
    
    // Mitra selection change
    $('#mitra_id').on('change', function() {
        const mitraId = $(this).val();
        const markingCode = $(this).find('option:selected').data('marking-code') || '';
        
        $('#marking').val(markingCode);
        
        if (mitraId) {
            // Load warehouses for this mitra
            $.get(`/api/mitras/${mitraId}/warehouses`, function(data) {
                const warehouseSelect = $('#warehouse_id');
                warehouseSelect.empty().append('<option value="">Pilih Gudang</option>');
                
                data.forEach(warehouse => {
                    warehouseSelect.append(`<option value="${warehouse.id}">${warehouse.name} - ${warehouse.products_count} item</option>`);
                });
                
                warehouseSelect.prop('disabled', false);
            });
            
            // Ambil data harga ongkir dan max_wg dari mitra
            $.get(`/api/mitras/${mitraId}`, function(data) {
                // Set nilai default untuk input harga ongkir dan max_wg
                mitraOngkirCbm = parseFloat(data.harga_ongkir_cbm) || 0;
                mitraOngkirWg = parseFloat(data.harga_ongkir_wg) || 0;
                const maxWeight = parseFloat(data.max_wg) || 0;
                
                $('#harga_ongkir_cbm').val(mitraOngkirCbm);
                $('#harga_ongkir_wg').val(mitraOngkirWg);
                $('#max_weight').val(maxWeight);
                $('#max_weight_display').text(formatNumber(maxWeight));
                
                // Update tampilan kalkulasi ongkir
                updateShippingCostCalculation();
            });
            
            // Hide the warehouse products section when mitra changes
            $('#warehouseProductsSection').addClass('hidden');
            $('#warehouseProductsList').empty();
        } else {
            $('#warehouse_id').empty().append('<option value="">Pilih Gudang</option>').prop('disabled', true);
            $('#warehouseProductsSection').addClass('hidden');
            
            // Reset harga ongkir dan max_weight
            mitraOngkirCbm = 0;
            mitraOngkirWg = 0;
            $('#harga_ongkir_cbm').val(0);
            $('#harga_ongkir_wg').val(0);
            $('#max_weight').val(0);
            $('#max_weight_display').text('0,00');
        }
        
        // Update marking code
        updateMarkingCode();
    });
    
    // Warehouse selection change
    $('#warehouse_id').on('change', function() {
        const warehouseId = $(this).val();
        
        $('#barangList').empty();
        detailCounter = 0;
        calculateTotals();
        
        if (warehouseId) {
            // Tampilkan informasi warehouse
            $.get(`/api/warehouses/${warehouseId}`, function(warehouse) {
                if ($('#warehouse_info').length === 0) {
                    const warehouseInfoHTML = `
                        <div id="warehouse_info" class="mt-2 p-3 text-sm bg-blue-50 dark:bg-slate-700 rounded-md">
                            <h6 class="font-semibold mb-1">Informasi Gudang:</h6>
                            <div class="grid grid-cols-2 gap-2">
                                <div><span class="font-medium">Nama:</span> ${warehouse.name}</div>
                                <div><span class="font-medium">Tipe:</span> ${warehouse.type || 'N/A'}</div>
                                <div class="col-span-2"><span class="font-medium">Alamat:</span> ${warehouse.address || 'N/A'}</div>
                            </div>
                        </div>
                    `;
                    
                    $('#warehouse_id').closest('div').append(warehouseInfoHTML);
                } else {
                    $('#warehouse_info').html(`
                        <h6 class="font-semibold mb-1">Informasi Gudang:</h6>
                        <div class="grid grid-cols-2 gap-2">
                            <div><span class="font-medium">Nama:</span> ${warehouse.name}</div>
                            <div><span class="font-medium">Tipe:</span> ${warehouse.type || 'N/A'}</div>
                            <div class="col-span-2"><span class="font-medium">Alamat:</span> ${warehouse.address || 'N/A'}</div>
                        </div>
                    `);
                }
            });
            
            // Load products untuk warehouse ini - PERBAIKAN
            $.get(`/api/warehouses/${warehouseId}/products`, function(data) {
                console.log("Products loaded:", data.length, "items");
                warehouseProducts = data;
                filteredProducts = [...warehouseProducts];
                currentPage = 1;
                
                // Show the warehouse products section - PERBAIKAN SELECTOR
                $('#warehouseProductsSection').removeClass('hidden');
                
                // Render products with pagination
                renderProducts();
            }).fail(function(error) {
                console.error("Error loading products:", error);
            });
        } else {
            $('#warehouse_info').remove();
            $('#warehouseProductsSection').addClass('hidden');
        }
    });
    
    // Product search event
    $('#product_search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        
        if (searchTerm === '') {
            filteredProducts = [...warehouseProducts];
        } else {
            filteredProducts = warehouseProducts.filter(product => 
                product.name.toLowerCase().includes(searchTerm)
            );
        }
        
        currentPage = 1;
        renderProducts();
    });
    
    // Fee components change
    $('[data-fee="true"]').on('input', calculateFees);
    
    // PPH change
    $('#pph').on('input', calculateGrandTotal);
    
    // PPN rate change
    $('#ppn').on('input', function() {
        calculatePPN();
        calculateGrandTotal();
    });
    
    // Shipping type change - update invoice number
    $('#shipping_type').on('change', function() {
        const shippingType = $(this).val();
        $.get(`/api/shippings/generate-invoice?type=${shippingType}`, function(data) {
            $('#invoice').val(data.invoice);
        });
    });
    
    // TOP and transaction date change
    $('#top, #transaction_date').on('change', function() {
        const transactionDate = $('#transaction_date').val();
        const top = parseInt($('#top').val()) || 0;
        
        if (transactionDate && top > 0) {
            const dueDate = new Date(transactionDate);
            dueDate.setDate(dueDate.getDate() + top);
            
            const year = dueDate.getFullYear();
            const month = String(dueDate.getMonth() + 1).padStart(2, '0');
            const day = String(dueDate.getDate()).padStart(2, '0');
            
            $('#due_date').val(`${year}-${month}-${day}`);
        }
    });
    
    // Marketing change
    $('#marketing_id').on('change', updateMarkingCode);
    
    // Service change
    $('#service').on('change', updateMarkingCode);
    
    // Event handler for input harga ongkir
    $('#harga_ongkir_cbm, #harga_ongkir_wg').on('input', updateShippingCostCalculation);
    
    // Delete calculation row event delegation
    $(document).on('click', '.delete-calculation', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });
    
    // Image upload handling 
    $(document).on('click', '.upload-image-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('td').find('.product-image-upload').trigger('click');
    });
    
    $(document).on('change', '.product-image-upload', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            const imgElement = $(this).closest('td').find('img');
            
            reader.onload = function(e) {
                imgElement.attr('src', e.target.result);
            };
            
            reader.readAsDataURL(file);
        }
    });
    
    // Initialize form
    $('#shipping_type').trigger('change'); // Generate initial invoice number
    updateNilaiDisplay();
    
    // Bank selection change
    $('#bank_id').on('change', function() {
        const bankId = $(this).val();
        
        if (bankId) {
            // Ambil data bank untuk mengisi rekening
            $.get(`/api/banks/${bankId}`, function(bank) {
                if (bank) {
                    $('#rek_no').val(bank.rek_no || '');
                    $('#rek_name').val(bank.rek_name || '');
                }
            });
        } else {
            // Reset nilai
            $('#rek_no').val('');
            $('#rek_name').val('');
        }
    });
});

/**
 * Shipping Form Submission Handler
 * Menangani submit form shipping ke controller
 */

$(document).ready(function() {
    // Form submit handler
    $('#shippingForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validasi dasar
        if (!validateForm()) {
            return false;
        }
        
        // Persiapkan data untuk dikirim dengan FormData untuk mendukung file upload
        const formData = new FormData(this);
        
        // Tambahkan informasi ringkasan
        formData.append('summary[total_carton]', parseNumberFromFormatted($('#carton_display').val()));
        formData.append('summary[total_weight]', parseNumberFromFormatted($('#gw_display').val()));
        formData.append('summary[total_volume]', parseNumberFromFormatted($('#volume_display').val()));
        formData.append('summary[total_cbm]', parseNumberFromFormatted($('#cbm_display').val()));
        
        // Tambahkan informasi biaya yang dihitung
        formData.append('cost_info[biaya]', parseNumberFromFormatted($('#biaya').val()));
        formData.append('cost_info[nilai_biaya]', parseNumberFromFormatted($('#nilai_biaya').val()));
        formData.append('cost_info[biaya_kirim]', parseNumberFromFormatted($('#biaya_kirim').val()));
        formData.append('cost_info[ppn_total]', parseNumberFromFormatted($('#ppn_total').val()));
        formData.append('cost_info[grand_total]', parseNumberFromFormatted($('#grand_total').val()));
        formData.append('cost_info[shipping_method]', $('#calculation_method_used').val());
        
        // Tampilkan loading
        showLoading();
        
        // Kirim data dengan AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    showSuccessMessage(response.message || 'Data berhasil disimpan');
                    
                    // Redirect ke halaman detail jika ada redirect URL
                    if (response) {
                        setTimeout(function() {
                            window.location.href = response.redirect_url;
                        }, 1500);
                    }
                } else {
                    showErrorMessage(response.message || 'Terjadi kesalahan saat menyimpan data');
                }
            },
            error: function(xhr) {
                hideLoading();
                
                // Cek apakah response berisi validation errors
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '<ul class="list-disc pl-5">';
                    
                    for (const key in errors) {
                        errorMessage += `<li>${errors[key][0]}</li>`;
                    }
                    
                    errorMessage += '</ul>';
                    showErrorMessage('Validasi gagal:', errorMessage);
                } else {
                    showErrorMessage('Terjadi kesalahan pada server. Silakan coba lagi nanti.');
                }
            }
        });
    });
    
    // Fungsi validasi form
    function validateForm() {
        // Reset pesan error
        $('.error-message').remove();
        
        let isValid = true;
        
        // Validasi produk, pastikan setidaknya ada satu produk ditambahkan
        if ($('#barangList tr').length === 0) {
            showErrorMessage('Setidaknya satu barang harus ditambahkan ke daftar');
            isValid = false;
        }
        
        // Validasi field yang required
        $('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                const fieldName = $(this).prev('label').text().replace('*', '') || 'Field ini';
                
                $(this).addClass('border-red-500');
                $(`<div class="text-xs text-red-500 mt-1 error-message">
                    <i class="fas fa-exclamation-circle mr-1"></i>${fieldName} wajib diisi
                </div>`).insertAfter($(this));
            } else {
                $(this).removeClass('border-red-500');
            }
        });
        
        return isValid;
    }
    
    // Fungsi untuk menampilkan loading
    function showLoading() {
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
    }
    
    // Fungsi untuk menyembunyikan loading
    function hideLoading() {
        $('#loadingOverlay').addClass('hidden');
    }
    
    // Fungsi untuk menampilkan pesan sukses
    function showSuccessMessage(message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: 'success',
            title: message
        });
    }
    
    // Fungsi untuk menampilkan pesan error
    function showErrorMessage(title, message = '') {
        Swal.fire({
            icon: 'error',
            title: title,
            html: message,
            confirmButtonText: 'OK'
        });
    }
    
    // Focus pada field pertama saat form load
    $('#customer_id').focus();
});

// Render products with pagination
function renderProducts() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedProducts = filteredProducts.slice(startIndex, endIndex);
    const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
    
    const productsList = $('#warehouseProductsList');
    productsList.empty();
    
    if (paginatedProducts.length === 0) {
        productsList.append(`<tr><td colspan="4" class="text-center py-4">Tidak ada barang tersedia</td></tr>`);
    } else {
        paginatedProducts.forEach(product => {
            productsList.append(`
                <tr data-product-id="${product.id}">
                    <td>${product.name}</td>
                    <td class="text-right">${formatNumber(product.price_kg)}</td>
                    <td class="text-right">${formatNumber(product.price_cbm)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary add-product-btn" data-product-id="${product.id}">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </button>
                    </td>
                </tr>
            `);
        });
    }
    
    // Update pagination info
    $('#product-pagination-info').text(`Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredProducts.length)} dari ${filteredProducts.length} barang`);
    
    // Render pagination
    renderPagination(totalPages);
    
    // Attach event listeners to add buttons
    $('.add-product-btn').on('click', function() {
        const productId = parseInt($(this).data('product-id'));
        const product = warehouseProducts.find(p => p.id === productId);
        if (product) {
            addProductToTable(product);
        }
    });
}

// Render pagination controls
function renderPagination(totalPages) {
    const pagination = $('#product-pagination');
    pagination.empty();
    
    // Previous button
    pagination.append(`
        <button class="btn btn-sm ${currentPage === 1 ? 'btn-disabled' : 'btn-secondary'}" 
                ${currentPage === 1 ? 'disabled' : ''} data-page="prev">
            <i class="fas fa-chevron-left"></i>
        </button>
    `);
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, startPage + 4);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination.append(`
            <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-secondary'}" data-page="${i}">
                ${i}
            </button>
        `);
    }
    
    // Next button
    pagination.append(`
        <button class="btn btn-sm ${currentPage === totalPages ? 'btn-disabled' : 'btn-secondary'}" 
                ${currentPage === totalPages ? 'disabled' : ''} data-page="next">
            <i class="fas fa-chevron-right"></i>
        </button>
    `);
    
    // Attach event listeners to pagination buttons
    pagination.find('button').on('click', function() {
        if ($(this).attr('disabled')) return;
        
        const page = $(this).data('page');
        if (page === 'prev') {
            currentPage--;
        } else if (page === 'next') {
            currentPage++;
        } else {
            currentPage = page;
        }
        
        renderProducts();
    });
}

// Add a product to the "List Barang" table
function addProductToTable(product) {
    const rowIndex = detailCounter++;
    const productImageUrl = product.image_url || '/images/no-image.jpg';
    
    // Buat row produk
    const row = `
        <tr data-index="${rowIndex}" data-product-id="${product.id}" class="product-row">
            <td class="text-center" style="min-width: 80px;">
                <div class="flex flex-col items-center">
                    <img src="${productImageUrl}" alt="${product.name}" class="h-14 w-14 rounded object-cover mb-1">
                    <input type="file" name="barang[${rowIndex}][product_image]" class="hidden product-image-upload" accept="image/*">
                    <button type="button" class="btn btn-xs btn-secondary upload-image-btn">
                        <i class="fas fa-upload"></i> Ganti
                    </button>
                </div>
            </td>
            <td>
                ${product.name}
                <input type="hidden" name="barang[${rowIndex}][product_id]" value="${product.id}">
                <input type="hidden" name="barang[${rowIndex}][name]" value="${product.name}">
                <input type="hidden" name="barang[${rowIndex}][price_kg]" value="${product.price_kg || 0}">
                <input type="hidden" name="barang[${rowIndex}][price_cbm]" value="${product.price_cbm || 0}">
            </td>
            <td class="text-center">
                <input type="text" class="input input-sm" name="barang[${rowIndex}][ctn]" value="" placeholder="CTN">
            </td>
            <td class="text-center">
                <input type="number" class="input input-sm qty-per-ctn" name="barang[${rowIndex}][qty_per_ctn]" value="1" min="1" step="1">
            </td>
            <td class="text-center">
                <input type="number" class="input input-sm total-ctns" name="barang[${rowIndex}][ctns]" value="1" min="1" step="1">
            </td>
            <td class="text-center">
                <input type="text" class="input input-sm total-qty" name="barang[${rowIndex}][qty]" value="1" readonly>
            </td>
            <td class="text-center">
                <div class="flex flex-col">
                    <input type="number" class="input input-sm dimension-input" name="barang[${rowIndex}][length]" value="" min="0.01" step="0.01">
                </div>
            </td>
            <td class="text-center">
                <div class="flex flex-col">
                    <input type="number" class="input input-sm dimension-input" name="barang[${rowIndex}][width]" value="" min="0.01" step="0.01">
                </div>
            </td>
            <td class="text-center">
                <div class="flex flex-col">
                    <input type="number" class="input input-sm dimension-input" name="barang[${rowIndex}][high]" value="" min="0.01" step="0.01">
                </div>
            </td>
            <td class="text-center">
                <div class="flex flex-col">
                    <input type="number" class="input input-sm gw-per-ctn" name="barang[${rowIndex}][gw_per_ctn]" value="" min="0.01" step="0.01">
                </div>
            </td>
            <td class="text-center volume-cell">
                <div class="flex flex-col">
                    <input type="text" class="input input-sm volume-display" readonly value="0,00">
                    <input type="hidden" class="volume" name="barang[${rowIndex}][volume]" value="0">
                </div>
            </td>
            <td class="text-center weight-cell">
                <div class="flex flex-col">
                    <input type="text" class="input input-sm total-gw-display" readonly value="0,00">
                    <input type="hidden" class="total-gw" name="barang[${rowIndex}][total_gw]" value="0">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-xs btn-icon btn-danger delete-barang">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#barangList').append(row);
    $('#emptyProductState').hide();
    
    // Attach event listeners to the new row
    attachRowEventListeners($(`#barangList tr[data-index="${rowIndex}"]`));
    
    // Calculate totals
    calculateTotals();
}

// Attach event listeners to a row
function attachRowEventListeners(row) {
    // Quantity calculations
    row.find('.qty-per-ctn, .total-ctns').on('input', function() {
        const qtyPerCtn = parseFloat(row.find('.qty-per-ctn').val()) || 0;
        const totalCtns = parseFloat(row.find('.total-ctns').val()) || 0;
        const totalQty = qtyPerCtn * totalCtns;
        
        row.find('.total-qty').val(totalQty);
        
        // Recalculate GW
        calculateRowGW(row);
        
        // Recalculate Volume
        calculateRowVolume(row);
        
        // Update totals
        calculateTotals();
    });
    
    // Dimension calculations for volume
    row.find('.dimension-input').on('input', function() {
        // Calculate volume
        calculateRowVolume(row);
        
        // Update totals
        calculateTotals();
    });
    
    // GW calculations
    row.find('.gw-per-ctn').on('input', function() {
        // Calculate GW
        calculateRowGW(row);
        
        // Update totals
        calculateTotals();
    });
    
    // Delete button
    row.find('.delete-barang').on('click', function() {
        row.remove();
        
        if ($('#barangList tr').length === 0) {
            $('#emptyProductState').show();
        }
        
        calculateTotals();
    });
}

// Function to calculate row GW
function calculateRowGW(row) {
    const gwPerCtn = parseFloat(row.find('.gw-per-ctn').val()) || 0;
    const totalCtns = parseFloat(row.find('.total-ctns').val()) || 0;
    const totalGw = gwPerCtn * totalCtns;
    
    row.find('.total-gw').val(totalGw.toFixed(2));
    row.find('.total-gw-display').val(formatNumber(totalGw));
}

// Function to calculate row volume
function calculateRowVolume(row) {
    const length = parseFloat(row.find('input[name$="[length]"]').val()) || 0;
    const width = parseFloat(row.find('input[name$="[width]"]').val()) || 0;
    const height = parseFloat(row.find('input[name$="[high]"]').val()) || 0;
    const totalCtns = parseFloat(row.find('.total-ctns').val()) || 0;
    
    // Volume in cubic meters (L*W*H in cm / 1,000,000) x jumlah carton
    const volumeCbm = length * width * height * totalCtns;
    
    row.find('.volume').val(volumeCbm.toFixed(6));
    row.find('.volume-display').val(formatNumber(volumeCbm));
    
    return volumeCbm;
}

// Calculate all totals
function calculateTotals() {
    let totalCtns = 0;
    let totalQty = 0;
    let totalVolume = 0;
    let totalGw = 0;
    
    // Count from product rows
    $('.product-row').each(function() {
        const ctns = parseFloat($(this).find('.total-ctns').val()) || 0;
        const qty = parseFloat($(this).find('.total-qty').val()) || 0;
        const volume = parseFloat($(this).find('.volume').val()) || 0;
        const gw = parseFloat($(this).find('.total-gw').val()) || 0;
        
        totalCtns += ctns;
        totalQty += qty;
        totalVolume += volume;
        totalGw += gw;
    });
    
    // Update display fields
    $('#carton_display').val(formatNumber(totalCtns));
    $('#gw_display').val(formatNumber(totalGw));
    $('#volume_display').val(formatNumber(totalVolume));
    $('#cbm_display').val(formatNumber(totalVolume)); // CBM is same as volume in m³
    
    // Update kalkulasi ongkir fields
    $('#total_volume_display').text(formatNumber(totalVolume));
    $('#total_weight_display').text(formatNumber(totalGw));
    
    // Update kalkulasi ongkir
    updateShippingCostCalculation();
}

// Update shipping cost calculation
function updateShippingCostCalculation() {
    const totalVolume = parseNumberFromFormatted($('#volume_display').val()) || 0;
    const totalWeight = parseNumberFromFormatted($('#gw_display').val()) || 0;
    const hargaOngkirCbm = parseFloat($('#harga_ongkir_cbm').val()) || 0;
    const hargaOngkirWg = parseFloat($('#harga_ongkir_wg').val()) || 0;
    const maxWeight = parseFloat($('#max_weight').val()) || 0;
    
    // Hitung biaya berdasarkan volume
    const volumeCost = totalVolume * hargaOngkirCbm;
    $('#volume_cost_display').text(`Rp ${formatNumber(volumeCost)}`);
    
    // Hitung biaya berdasarkan berat
    const weightCost = totalWeight * hargaOngkirWg;
    $('#weight_cost_display').text(`Rp ${formatNumber(weightCost)}`);
    
    // Tentukan metode kalkulasi berdasarkan regulasi max_wg
    let selectedMethod = '';
    let selectedCost = 0;
    
    // Styling untuk metode yang dipilih
    $('.bg-blue-50').removeClass('border-blue-500 border-2');
    $('.bg-green-50').removeClass('border-green-500 border-2');
    
    // Otomatis tentukan berdasarkan regulasi max_wg
    if (maxWeight === 0) {
        // Jika max_weight tidak ditentukan (0), gunakan perhitungan yang lebih tinggi
        if (volumeCost >= weightCost) {
            selectedMethod = 'volume';
            selectedCost = volumeCost;
            $('.bg-blue-50').addClass('border-blue-500 border-2');
        } else {
            selectedMethod = 'weight';
            selectedCost = weightCost;
            $('.bg-green-50').addClass('border-green-500 border-2');
        }
    } else if (totalWeight <= maxWeight) {
        // Jika total berat <= max_weight, gunakan perhitungan CBM
        selectedMethod = 'volume';
        selectedCost = volumeCost;
        $('.bg-blue-50').addClass('border-blue-500 border-2');
    } else {
        // Jika total berat > max_weight, gunakan perhitungan berat
        selectedMethod = 'weight';
        selectedCost = weightCost;
        $('.bg-green-50').addClass('border-green-500 border-2');
    }
    
    // Update message dan total cost
    let message = '';
    if (selectedMethod === 'volume') {
        message = `<span class="text-blue-700 font-medium">Menggunakan perhitungan Volume</span>: ${formatNumber(totalVolume)} m³ × Rp ${formatNumber(hargaOngkirCbm)} = <span class="font-bold">Rp ${formatNumber(selectedCost)}</span><br>`;
        
        if (maxWeight === 0) {
            message += `<span class="mt-1 block text-xs opacity-80">Alasan: Tidak ada batas maksimum berat yang ditentukan dan nilai ongkir volume lebih tinggi</span>`;
        } else {
            message += `<span class="mt-1 block text-xs opacity-80">Alasan: Berat total ${formatNumber(totalWeight)} kg masih di bawah batas maksimum ${formatNumber(maxWeight)} kg</span>`;
        }
    } else {
        message = `<span class="text-green-700 font-medium">Menggunakan perhitungan Berat</span>: ${formatNumber(totalWeight)} kg × Rp ${formatNumber(hargaOngkirWg)} = <span class="font-bold">Rp ${formatNumber(selectedCost)}</span><br>`;
        
        if (maxWeight === 0) {
            message += `<span class="mt-1 block text-xs opacity-80">Alasan: Tidak ada batas maksimum berat yang ditentukan dan nilai ongkir berat lebih tinggi</span>`;
        } else {
            message += `<span class="mt-1 block text-xs opacity-80">Alasan: Berat total ${formatNumber(totalWeight)} kg melebihi batas maksimum ${formatNumber(maxWeight)} kg</span>`;
        }
    }
    
    // Update UI untuk perhitungan yang digunakan
    $('#used_calculation_message').html(message);
    $('#selected_shipping_cost').val(formatNumber(selectedCost));
    $('#calculation_method_used').val(selectedMethod);
    
    // Otomatis update biaya kirim (tanpa perlu tombol apply)
    $('#jkt_sda').val(formatNumber(selectedCost));
    
    // Hitung ulang semua biaya
    calculateFees();
}

// Calculate fees and total biaya
function calculateFees() {
    let totalFees = 0;
    
    $('[data-fee="true"]').each(function() {
        totalFees += parseNumberFromFormatted($(this).val()) || 0;
    });
    
    // Biaya is the total of all fees
    $('#biaya').val(formatNumber(totalFees));
    
    // Calculate nilai_biaya
    const nilaiBarang = parseNumberFromFormatted($('#nilai').val()) || 0;
    $('#nilai_biaya').val(formatNumber(nilaiBarang + totalFees));
    
    // Update biaya kirim
    $('#biaya_kirim').val(formatNumber(totalFees));
    
    // Update nilai display
    updateNilaiDisplay();
    
    // Update PPN and grand total
    calculatePPN();
    calculateGrandTotal();
}

// Calculate PPN total
function calculatePPN() {
    const biayaKirim = parseNumberFromFormatted($('#biaya_kirim').val()) || 0;
    const ppnRate = parseFloat($('#ppn').val()) || 0;
    const ppnAmount = biayaKirim * (ppnRate / 100);
    
    $('#ppn_total').val(formatNumber(ppnAmount));
}

// Calculate grand total
function calculateGrandTotal() {
    const biayaKirim = parseNumberFromFormatted($('#biaya_kirim').val()) || 0;
    const pph = parseNumberFromFormatted($('#pph').val()) || 0;
    const ppnTotal = parseNumberFromFormatted($('#ppn_total').val()) || 0;
    
    const grandTotal = biayaKirim + pph + ppnTotal;
    $('#grand_total').val(formatNumber(grandTotal));
}

// Update marking code
function updateMarkingCode() {
    const mitraCode = $('#mitra_id option:selected').data('marking-code') || '';
    const marketingCode = $('#marketing_id option:selected').data('code') || '';
    const serviceType = $('#service').val() || '';
    
    let markingCode = '';
    
    if (mitraCode || marketingCode || serviceType) {
        const parts = [];
        if (mitraCode) parts.push(mitraCode);
        if (marketingCode) parts.push(marketingCode);
        if (serviceType) parts.push(serviceType);
        
        markingCode = parts.join('/');
    }
    
    $('#marking').val(markingCode);
}

// Update nilai display
function updateNilaiDisplay() {
    const nilaiValue = parseNumberFromFormatted($('#nilai').val()) || 0;
    $('#nilai_simple').text('Rp ' + formatNumber(nilaiValue));
}