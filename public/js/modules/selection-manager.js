/**
 * Selection Manager Module
 * Handles selection changes for customers, mitras, warehouses, etc.
 */

import { FormatUtils } from './format-utils.js';
import { UIHelpers } from './ui-helpers.js';
import { CalculationManager } from './calculation-manager.js';
import { ProductManager } from './product-manager.js';

export const SelectionManager = {
    state: null,
    
    init(shippingState) {
        this.state = shippingState;
    },
    
    handleCustomerChange() {
        const customerId = $(this).val();
        const marketingId = $(this).find('option:selected').data('marketing-id');
        
        if (marketingId) {
            $('#marketing_id').val(marketingId).prop('disabled', true);
            $('#marketing_id').trigger('change');
        } else {
            $('#marketing_id').val('').prop('disabled', true);
            SelectionManager.updateMarkingCode();
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
    },
    
    handleBankChange() {
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
    },
    
    handleMitraChange() {
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
        
        SelectionManager.state.selectedMitraId = mitraId;
        const markingCode = $(this).find('option:selected').data('marking-code') || '';
        $('#marking').val(markingCode);
        
        // If mitra is selected, load warehouses
        if (mitraId) {
            // Show loading indicator
            $('#warehouse_id').html('<option value="">Loading...</option>');
            UIHelpers.showMitraLoading();
            
            // Load warehouses for this mitra
            $.get(`/api/mitras/${mitraId}/warehouses`, function(warehouses) {
                $('#warehouse_id').empty().append('<option value="">Pilih Gudang</option>');
                
                warehouses.forEach(warehouse => {
                    $('#warehouse_id').append(`<option value="${warehouse.id}" data-type="${warehouse.type}">${warehouse.name}</option>`);
                });
                
                $('#warehouse_id').prop('disabled', false);
            });
            
            // Load categories for this mitra
            $.get(`/api/mitras/${mitraId}/categories`, function(categories) {
                SelectionManager.state.mitraCategories = categories;
            });
            
            // Get mitra data
            $.get(`/api/mitras/${mitraId}`, function(data) {
                // Remove loading once all data is fetched
                UIHelpers.hideMitraLoading();
                
                // Set max weight value
                const maxWeight = parseFloat(data.max_wg) || 0;
                $('#max_weight').val(maxWeight);
                $('#max_weight_display').text(FormatUtils.formatNumber(maxWeight));
            });
        } else {
            // Reset everything if no mitra selected
            SelectionManager.state.selectedMitraId = null;
            $('#max_weight').val(0);
            $('#max_weight_display').text('0,00');
        }
        
        // Update marking code
        SelectionManager.updateMarkingCode();
    },
    
    handleWarehouseChange() {
        const warehouseId = $(this).val();
        SelectionManager.state.selectedWarehouseId = warehouseId;
        
        // Reset dependent fields
        $('#service').prop('disabled', true).val('');
        $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
        $('#categoryInfoBtn').addClass('hidden');
        $('#selectedCategoryInfo').addClass('hidden');
        $('#warehouseProductsSection').addClass('hidden');
        $('#warehouseProductsList').empty();
        $('#barangList').empty();
        $('#warehouse_category_container').empty();
        SelectionManager.state.detailCounter = 0;
        
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
            SelectionManager.updateMarkingCode();
        }
        
        CalculationManager.calculateTotals();
    },
    
    handleServiceChange() {
        const serviceType = $(this).val();
        SelectionManager.updateMarkingCode();
        
        // Reset dependent fields
        $('#category_id').empty().append('<option value="">Pilih Kategori</option>').prop('disabled', true);
        $('#categoryInfoBtn').addClass('hidden');
        $('#selectedCategoryInfo').addClass('hidden');
        $('#warehouseProductsSection').addClass('hidden');
        $('#warehouseProductsList').empty();
        
        if (serviceType && SelectionManager.state.selectedWarehouseId) {
            // Show loading indicator for categories
            const loadingHtml = '<option value="">Loading...</option>';
            $('#category_id').html(loadingHtml);
            
            // Update warehouse info to include service type if exists
            if ($('#warehouse_info').length > 0) {
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
            }
            
            // Load categories for the dropdown with service-specific pricing
            $.get(`/api/warehouses/${SelectionManager.state.selectedWarehouseId}?service=${serviceType}`, function(response) {
                const serviceTypeLower = serviceType.toLowerCase();
                
                if (response.categories && response.categories.length > 0) {
                    $('#category_id').empty().append('<option value="">Pilih Kategori</option>');
                    
                    response.categories.forEach(category => {
                        // Get the appropriate pricing fields based on service type
                        const mitPriceCbmField = `mit_price_cbm_${serviceTypeLower}`;
                        const mitPriceKgField = `mit_price_kg_${serviceTypeLower}`;
                        const custPriceCbmField = `cust_price_cbm_${serviceTypeLower}`;
                        const custPriceKgField = `cust_price_kg_${serviceTypeLower}`;
                        
                        // Use both mitra and customer pricing for data attributes
                        const mitPriceCbm = category[mitPriceCbmField] || 0;
                        const mitPriceKg = category[mitPriceKgField] || 0;
                        const custPriceCbm = category[custPriceCbmField] || 0;
                        const custPriceKg = category[custPriceKgField] || 0;
                        
                        $('#category_id').append(`
                            <option value="${category.id}" 
                                    data-mit-price-cbm-${serviceTypeLower}="${mitPriceCbm}" 
                                    data-mit-price-kg-${serviceTypeLower}="${mitPriceKg}"
                                    data-cust-price-cbm-${serviceTypeLower}="${custPriceCbm}" 
                                    data-cust-price-kg-${serviceTypeLower}="${custPriceKg}">
                                ${category.name} - ${serviceType} - Rp ${FormatUtils.formatNumber(custPriceCbm)}/CBM, Rp ${FormatUtils.formatNumber(custPriceKg)}/KG
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
    },
    
    handleCategoryChange() {
        const categoryId = $(this).val();
        SelectionManager.state.selectedCategoryId = categoryId;
        
        if (categoryId && SelectionManager.state.selectedWarehouseId) {
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
            
            // Get service-specific pricing fields - both mitra and customer prices
            const mitraPriceCbmField = `mit-price-cbm-${serviceTypeLower}`;
            const mitraPriceKgField = `mit-price-kg-${serviceTypeLower}`;
            const custPriceCbmField = `cust-price-cbm-${serviceTypeLower}`;
            const custPriceKgField = `cust-price-kg-${serviceTypeLower}`;
            
            // Get the price values from data attributes
            const mitraPriceCbm = parseFloat($(this).find('option:selected').data(mitraPriceCbmField)) || 0;
            const mitraPriceKg = parseFloat($(this).find('option:selected').data(mitraPriceKgField)) || 0;
            const custPriceCbm = parseFloat($(this).find('option:selected').data(custPriceCbmField)) || 0;
            const custPriceKg = parseFloat($(this).find('option:selected').data(custPriceKgField)) || 0;
            
            // Update pricing inputs with customer prices (for calculations)
            $('#harga_ongkir_cbm').val(custPriceCbm);
            $('#harga_ongkir_wg').val(custPriceKg);
            
            // Update pricing display - ensure correct labels match correct values
            $('#categoryPriceCbm').text(`Rp ${FormatUtils.formatNumber(mitraPriceCbm)}`);     // Mitra CBM price
            $('#categoryPriceKg').text(`Rp ${FormatUtils.formatNumber(mitraPriceKg)}`);       // Mitra KG price
            $('#categoryCustPriceCbm').text(`Rp ${FormatUtils.formatNumber(custPriceCbm)}`);  // Customer CBM price
            $('#categoryCustPriceKg').text(`Rp ${FormatUtils.formatNumber(custPriceKg)}`);    // Customer KG price
            
            $('#selectedCategoryInfo').removeClass('hidden');
            
            // Show products section and load products with service type
            $('#warehouseProductsSection').removeClass('hidden');
            $('#warehouseProductsList').html('<tr><td colspan="4" class="text-center py-4"><i class="fas fa-circle-notch fa-spin mr-2"></i> Memuat data produk...</td></tr>');
            
            // Include service type in API call
            $.get(`/api/warehouses/${SelectionManager.state.selectedWarehouseId}/categories/${categoryId}/products?service=${serviceType}`, function(products) {
                console.log("Products loaded:", products);
                
                // Process products for display - ensure we're using customer prices
                SelectionManager.state.warehouseProducts = products.map(product => {
                    return {
                        ...product,
                        categoryName: $('#category_id option:selected').text().split(' - ')[0],
                        price_cbm: product.cust_price_cbm || 0, // Use customer price
                        price_kg: product.cust_price_kg || 0,   // Use customer price
                    };
                });
                
                // Initialize display
                SelectionManager.state.filteredProducts = [...SelectionManager.state.warehouseProducts];
                SelectionManager.state.currentPage = 1;
                ProductManager.renderProducts();
                
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
    },
    
    handleShippingTypeChange() {
        const shippingType = $(this).val();
        $.get(`/api/shippings/generate-invoice?type=${shippingType}`, function(data) {
            $('#invoice').val(data.invoice);
        });
    },
    
    handlePaymentTermsChange() {
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
    },
    
    updateMarkingCode() {
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
    },
    
    displayMitraCategories(categories, serviceType = 'sea') {
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
                                <span class="text-gray-600 dark:text-gray-300">Rp ${FormatUtils.formatNumber(category[`mit_price_cbm_${serviceType}`] || 0)}/CBM</span>
                                <span class="text-gray-600 dark:text-gray-300">Rp ${FormatUtils.formatNumber(category[`mit_price_kg_${serviceType}`] || 0)}/KG</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        // Place after warehouse selection in a full-width container
        $('#warehouse_category_container').html(categoryHTML);
    },
    
    highlightWarehouseCategories(warehouseCategories) {
        if (!$('#mitra_categories_section').length) return;
        
        // First reset all categories to normal style
        $('#mitra_categories_section .grid > div').removeClass('border-2 border-green-500')
            .find('.badge-active').remove();
        
        const categoryIds = warehouseCategories.map(cat => cat.id);
        
        $('#mitra_categories_section .grid > div').each((index, element) => {
            const category = SelectionManager.state.mitraCategories[index];
            if (category && categoryIds.includes(category.id)) {
                $(element).addClass('border-2 border-green-500');
                
                // Add active badge
                if ($(element).find('.badge-active').length === 0) {
                    $(element).append(`
                        <span class="badge-active absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4 h-5 w-5 flex items-center justify-center rounded-full bg-green-500 text-white text-xs">
                            <i class="fas fa-check"></i>
                        </span>
                    `);
                }
            }
        });
    }
};