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
let mitraCategories = [];
let selectedMitraId = null;
let selectedWarehouseId = null;
let selectedCategoryId = null;

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
    
    // Load tax data
    loadTaxData();
    
    // Customer selection change
    $('#customer_id').on('change', function() {
        const customerId = $(this).val();
        const marketingId = $(this).find('option:selected').data('marketing-id');
        
        if (marketingId) {
            $('#marketing_id').val(marketingId).prop('disabled', true);
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

    // Bank selection change
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
        
        selectedMitraId = mitraId;
        $('#marking').val(markingCode);
        
        if (mitraId) {
            // Reset the warehouse selection and product display
            $('#warehouse_id').empty().append('<option value="">Pilih Gudang</option>').prop('disabled', true);
            $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
            $('#warehouseProductsSection').addClass('hidden');
            $('#warehouseProductsList').empty();
            $('#mitra_categories_section').remove();
            
            // Show loading indicator
            showMitraLoading();
            
            // Load warehouses for this mitra
            $.get(`/api/mitras/${mitraId}/warehouses`, function(warehouses) {
                const warehouseSelect = $('#warehouse_id');
                warehouseSelect.empty().append('<option value="">Pilih Gudang</option>');
                
                if (warehouses.length > 0) {
                    warehouses.forEach(warehouse => {
                        warehouseSelect.append(`<option value="${warehouse.id}">${warehouse.name} (${warehouse.type}) - ${warehouse.products_count} item</option>`);
                    });
                    
                    warehouseSelect.prop('disabled', false);
                } else {
                    warehouseSelect.append('<option value="" disabled>Tidak ada gudang tersedia</option>');
                }
            });
            
            // Load categories for this mitra
            $.get(`/api/mitras/${mitraId}/categories`, function(categories) {
                mitraCategories = categories;
                
                // Display categories section
                if (categories.length > 0) {
                    displayMitraCategories(categories);
                }
            });
            
            // Get mitra data
            $.get(`/api/mitras/${mitraId}`, function(data) {
                // Remove loading once all data is fetched
                hideMitraLoading();
                
                // Set max weight value
                const maxWeight = parseFloat(data.max_wg) || 0;
                $('#max_weight').val(maxWeight);
                $('#max_weight_display').text(formatNumber(maxWeight));
            });
        } else {
            // Reset everything if no mitra selected
            selectedMitraId = null;
            $('#warehouse_id').empty().append('<option value="">Pilih Gudang</option>').prop('disabled', true);
            $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
            $('#warehouseProductsSection').addClass('hidden');
            $('#mitra_categories_section').remove();
            
            // Reset max weight
            $('#max_weight').val(0);
            $('#max_weight_display').text('0,00');
        }
        
        // Update marking code
        updateMarkingCode();
    });
    
    // Warehouse selection change
    $('#warehouse_id').on('change', function() {
        const warehouseId = $(this).val();
        selectedWarehouseId = warehouseId;
        
        // Reset category selection and product display
        $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
        $('#barangList').empty();
        detailCounter = 0;
        calculateTotals();
        
        if (warehouseId) {
            // Get warehouse details with categories
            $.get(`/api/warehouses/${warehouseId}`, function(response) {
                // Display warehouse info
                if ($('#warehouse_info').length === 0) {
                    const warehouseInfoHTML = `
                        <div id="warehouse_info" class="mt-2 p-3 text-sm bg-blue-50 dark:bg-slate-700 rounded-md">
                            <h6 class="font-semibold mb-1">Informasi Gudang:</h6>
                            <div class="grid grid-cols-2 gap-2">
                                <div><span class="font-medium">Nama:</span> ${response.warehouse.name}</div>
                                <div><span class="font-medium">Tipe:</span> ${response.warehouse.type || 'N/A'}</div>
                                <div class="col-span-2"><span class="font-medium">Alamat:</span> ${response.warehouse.address || 'N/A'}</div>
                            </div>
                        </div>
                    `;
                    
                    $('#warehouse_id').closest('div').append(warehouseInfoHTML);
                } else {
                    $('#warehouse_info').html(`
                        <h6 class="font-semibold mb-1">Informasi Gudang:</h6>
                        <div class="grid grid-cols-2 gap-2">
                            <div><span class="font-medium">Nama:</span> ${response.warehouse.name}</div>
                            <div><span class="font-medium">Tipe:</span> ${response.warehouse.type || 'N/A'}</div>
                            <div class="col-span-2"><span class="font-medium">Alamat:</span> ${response.warehouse.address || 'N/A'}</div>
                        </div>
                    `);
                }
                
                // Highlight available categories for this warehouse
                highlightWarehouseCategories(response.categories);
                
                // Populate category select dropdown
                const categorySelect = $('#category_id');
                categorySelect.empty().append('<option value="">Pilih Kategori</option>');
                
                if (response.categories && response.categories.length > 0) {
                    response.categories.forEach(category => {
                        categorySelect.append(`
                            <option value="${category.id}" 
                                    data-price-cbm="${category.mit_price_cbm}" 
                                    data-price-kg="${category.mit_price_kg}">
                                ${category.name} - Rp ${formatNumber(category.mit_price_cbm)}/CBM, Rp ${formatNumber(category.mit_price_kg)}/KG
                            </option>
                        `);
                    });
                    
                    categorySelect.prop('disabled', false);
                } else {
                    categorySelect.append('<option value="" disabled>Tidak ada kategori tersedia</option>');
                }
            });
        } else {
            selectedWarehouseId = null;
            $('#warehouse_info').remove();
            $('#warehouseProductsSection').addClass('hidden');
        }
    });
    

$('#category_id').on('change', function() {
    const categoryId = $(this).val();
    selectedCategoryId = categoryId;
    
    if (categoryId && selectedWarehouseId) {
        // Get service type from select
        const serviceType = $('#service').val() || 'SEA';
        const serviceTypeLower = serviceType.toLowerCase();
        
        // Show selected category info with service type
        $('#categoryName').text($(this).find('option:selected').text().split(' - ')[0]);
        $('#serviceDisplay').text(serviceType);
        $('#serviceTypeDisplay').text(serviceType);
        
        if (serviceType === 'SEA') {
            $('#serviceDisplay').removeClass('badge-soft-warning').addClass('badge-soft-primary');
            $('#serviceDisplay').html('<i class="fas fa-ship mr-1"></i> SEA (Laut)');
        } else {
            $('#serviceDisplay').removeClass('badge-soft-primary').addClass('badge-soft-warning');
            $('#serviceDisplay').html('<i class="fas fa-plane mr-1"></i> AIR (Udara)');
        }
        
        // Get pricing fields based on service type
        const priceCbmField = `price-cbm-${serviceTypeLower}`;
        const priceKgField = `price-kg-${serviceTypeLower}`;
        const custPriceCbmField = `cust-price-cbm-${serviceTypeLower}`;
        const custPriceKgField = `cust-price-kg-${serviceTypeLower}`;
        
        const priceCbm = parseFloat($(this).find('option:selected').data(priceCbmField)) || 0;
        const priceKg = parseFloat($(this).find('option:selected').data(priceKgField)) || 0;
        const custPriceCbm = parseFloat($(this).find('option:selected').data(custPriceCbmField)) || 0;
        const custPriceKg = parseFloat($(this).find('option:selected').data(custPriceKgField)) || 0;
        
        // Update pricing inputs
        $('#harga_ongkir_cbm').val(priceCbm);
        $('#harga_ongkir_wg').val(priceKg);
        
        // Update pricing display
        $('#categoryPriceCbm').text(`Rp ${formatNumber(priceCbm)}`);
        $('#categoryPriceKg').text(`Rp ${formatNumber(priceKg)}`);
        $('#categoryCustPriceCbm').text(`Rp ${formatNumber(custPriceCbm)}`);
        $('#categoryCustPriceKg').text(`Rp ${formatNumber(custPriceKg)}`);
        $('#selectedCategoryInfo').removeClass('hidden');
        
        // Show products section and load products
        $('#warehouseProductsSection').removeClass('hidden');
        $('#warehouseProductsList').html('<tr><td colspan="4" class="text-center py-4"><i class="fas fa-circle-notch fa-spin mr-2"></i> Memuat data produk...</td></tr>');
        
        // Include service type in API call
        $.get(`/api/warehouses/${selectedWarehouseId}/categories/${categoryId}/products?service=${serviceType}`, function(products) {
            console.log("Products loaded:", products);
            
            // Process products for display
            warehouseProducts = products.map(product => {
                return {
                    ...product,
                    categoryName: $('#category_id option:selected').text().split(' - ')[0],
                    price_cbm: product.price_cbm || 0,
                    price_kg: product.price_kg || 0,
                    cust_price_cbm: product.cust_price_cbm || 0,
                    cust_price_kg: product.cust_price_kg || 0
                };
            });
            
            // Initialize display
            filteredProducts = [...warehouseProducts];
            currentPage = 1;
            renderProducts();
            
            // Reset product search
            $('#product_search').val('');
        }).fail(function(error) {
            console.error("Error loading products:", error);
            $('#warehouseProductsList').html('<tr><td colspan="4" class="text-center py-4 text-red-500"><i class="fas fa-exclamation-circle mr-2"></i> Gagal memuat data produk</td></tr>');
        });
    } else {
        $('#warehouseProductsSection').addClass('hidden');
        $('#selectedCategoryInfo').addClass('hidden');
    }
});
// Update the service type change handler to refresh category pricing
$('#service').on('change', function() {
    const serviceType = $(this).val() || 'SEA';
    updateMarkingCode();
    
    // If warehouse is already selected, reload the categories with new service type
    if ($('#warehouse_id').val()) {
        $('#warehouse_id').trigger('change');
    }
    
    // If category is already selected, update the displayed pricing
    if ($('#category_id').val()) {
        $('#category_id').trigger('change');
    }
    
    // Update service type display in selected category info if visible
    if (!$('#selectedCategoryInfo').hasClass('hidden')) {
        $('#serviceTypeDisplay').text(serviceType);
        
        if (serviceType === 'SEA') {
            $('#serviceDisplay').removeClass('badge-soft-warning').addClass('badge-soft-primary');
            $('#serviceDisplay').html('<i class="fas fa-ship mr-1"></i> SEA (Laut)');
        } else {
            $('#serviceDisplay').removeClass('badge-soft-primary').addClass('badge-soft-warning');
            $('#serviceDisplay').html('<i class="fas fa-plane mr-1"></i> AIR (Udara)');
        }
    }
});
// Update warehouse selection change handler to include service-specific pricing
$('#warehouse_id').on('change', function() {
    const warehouseId = $(this).val();
    selectedWarehouseId = warehouseId;
    
    // Reset dependent fields
    $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
    $('#categoryInfoBtn').addClass('hidden');
    $('#selectedCategoryInfo').addClass('hidden');
    $('#warehouseProductsSection').addClass('hidden');
    $('#warehouseProductsList').empty();
    $('#barangList').empty();
    $('#warehouse_category_container').empty();
    detailCounter = 0;
    
    if (warehouseId) {
        // Get service type from select
        const serviceType = $('#service').val() || 'SEA';
        const serviceTypeLower = serviceType.toLowerCase();
        
        // Show loading indicator for categories
        const loadingHtml = '<option value="">Loading...</option>';
        $('#category_id').html(loadingHtml);
        
        // Show loading in warehouse info container
        $('#warehouse_category_container').html(`
            <div class="p-3 bg-blue-50 dark:bg-slate-700 rounded-md flex items-center justify-center w-full">
                <div class="animate-spin mr-3 h-5 w-5 text-blue-500">
                    <i class="fas fa-circle-notch"></i>
                </div>
                <p class="text-sm text-blue-600 dark:text-blue-400">Memuat informasi gudang...</p>
            </div>
        `);
        
        // Include service type in API call
        $.get(`/api/warehouses/${warehouseId}?service=${serviceType}`, function(response) {
            // Display warehouse info in the new container
            const warehouseInfoHTML = `
                <div id="warehouse_info" class="p-3 text-sm bg-blue-50 dark:bg-slate-700 rounded-md w-full mb-3">
                    <h6 class="font-semibold mb-2">Informasi Gudang:</h6>
                    <div class="grid grid-cols-1 gap-2">
                        <div><span class="font-medium">Nama:</span> ${response.warehouse.name}</div>
                        <div><span class="font-medium">Tipe:</span> ${response.warehouse.type || 'N/A'}</div>
                        <div><span class="font-medium">Alamat:</span> ${response.warehouse.address || 'N/A'}</div>
                        <div><span class="font-medium">Layanan:</span> ${serviceType}</div>
                    </div>
                </div>
            `;
            
            $('#warehouse_category_container').html(warehouseInfoHTML);
            
            // Display mitra categories with service type
            if (mitraCategories.length > 0) {
                displayMitraCategories(mitraCategories, serviceTypeLower);
                
                // Highlight available categories
                highlightWarehouseCategories(response.categories);
            }
            
            // Load categories for the dropdown
            if (response.categories && response.categories.length > 0) {
                $('#category_id').empty().append('<option value="">Pilih Kategori</option>');
                
                response.categories.forEach(category => {
                    // Use the field names that match our service type
                    const mitPriceCbmField = `mit_price_cbm_${serviceTypeLower}`;
                    const mitPriceKgField = `mit_price_kg_${serviceTypeLower}`;
                    const custPriceCbmField = `cust_price_cbm_${serviceTypeLower}`;
                    const custPriceKgField = `cust_price_kg_${serviceTypeLower}`;
                    
                    const mitPriceCbm = category[mitPriceCbmField] || 0;
                    const mitPriceKg = category[mitPriceKgField] || 0;
                    const custPriceCbm = category[custPriceCbmField] || 0;
                    const custPriceKg = category[custPriceKgField] || 0;
                    
                    $('#category_id').append(`
                        <option value="${category.id}" 
                                data-price-cbm-${serviceTypeLower}="${mitPriceCbm}" 
                                data-price-kg-${serviceTypeLower}="${mitPriceKg}"
                                data-cust-price-cbm-${serviceTypeLower}="${custPriceCbm}" 
                                data-cust-price-kg-${serviceTypeLower}="${custPriceKg}">
                            ${category.name} - ${serviceType} - Rp ${formatNumber(mitPriceCbm)}/CBM, Rp ${formatNumber(mitPriceKg)}/KG
                        </option>
                    `);
                });
                
                $('#category_id').prop('disabled', false);
                $('#categoryInfoBtn').removeClass('hidden');
            } else {
                $('#category_id').empty().append('<option value="">Tidak ada kategori tersedia</option>');
                
                // Show no categories message
                $('#warehouse_category_container').append(`
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-md w-full">
                        <p class="text-sm text-gray-500 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tidak ada kategori tersedia untuk gudang ini dengan layanan ${serviceType}
                        </p>
                    </div>
                `);
            }
        }).fail(function(error) {
            console.error("Error loading warehouse details:", error);
            $('#warehouse_category_container').html(`
                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-md w-full">
                    <p class="text-sm text-red-600 dark:text-red-400 text-center">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Gagal memuat informasi gudang
                    </p>
                </div>
            `);
            $('#category_id').empty().append('<option value="">Error memuat kategori</option>');
        });
    } else {
        // Reset everything if no warehouse selected
        resetWarehouseAndCategorySection();
    }
    
    calculateTotals();
});
// Update the displayMitraCategories function to include service type information
function displayMitraCategories(categories, serviceType = 'sea') {
    // Remove existing section if any
    $('#mitra_categories_section').remove();
    
    const categoryHTML = `
        <div id="mitra_categories_section" class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-md border border-amber-100 dark:border-amber-800/30 w-full">
            <h6 class="text-sm font-medium text-amber-800 dark:text-amber-400 mb-2">
                <i class="fas fa-tags mr-1"></i> Kategori Mitra - ${serviceType.toUpperCase()} (${categories.length})
            </h6>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 w-full">
                ${categories.map(category => `
                    <div class="text-xs bg-white dark:bg-slate-800 p-3 rounded flex justify-between items-center relative w-full border border-amber-100 dark:border-slate-700 hover:bg-amber-50 dark:hover:bg-slate-700/50">
                        <span class="font-medium text-sm">${category.name}</span>
                        <div class="flex flex-col items-end">
                            <span class="text-gray-600 dark:text-gray-300">Rp ${formatNumber(category[`mit_price_cbm_${serviceType}`] || 0)}/CBM</span>
                            <span class="text-gray-600 dark:text-gray-300">Rp ${formatNumber(category[`mit_price_kg_${serviceType}`] || 0)}/KG</span>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    // Place after warehouse selection in a full-width container
    $('#warehouse_category_container').html(categoryHTML);
}
    
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
    
    // Event handler for harga ongkir inputs
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
    
    // Load tax data
    loadTaxData();
});

/**
 * Shipping Form Submission Handler
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
        
        // Tambahkan informasi kategori yang dipilih
        formData.append('category_id', selectedCategoryId || '');
        
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
                    showSuccessMessage(response.message || 'Data berhasil disimpan', response.redirect_url);
                } else {
                    showErrorMessage(response.message || 'Terjadi kesalahan saat menyimpan data');
                }
            },
            error: function(xhr) {
                hideLoading();
                
                // Cek apakah response berisi validation errors
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '<div class="text-left"><ul class="list-disc pl-5 space-y-1">';
                    
                    for (const key in errors) {
                        errorMessage += `<li class="text-red-600">${errors[key][0]}</li>`;
                    }
                    
                    errorMessage += '</ul></div>';
                    showErrorMessage('Validasi gagal', errorMessage);
                } else {
                    showErrorMessage(
                        'Terjadi kesalahan', 
                        xhr.responseJSON?.message || 'Terjadi kesalahan pada server. Silakan coba lagi nanti.'
                    );
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
        
        // Validasi kategori, pastikan kategori dipilih
        if (!selectedCategoryId) {
            $('#category_id').addClass('border-red-500');
            $(`<div class="text-xs text-red-500 mt-1 error-message">
                <i class="fas fa-exclamation-circle mr-1"></i>Kategori wajib dipilih
            </div>`).insertAfter($('#category_id'));
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
    function showSuccessMessage(message, redirectUrl) {
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
    }
    
    // Fungsi untuk menampilkan pesan error
    function showErrorMessage(title, message = '') {
        Swal.fire({
            icon: 'error',
            title: title,
            html: message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444'
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
                    <td>
                        <div class="flex flex-col">
                            <span class="font-medium">${product.name}</span>
                            <span class="text-xs text-gray-500">
                                <span class="badge badge-soft-primary">${product.categoryName || 'Tanpa Kategori'}</span>
                            </span>
                        </div>
                    </td>
                    <td class="text-right">Rp ${formatNumber(product.price_kg)}</td>
                    <td class="text-right">Rp ${formatNumber(product.price_cbm)}</td>
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
            
            // Highlight the corresponding row in the barangList if it exists
            setTimeout(() => {
                const existingRow = $('#barangList').find(`tr[data-product-id="${productId}"]`);
                if (existingRow.length > 0) {
                    existingRow.addClass('bg-green-50 dark:bg-green-900/20');
                    setTimeout(() => {
                        existingRow.removeClass('bg-green-50 dark:bg-green-900/20');
                    }, 1500);
                }
            }, 100);
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
    // First check if the product already exists in the table
    const existingRow = $('#barangList').find(`tr[data-product-id="${product.id}"]`);
    
    if (existingRow.length > 0) {
        // Product already exists, just increment the quantity instead of adding a new row
        const currentCtns = parseInt(existingRow.find('.total-ctns').val()) || 1;
        existingRow.find('.total-ctns').val(currentCtns + 1).trigger('input');
        
        // Show feedback to user
        showToast(`Jumlah ${product.name} ditambahkan`, 'success');
        return;
    }
    
    // If product doesn't exist yet, continue with adding a new row
    const rowIndex = detailCounter++;
    const productImageUrl = product.image_url || '/noimage.jpg';
    
    // Buat row produk dengan informasi kategori
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
                <div class="flex flex-col">
                    <span class="font-medium">${product.name}</span>
                    <span class="text-xs text-gray-500">
                        <span class="badge badge-soft-primary">${product.categoryName || 'Tanpa Kategori'}</span>
                    </span>
                </div>
                <input type="hidden" name="barang[${rowIndex}][product_id]" value="${product.id}">
                <input type="hidden" name="barang[${rowIndex}][name]" value="${product.name}">
                <input type="hidden" name="barang[${rowIndex}][price_kg]" value="${product.price_kg || 0}">
                <input type="hidden" name="barang[${rowIndex}][price_cbm]" value="${product.price_cbm || 0}">
                <input type="hidden" name="barang[${rowIndex}][category_id]" value="${selectedCategoryId || 0}">
                <input type="hidden" name="barang[${rowIndex}][category_name]" value="${product.categoryName || ''}">
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
    
    // Show feedback to user
    showToast(`${product.name} ditambahkan ke daftar`, 'success');
}

// Add a simple toast notification function
function showToast(message, type = 'info') {
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
    const volumeCbm = (length * width * height / 1000000) * totalCtns;
    
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
    
    // Get pricing from currently selected category (via input fields)
    const priceCbm = parseFloat($('#harga_ongkir_cbm').val()) || 0;
    const priceKg = parseFloat($('#harga_ongkir_wg').val()) || 0;
    
    // Calculate costs
    const volumeCost = totalVolume * priceCbm;
    const weightCost = totalWeight * priceKg;
    
    // Display the costs
    $('#volume_cost_display').text(`Rp ${formatNumber(volumeCost)}`);
    $('#weight_cost_display').text(`Rp ${formatNumber(weightCost)}`);
    
    // Get max weight from mitra
    const maxWeight = parseFloat($('#max_weight').val()) || 0;
    
    // Determine calculation method
    let selectedMethod = '';
    let selectedCost = 0;
    
    // Styling for selected method
    $('.bg-blue-50').removeClass('border-blue-500 border-2');
    $('.bg-green-50').removeClass('border-green-500 border-2');
    
    // Automatically determine based on regulation
    if (maxWeight === 0) {
        // If max_weight not set, use the higher cost calculation
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
        // If weight is under max, use volume calculation
        selectedMethod = 'volume';
        selectedCost = volumeCost;
        $('.bg-blue-50').addClass('border-blue-500 border-2');
    } else {
        // If weight exceeds max, use weight calculation
        selectedMethod = 'weight';
        selectedCost = weightCost;
        $('.bg-green-50').addClass('border-green-500 border-2');
    }
    
    // Update message and total cost
    let message = '';
    if (selectedMethod === 'volume') {
        message = `<span class="text-blue-700 font-medium">Menggunakan perhitungan Volume</span>: 
                   Total Biaya CBM = <span class="font-bold">Rp ${formatNumber(volumeCost)}</span><br>`;
        
        if (maxWeight === 0) {
            message += `<span class="mt-1 block text-xs opacity-80">Alasan: Tidak ada batas maksimum berat yang ditentukan dan nilai ongkir volume lebih tinggi</span>`;
        } else {
            message += `<span class="mt-1 block text-xs opacity-80">Alasan: Berat total ${formatNumber(totalWeight)} kg masih di bawah batas maksimum ${formatNumber(maxWeight)} kg</span>`;
        }
    } else {
        message = `<span class="text-green-700 font-medium">Menggunakan perhitungan Berat</span>: 
                   Total Biaya KG = <span class="font-bold">Rp ${formatNumber(weightCost)}</span><br>`;
        
        if (maxWeight === 0) {
            message += `<span class="mt-1 block text-xs opacity-80">Alasan: Tidak ada batas maksimum berat yang ditentukan dan nilai ongkir berat lebih tinggi</span>`;
        } else {
            message += `<span class="mt-1 block text-xs opacity-80">Alasan: Berat total ${formatNumber(totalWeight)} kg melebihi batas maksimum ${formatNumber(maxWeight)} kg</span>`;
        }
    }
    
    // Update UI elements
    $('#used_calculation_message').html(message);
    $('#selected_shipping_cost').val(formatNumber(selectedCost));
    $('#calculation_method_used').val(selectedMethod);
    
    // Automatically update shipping cost (no need for apply button)
    $('#jkt_sda').val(formatNumber(selectedCost));
    
    // Recalculate all fees
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
    
    const customerCode = $('#customer_id option:selected').data('code') || '';
    
    let markingCode = '';
    
    if (mitraCode) {
        const parts = [];
        parts.push(mitraCode);
        parts.push('WMLC'); 
        if (marketingCode) parts.push(marketingCode);
        if (customerCode) parts.push(customerCode);
        if (serviceType) parts.push(serviceType);
        
        markingCode = parts.join('/');
    }
    
    $('#marking').val(markingCode);
}

// Update nilai display
function updateNilaiDisplay() {
    const nilaiValue = parseNumberFromFormatted($('#nilai').val()) || 0;
    $('#nilai_simple').text('Rp ' + formatNumber(nilaiValue));
    
    calculatePPH();
}

// Function to load tax data
function loadTaxData() {
    // Fetch all active taxes
    $.get('/api/taxes', function(response) {
        if (response.success && response.data) {
            const taxes = response.data;
            
            // Look for PPN tax
            const ppnTax = taxes.find(tax => tax.name.toLowerCase().includes('ppn'));
            if (ppnTax) {
                $('#ppn').val(ppnTax.type === 'percentage' ? ppnTax.value : 0);
                $('#ppn').closest('div').attr('title', `PPN ${ppnTax.value}% (ID: ${ppnTax.id})`);
            }
            
            // Look for PPH tax
            const pphTax = taxes.find(tax => tax.name.toLowerCase().includes('pph'));
            if (pphTax) {
                // Store the PPH tax information in data attributes
                $('#pph').data('tax-type', pphTax.type);
                $('#pph').data('tax-value', pphTax.value);
                $('#pph').data('tax-id', pphTax.id);
                
                // Update tooltip with tax info
                let tooltipText = '';
                if (pphTax.type === 'percentage') {
                    tooltipText = `PPH ${pphTax.value}% (ID: ${pphTax.id})`;
                } else {
                    tooltipText = `PPH Fixed Rp ${formatNumber(pphTax.value)} (ID: ${pphTax.id})`;
                }
                $('#pph').closest('div').attr('title', tooltipText);
                
                // If it's a fixed amount, directly set the value
                if (pphTax.type === 'fixed') {
                    $('#pph').val(formatNumber(pphTax.value));
                } else {
                    // For percentage, we'll calculate it based on nilai
                    calculatePPH();
                }
            }
            
            // Recalculate values
            calculatePPN();
            calculateGrandTotal();
        }
    }).fail(function(error) {
        console.error('Error loading tax data:', error);
    });
}

// Calculate PPH based on percentage and nilai barang
function calculatePPH() {
    const pphType = $('#pph').data('tax-type');
    const pphValue = parseFloat($('#pph').data('tax-value')) || 0;
    const nilaiBarang = parseNumberFromFormatted($('#nilai').val()) || 0;
    
    let pphAmount = 0;
    
    if (pphType === 'percentage') {
        // Calculate as percentage
        if (pphValue > 0 && nilaiBarang > 0) {
            pphAmount = nilaiBarang * (pphValue / 100);
        }
    } else if (pphType === 'fixed') {
        // Use the fixed amount directly
        pphAmount = pphValue;
    }
    
    // Update the PPH field
    $('#pph').val(formatNumber(pphAmount));
    
    // Update grand total
    calculateGrandTotal();
}

// Display loading indicator while fetching mitra data
function showMitraLoading() {
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
}

function hideMitraLoading() {
    $('#mitra_loading').remove();
}

// Display mitra categories in a new section with improved styling
function displayMitraCategories(categories) {
    // Remove existing section if any
    $('#mitra_categories_section').remove();
    
    const categoryHTML = `
        <div id="mitra_categories_section" class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-md border border-amber-100 dark:border-amber-800/30 w-full">
            <h6 class="text-sm font-medium text-amber-800 dark:text-amber-400 mb-2">
                <i class="fas fa-tags mr-1"></i> Kategori Mitra (${categories.length})
            </h6>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 w-full">
                ${categories.map(category => `
                    <div class="text-xs bg-white dark:bg-slate-800 p-3 rounded flex justify-between items-center relative w-full border border-amber-100 dark:border-slate-700 hover:bg-amber-50 dark:hover:bg-slate-700/50">
                        <span class="font-medium text-sm">${category.name}</span>
                        <div class="flex flex-col items-end">
                            <span class="text-gray-600 dark:text-gray-300">Rp ${formatNumber(category.mit_price_cbm)}/CBM</span>
                            <span class="text-gray-600 dark:text-gray-300">Rp ${formatNumber(category.mit_price_kg)}/KG</span>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    // Place after warehouse selection in a full-width container
    $('#warehouse_category_container').html(categoryHTML);
}

// Highlight which categories are available in the selected warehouse
function highlightWarehouseCategories(warehouseCategories) {
    if (!$('#mitra_categories_section').length) return;
    
    // First reset all categories to normal style
    $('#mitra_categories_section .grid > div').removeClass('border-2 border-green-500')
        .find('.badge-active').remove();
    
    // Get all category IDs from the warehouse
    const categoryIds = warehouseCategories.map(cat => cat.id);
    
    // Add highlight class to categories that are used in this warehouse
    $('#mitra_categories_section .grid > div').each(function(index) {
        const category = mitraCategories[index];
        if (category && categoryIds.includes(category.id)) {
            $(this).addClass('border-2 border-green-500');
            
            // Add active badge
            if ($(this).find('.badge-active').length === 0) {
                $(this).append(`
                    <span class="badge-active absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4 h-5 w-5 flex items-center justify-center rounded-full bg-green-500 text-white text-xs shadow-sm">
                        <i class="fas fa-check"></i>
                    </span>
                `);
            }
        }
    });
}

// Update warehouse selection change handler to place warehouse info and categories side by side
$('#warehouse_id').on('change', function() {
    const warehouseId = $(this).val();
    selectedWarehouseId = warehouseId;
    
    // Reset dependent fields
    $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
    $('#categoryInfoBtn').addClass('hidden');
    $('#selectedCategoryInfo').addClass('hidden');
    $('#warehouseProductsSection').addClass('hidden');
    $('#warehouseProductsList').empty();
    $('#barangList').empty();
    $('#warehouse_category_container').empty();
    detailCounter = 0;
    
    if (warehouseId) {
        // Show loading indicator for categories
        const loadingHtml = '<option value="">Loading...</option>';
        $('#category_id').html(loadingHtml);
        
        // Show loading in warehouse info container
        $('#warehouse_category_container').html(`
            <div class="p-3 bg-blue-50 dark:bg-slate-700 rounded-md flex items-center justify-center w-full">
                <div class="animate-spin mr-3 h-5 w-5 text-blue-500">
                    <i class="fas fa-circle-notch"></i>
                </div>
                <p class="text-sm text-blue-600 dark:text-blue-400">Memuat informasi gudang...</p>
            </div>
        `);
        
        // Load warehouse details
        $.get(`/api/warehouses/${warehouseId}`, function(response) {
            // Display warehouse info in the new container
            const warehouseInfoHTML = `
                <div id="warehouse_info" class="p-3 text-sm bg-blue-50 dark:bg-slate-700 rounded-md w-full mb-3">
                    <h6 class="font-semibold mb-2">Informasi Gudang:</h6>
                    <div class="grid grid-cols-1 gap-2">
                        <div><span class="font-medium">Nama:</span> ${response.warehouse.name}</div>
                        <div><span class="font-medium">Tipe:</span> ${response.warehouse.type || 'N/A'}</div>
                        <div><span class="font-medium">Alamat:</span> ${response.warehouse.address || 'N/A'}</div>
                    </div>
                </div>
            `;
            
            $('#warehouse_category_container').html(warehouseInfoHTML);
            
            // Display mitra categories
            if (mitraCategories.length > 0) {
                displayMitraCategories(mitraCategories);
                
                // Highlight available categories
                highlightWarehouseCategories(response.categories);
            }
            
            // Load categories for the dropdown
            if (response.categories && response.categories.length > 0) {
                $('#category_id').empty().append('<option value="">Pilih Kategori</option>');
                
                response.categories.forEach(category => {
                    $('#category_id').append(`
                        <option value="${category.id}" 
                                data-price-cbm="${category.mit_price_cbm}" 
                                data-price-kg="${category.mit_price_kg}"
                                data-cust-price-cbm="${category.cust_price_cbm}" 
                                data-cust-price-kg="${category.cust_price_kg}">
                            ${category.name} - Rp ${formatNumber(category.mit_price_cbm)}/CBM, Rp ${formatNumber(category.mit_price_kg)}/KG
                        </option>
                    `);
                });
                
                $('#category_id').prop('disabled', false);
                $('#categoryInfoBtn').removeClass('hidden');
            } else {
                $('#category_id').empty().append('<option value="">Tidak ada kategori tersedia</option>');
                
                // Show no categories message
                $('#warehouse_category_container').append(`
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-md w-full">
                        <p class="text-sm text-gray-500 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tidak ada kategori tersedia untuk gudang ini
                        </p>
                    </div>
                `);
            }
        }).fail(function(error) {
            console.error("Error loading warehouse details:", error);
            $('#warehouse_category_container').html(`
                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-md w-full">
                    <p class="text-sm text-red-600 dark:text-red-400 text-center">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Gagal memuat informasi gudang
                    </p>
                </div>
            `);
            $('#category_id').empty().append('<option value="">Error memuat kategori</option>');
        });
    } else {
        // Reset everything if no warehouse selected
        resetWarehouseAndCategorySection();
    }
    
    calculateTotals();
});
function highlightWarehouseCategories(warehouseCategories) {
    if (!$('#mitra_categories_section').length) return;
    
    // First reset all categories to normal style
    $('#mitra_categories_section .grid > div').removeClass('border-2 border-green-500')
        .find('.badge-active').remove();
    
    const categoryIds = warehouseCategories.map(cat => cat.id);
    
    $('#mitra_categories_section .grid > div').each(function(index) {
        const category = mitraCategories[index];
        if (category && categoryIds.includes(category.id)) {
            $(this).addClass('border-2 border-green-500');
            
            //  active badge
            if ($(this).find('.badge-active').length === 0) {
                $(this).append(`
                    <span class="badge-active absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4 h-5 w-5 flex items-center justify-center rounded-full bg-green-500 text-white text-xs">
                        <i class="fas fa-check"></i>
                    </span>
                `);
            }
        }
    });
}
// Mitra change handler - only enable warehouse selection
$('#mitra_id').on('change', function() {
    const mitraId = $(this).val();
    
    // Reset all dependent fields
    $('#warehouse_id').empty().append('<option value="">Pilih Gudang</option>').prop('disabled', true);
    $('#service').prop('disabled', true).val('');
    $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
    $('#categoryInfoBtn').addClass('hidden');
    $('#selectedCategoryInfo').addClass('hidden');
    $('#warehouseProductsSection').addClass('hidden');
    $('#warehouseProductsList').empty();
    $('#barangList').empty();
    $('#warehouse_category_container').empty();
    
    // If mitra is selected, load warehouses
    if (mitraId) {
        // Show loading indicator
        $('#warehouse_id').html('<option value="">Loading...</option>');
        
        // Load warehouses for this mitra
        $.get(`/api/mitras/${mitraId}/warehouses`, function(warehouses) {
            $('#warehouse_id').empty().append('<option value="">Pilih Gudang</option>');
            
            warehouses.forEach(warehouse => {
                $('#warehouse_id').append(`<option value="${warehouse.id}" data-type="${warehouse.type}">${warehouse.name}</option>`);
            });
            
            $('#warehouse_id').prop('disabled', false);
        });
        
        // Load mitra categories for reference
        $.get(`/api/mitras/${mitraId}/categories`, function(categories) {
            mitraCategories = categories;
        });
        
        // Update marking code
        updateMarkingCode();
    }
});

// Warehouse change handler - enable service selection
$('#warehouse_id').on('change', function() {
    const warehouseId = $(this).val();
    selectedWarehouseId = warehouseId;
    
    // Reset dependent fields
    $('#service').prop('disabled', true).val('');
    $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
    $('#categoryInfoBtn').addClass('hidden');
    $('#selectedCategoryInfo').addClass('hidden');
    $('#warehouseProductsSection').addClass('hidden');
    $('#warehouseProductsList').empty();
    $('#barangList').empty();
    $('#warehouse_category_container').empty();
    
    if (warehouseId) {
        // Show warehouse info
        const warehouseType = $(this).find('option:selected').data('type');
        
        // Display basic warehouse info
        const warehouseInfoHTML = `
            <div id="warehouse_info" class="p-3 text-sm bg-blue-50 dark:bg-slate-700 rounded-md w-full mb-3">
                <h6 class="font-semibold mb-2">Informasi Gudang:</h6>
                <div class="grid grid-cols-1 gap-2">
                    <div><span class="font-medium">Nama:</span> ${$(this).find('option:selected').text()}</div>
                    <div><span class="font-medium">Tipe:</span> ${warehouseType || 'N/A'}</div>
                </div>
            </div>
        `;
        
        $('#warehouse_category_container').html(warehouseInfoHTML);
        
        // Now enable service selection
        $('#service').prop('disabled', false);
        
        // Update marking code
        updateMarkingCode();
    }
});

// Service change handler - only after this enable category selection
$('#service').on('change', function() {
    const serviceType = $(this).val();
    
    // Reset dependent fields
    $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
    $('#categoryInfoBtn').addClass('hidden');
    $('#selectedCategoryInfo').addClass('hidden');
    $('#warehouseProductsSection').addClass('hidden');
    $('#warehouseProductsList').empty();
    
    if (serviceType && selectedWarehouseId) {
        // Show loading indicator for categories
        const loadingHtml = '<option value="">Loading...</option>';
        $('#category_id').html(loadingHtml);
        
        // Update warehouse info to include service type
        const warehouseInfoHTML = $('#warehouse_info').html() + `
            <div class="mt-2"><span class="font-medium">Layanan:</span> ${serviceType}</div>
        `;
        $('#warehouse_info').html(warehouseInfoHTML);
        
        // Add service badge
        let serviceBadge = '';
        if (serviceType === 'SEA') {
            serviceBadge = `
                <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                    <i class="fas fa-ship mr-1"></i> SEA (Laut)
                </div>
            `;
        } else {
            serviceBadge = `
                <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300">
                    <i class="fas fa-plane mr-1"></i> AIR (Udara)
                </div>
            `;
        }
        $('#warehouse_info').append(serviceBadge);
        
        // Load categories for the dropdown with service-specific pricing
        $.get(`/api/warehouses/${selectedWarehouseId}?service=${serviceType}`, function(response) {
            const serviceTypeLower = serviceType.toLowerCase();
            
            if (response.categories && response.categories.length > 0) {
                $('#category_id').empty().append('<option value="">Pilih Kategori</option>');
                
                response.categories.forEach(category => {
                    // Get the appropriate pricing fields based on service type
                    const mitPriceCbmField = `mit_price_cbm_${serviceTypeLower}`;
                    const mitPriceKgField = `mit_price_kg_${serviceTypeLower}`;
                    const custPriceCbmField = `cust_price_cbm_${serviceTypeLower}`;
                    const custPriceKgField = `cust_price_kg_${serviceTypeLower}`;
                    
                    // Use the field names that match our service type 
                    const mitPriceCbm = category[mitPriceCbmField] || 0;
                    const mitPriceKg = category[mitPriceKgField] || 0;
                    const custPriceCbm = category[custPriceCbmField] || 0;
                    const custPriceKg = category[custPriceKgField] || 0;
                    
                    $('#category_id').append(`
                        <option value="${category.id}" 
                                data-price-cbm-${serviceTypeLower}="${mitPriceCbm}" 
                                data-price-kg-${serviceTypeLower}="${mitPriceKg}"
                                data-cust-price-cbm-${serviceTypeLower}="${custPriceCbm}" 
                                data-cust-price-kg-${serviceTypeLower}="${custPriceKg}">
                            ${category.name} - ${serviceType} - Rp ${formatNumber(mitPriceCbm)}/CBM, Rp ${formatNumber(mitPriceKg)}/KG
                        </option>
                    `);
                });
                
                $('#category_id').prop('disabled', false);
                $('#categoryInfoBtn').removeClass('hidden');
            } else {
                $('#category_id').empty().append('<option value="">Tidak ada kategori tersedia</option>');
                
                // Show no categories message
                $('#warehouse_category_container').append(`
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-md w-full">
                        <p class="text-sm text-gray-500 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tidak ada kategori tersedia untuk gudang ini dengan layanan ${serviceType}
                        </p>
                    </div>
                `);
            }
        }).fail(function(error) {
            console.error("Error loading warehouse details:", error);
            $('#category_id').empty().append('<option value="">Error memuat kategori</option>');
        });
        
        // Update marking code to include service
        updateMarkingCode();
    }
});

// Category change handler - now with service type
$('#category_id').on('change', function() {
    const categoryId = $(this).val();
    selectedCategoryId = categoryId;
    
    if (categoryId && selectedWarehouseId) {
        // Get service type
        const serviceType = $('#service').val();
        const serviceTypeLower = serviceType.toLowerCase();
        
        // Show selected category info with service type
        $('#categoryName').text($(this).find('option:selected').text().split(' - ')[0]);
        $('#serviceTypeDisplay').text(serviceType);
        
        if (serviceType === 'SEA') {
            $('#serviceDisplay').removeClass('badge-soft-warning').addClass('badge-soft-primary');
            $('#serviceDisplay').html('<i class="fas fa-ship mr-1"></i> SEA (Laut)');
        } else {
            $('#serviceDisplay').removeClass('badge-soft-primary').addClass('badge-soft-warning');
            $('#serviceDisplay').html('<i class="fas fa-plane mr-1"></i> AIR (Udara)');
        }
        
        // Get service-specific pricing fields
        const priceCbmField = `price-cbm-${serviceTypeLower}`;
        const priceKgField = `price-kg-${serviceTypeLower}`;
        const custPriceCbmField = `cust-price-cbm-${serviceTypeLower}`;
        const custPriceKgField = `cust-price-kg-${serviceTypeLower}`;
        
        const priceCbm = parseFloat($(this).find('option:selected').data(priceCbmField)) || 0;
        const priceKg = parseFloat($(this).find('option:selected').data(priceKgField)) || 0;
        const custPriceCbm = parseFloat($(this).find('option:selected').data(custPriceCbmField)) || 0;
        const custPriceKg = parseFloat($(this).find('option:selected').data(custPriceKgField)) || 0;
        
        // Update pricing inputs
        $('#harga_ongkir_cbm').val(priceCbm);
        $('#harga_ongkir_wg').val(priceKg);
        
        // Update pricing display
        $('#categoryPriceCbm').text(`Rp ${formatNumber(priceCbm)}`);
        $('#categoryPriceKg').text(`Rp ${formatNumber(priceKg)}`);
        $('#categoryCustPriceCbm').text(`Rp ${formatNumber(custPriceCbm)}`);
        $('#categoryCustPriceKg').text(`Rp ${formatNumber(custPriceKg)}`);
        $('#selectedCategoryInfo').removeClass('hidden');
        
        // Show products section and load products with service type
        $('#warehouseProductsSection').removeClass('hidden');
        $('#warehouseProductsList').html('<tr><td colspan="4" class="text-center py-4"><i class="fas fa-circle-notch fa-spin mr-2"></i> Memuat data produk...</td></tr>');
        
        // Include service type in API call
        $.get(`/api/warehouses/${selectedWarehouseId}/categories/${categoryId}/products?service=${serviceType}`, function(products) {
            console.log("Products loaded:", products);
            
            // Process products for display
            warehouseProducts = products.map(product => {
                return {
                    ...product,
                    categoryName: $('#category_id option:selected').text().split(' - ')[0],
                    price_cbm: product.price_cbm || 0,
                    price_kg: product.price_kg || 0,
                    cust_price_cbm: product.cust_price_cbm || 0,
                    cust_price_kg: product.cust_price_kg || 0
                };
            });
            
            // Initialize display
            filteredProducts = [...warehouseProducts];
            currentPage = 1;
            renderProducts();
            
            // Reset product search
            $('#product_search').val('');
        }).fail(function(error) {
            console.error("Error loading products:", error);
            $('#warehouseProductsList').html('<tr><td colspan="4" class="text-center py-4 text-red-500"><i class="fas fa-exclamation-circle mr-2"></i> Gagal memuat data produk</td></tr>');
        });
    } else {
        $('#warehouseProductsSection').addClass('hidden');
        $('#selectedCategoryInfo').addClass('hidden');
    }
});

// Update marking code function to include service
function updateMarkingCode() {
    // Get selected values
    const mitraCode = $('#mitra_id option:selected').data('marking-code') || '';
    const marketingCode = $('#marketing_id option:selected').data('code') || '';
    const customerCode = $('#customer_id option:selected').data('code') || '';
    const serviceType = $('#service').val() || '';
    
    // Build marking code
    if (mitraCode) {
        let code = `${mitraCode}/WMLC`;
        
        if (marketingCode) {
            code += `/${marketingCode}`;
        }
        
        if (customerCode) {
            code += `/${customerCode}`;
        }
        
        if (serviceType) {
            code += `/${serviceType}`;
        }
        
        $('#marking').val(code);
    } else {
        $('#marking').val('');
    }
}