/**
 * Product Manager Module
 * Handles product listing, searching and addition to cart
 */

import { FormatUtils } from './format-utils.js';
import { CalculationManager } from './calculation-manager.js';
import { UIHelpers } from './ui-helpers.js';

export const ProductManager = {
    state: null,
    
    init(shippingState) {
        this.state = shippingState;
    },
    
    handleProductSearch() {
        const searchTerm = $(this).val().toLowerCase().trim();
        
        if (searchTerm === '') {
            ProductManager.state.filteredProducts = [...ProductManager.state.warehouseProducts];
        } else {
            ProductManager.state.filteredProducts = ProductManager.state.warehouseProducts.filter(product => 
                product.name.toLowerCase().includes(searchTerm)
            );
        }
        
        ProductManager.state.currentPage = 1;
        ProductManager.renderProducts();
    },
    
    renderProducts() {
        const startIndex = (ProductManager.state.currentPage - 1) * ProductManager.state.itemsPerPage;
        const endIndex = startIndex + ProductManager.state.itemsPerPage;
        const paginatedProducts = ProductManager.state.filteredProducts.slice(startIndex, endIndex);
        const totalPages = Math.ceil(ProductManager.state.filteredProducts.length / ProductManager.state.itemsPerPage);
        
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
                        <td class="text-right">Rp ${FormatUtils.formatNumber(product.price_kg)}</td>
                        <td class="text-right">Rp ${FormatUtils.formatNumber(product.price_cbm)}</td>
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
        $('#product-pagination-info').text(`Menampilkan ${startIndex + 1}-${Math.min(endIndex, ProductManager.state.filteredProducts.length)} dari ${ProductManager.state.filteredProducts.length} barang`);
        
        // Render pagination
        ProductManager.renderPagination(totalPages);
        
        // Attach event listeners to add buttons
        $('.add-product-btn').on('click', function() {
            const productId = parseInt($(this).data('product-id'));
            const product = ProductManager.state.warehouseProducts.find(p => p.id === productId);
            if (product) {
                ProductManager.addProductToTable(product);
                
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
    },
    
    renderPagination(totalPages) {
        const pagination = $('#product-pagination');
        pagination.empty();
        
        // Previous button
        pagination.append(`
            <button class="btn btn-sm ${ProductManager.state.currentPage === 1 ? 'btn-disabled' : 'btn-secondary'}" 
                    ${ProductManager.state.currentPage === 1 ? 'disabled' : ''} data-page="prev">
                <i class="fas fa-chevron-left"></i>
            </button>
        `);
        
        // Page numbers
        const startPage = Math.max(1, ProductManager.state.currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);
        
        for (let i = startPage; i <= endPage; i++) {
            pagination.append(`
                <button class="btn btn-sm ${i === ProductManager.state.currentPage ? 'btn-primary' : 'btn-secondary'}" data-page="${i}">
                    ${i}
                </button>
            `);
        }
        
        // Next button
        pagination.append(`
            <button class="btn btn-sm ${ProductManager.state.currentPage === totalPages ? 'btn-disabled' : 'btn-secondary'}" 
                    ${ProductManager.state.currentPage === totalPages ? 'disabled' : ''} data-page="next">
                <i class="fas fa-chevron-right"></i>
            </button>
        `);
        
        // Attach event listeners to pagination buttons
        pagination.find('button').on('click', function() {
            if ($(this).attr('disabled')) return;
            
            const page = $(this).data('page');
            if (page === 'prev') {
                ProductManager.state.currentPage--;
            } else if (page === 'next') {
                ProductManager.state.currentPage++;
            } else {
                ProductManager.state.currentPage = page;
            }
            
            ProductManager.renderProducts();
        });
    },
    
    addProductToTable(product) {
        // First check if the product already exists in the table
        const existingRow = $('#barangList').find(`tr[data-product-id="${product.id}"]`);
        
        if (existingRow.length > 0) {
            // Product already exists, just increment the quantity instead of adding a new row
            const currentCtns = parseInt(existingRow.find('.total-ctns').val()) || 1;
            existingRow.find('.total-ctns').val(currentCtns + 1).trigger('input');
            
            // Show feedback to user
            UIHelpers.showToast(`Jumlah ${product.name} ditambahkan`, 'success');
            return;
        }
        
        // If product doesn't exist yet, continue with adding a new row
        const rowIndex = ProductManager.state.detailCounter++;
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
                    <input type="hidden" name="barang[${rowIndex}][category_id]" value="${ProductManager.state.selectedCategoryId || 0}">
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
        ProductManager.attachRowEventListeners($(`#barangList tr[data-index="${rowIndex}"]`));
        
        // Calculate totals
        CalculationManager.calculateTotals();
        
        // Show feedback to user
        UIHelpers.showToast(`${product.name} ditambahkan ke daftar`, 'success');
    },
    
    attachRowEventListeners(row) {
        // Quantity calculations
        row.find('.qty-per-ctn, .total-ctns').on('input', function() {
            const qtyPerCtn = parseFloat(row.find('.qty-per-ctn').val()) || 0;
            const totalCtns = parseFloat(row.find('.total-ctns').val()) || 0;
            const totalQty = qtyPerCtn * totalCtns;
            
            row.find('.total-qty').val(totalQty);
            
            // Recalculate GW
            ProductManager.calculateRowGW(row);
            
            // Recalculate Volume
            ProductManager.calculateRowVolume(row);
            
            // Update totals
            CalculationManager.calculateTotals();
        });
        
        // Dimension calculations for volume
        row.find('.dimension-input').on('input', function() {
            // Calculate volume
            ProductManager.calculateRowVolume(row);
            
            // Update totals
            CalculationManager.calculateTotals();
        });
        
        // GW calculations
        row.find('.gw-per-ctn').on('input', function() {
            // Calculate GW
            ProductManager.calculateRowGW(row);
            
            // Update totals
            CalculationManager.calculateTotals();
        });
        
        // Delete button
        row.find('.delete-barang').on('click', function() {
            row.remove();
            
            if ($('#barangList tr').length === 0) {
                $('#emptyProductState').show();
            }
            
            CalculationManager.calculateTotals();
        });
    },
    
    calculateRowGW(row) {
        const gwPerCtn = parseFloat(row.find('.gw-per-ctn').val()) || 0;
        const totalCtns = parseFloat(row.find('.total-ctns').val()) || 0;
        const totalGw = gwPerCtn * totalCtns;
        
        row.find('.total-gw').val(totalGw.toFixed(2));
        row.find('.total-gw-display').val(FormatUtils.formatNumber(totalGw));
    },
    
    calculateRowVolume(row) {
        const length = parseFloat(row.find('input[name$="[length]"]').val()) || 0;
        const width = parseFloat(row.find('input[name$="[width]"]').val()) || 0;
        const height = parseFloat(row.find('input[name$="[high]"]').val()) || 0;
        const totalCtns = parseFloat(row.find('.total-ctns').val()) || 0;
        
        // Volume in cubic meters (L*W*H in cm / 1,000,000) x jumlah carton
        const volumeCbm = (length * width * height / 1000000) * totalCtns;
        
        row.find('.volume').val(volumeCbm.toFixed(6));
        row.find('.volume-display').val(FormatUtils.formatNumber(volumeCbm));
        
        return volumeCbm;
    }
};