/**
 * Shipping Form Handler
 * Menangani interaksi form shipping - dibuat oleh AstaCode
 */

// Import our modules
import { FormatUtils } from './modules/format-utils.js';
import { SelectionManager } from './modules/selection-manager.js';
import { ProductManager } from './modules/product-manager.js';
import { CalculationManager } from './modules/calculation-manager.js';
import { FormSubmissionHandler } from './modules/form-submission.js';
import { UIHelpers } from './modules/ui-helpers.js';

// Global state management
const ShippingState = {
    detailCounter: 0,
    warehouseProducts: [],
    currentPage: 1,
    itemsPerPage: 10,
    filteredProducts: [],
    mitraCategories: [],
    selectedMitraId: null,
    selectedWarehouseId: null,
    selectedCategoryId: null
};

// Initialize the application
$(document).ready(function() {
    // Initialize UI components
    UIHelpers.initSelect2();
    UIHelpers.initMoneyInputs();
    
    // Initialize managers with state
    SelectionManager.init(ShippingState);
    ProductManager.init(ShippingState);
    CalculationManager.init(ShippingState);
    FormSubmissionHandler.init(ShippingState);
    
    // Register event handlers for form interactions
    registerEventHandlers();
    
    // Initialize form state
    initializeForm();
});

// Register all event handlers for the form
function registerEventHandlers() {
    // Selection change handlers
    $('#customer_id').on('change', SelectionManager.handleCustomerChange);
    $('#bank_id').on('change', SelectionManager.handleBankChange);
    $('#mitra_id').on('change', SelectionManager.handleMitraChange);
    $('#warehouse_id').on('change', SelectionManager.handleWarehouseChange);
    $('#service').on('change', SelectionManager.handleServiceChange);
    $('#category_id').on('change', SelectionManager.handleCategoryChange);
    $('#marketing_id').on('change', SelectionManager.updateMarkingCode);
    
    // Product search
    $('#product_search').on('input', ProductManager.handleProductSearch);
    
    // Fee and tax calculations
    $('[data-fee="true"]').on('input', CalculationManager.calculateFees);
    $('#pph').on('input', CalculationManager.calculateGrandTotal);
    $('#ppn').on('input', function() {
        CalculationManager.calculatePPN();
        CalculationManager.calculateGrandTotal();
    });
    
    // Shipping settings
    $('#shipping_type').on('change', SelectionManager.handleShippingTypeChange);
    $('#top, #transaction_date').on('change', SelectionManager.handlePaymentTermsChange);
    $('#harga_ongkir_cbm, #harga_ongkir_wg').on('input', CalculationManager.updateShippingCostCalculation);
    
    // Dynamic row management
    $(document).on('click', '.delete-calculation', function() {
        $(this).closest('tr').remove();
        CalculationManager.calculateTotals();
    });
    
    // Image upload handling
    $(document).on('click', '.upload-image-btn', UIHelpers.handleImageUploadButtonClick);
    $(document).on('change', '.product-image-upload', UIHelpers.handleImageFileChange);
    
    // Form submission
    $('#shippingForm').on('submit', FormSubmissionHandler.handleFormSubmit);
}

// Initialize form state
function initializeForm() {
    $('#shipping_type').trigger('change'); // Generate initial invoice number
    CalculationManager.updateNilaiDisplay();
    CalculationManager.loadTaxData();
    $('#customer_id').focus();
}