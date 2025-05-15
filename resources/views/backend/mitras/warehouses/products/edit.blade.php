<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Edit Product" page="Warehouses" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <div class="card">
            <div class="card-body p-6">
                <div class="flex items-center p-4 mb-6 rounded-md bg-slate-50 dark:bg-slate-800">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-500 text-white">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="ml-4">
                        <h5 class="text-base font-medium">{{ $warehouse->name }}</h5>
                        <p class="text-sm text-slate-500">{{ ucfirst($warehouse->type) }} Freight Warehouse</p>
                    </div>
                </div>

                <form action="{{ route('mitra.warehouses.products.update', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id, 'product' => $product->id]) }}" method="POST" id="productForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="name">Product Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name', $product->name) }}" required />
                        </div>
                        
                        <!-- Parent Product -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="parent_id">Parent Product</label>
                            <select id="parent_id" name="parent_id" class="select">
                                <option value="">None (Top Level Product)</option>
                                @foreach($potentialParents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $product->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ str_repeat('— ', $parent->level) }} {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Optional. Select a parent to create a sub-product.</p>
                        </div>
                        
                        <!-- Category Product -->
                        <div class="flex flex-col gap-1 col-span-2">
                            <div class="flex items-center justify-between mb-1">
                                <label class="label font-medium" for="category_product_id">Product Category</label>
                                <button type="button" id="addCategoryBtn" class="text-xs text-primary-500 hover:text-primary-600 flex items-center">
                                    <i class="fas fa-plus-circle mr-1"></i> Create New Category
                                </button>
                            </div>
                            <select id="category_product_id" name="category_product_id" class="select">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            data-mit-price-cbm="{{ $category->mit_price_cbm }}"
                                            data-mit-price-kg="{{ $category->mit_price_kg }}"
                                            data-cust-price-cbm="{{ $category->cust_price_cbm }}"
                                            data-cust-price-kg="{{ $category->cust_price_kg }}"
                                            {{ old('category_product_id', $product->category_product_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->mitra->name ?? 'Unknown' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Select a category to automatically apply pricing information.</p>
                        </div>
                    </div>
                    
                    <!-- Pricing Information -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Pricing Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- CBM Pricing -->
                            <div class="border dark:border-slate-600 rounded-lg p-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-300 mb-3">CBM Pricing</h4>
                                
                                <!-- Mitra Price CBM -->
                                <div class="flex flex-col gap-1 mb-4">
                                    <label class="label mb-1 font-medium" for="mit_price_cbm_display">Mitra Price</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                        <input type="text" class="input pl-10 price-input" id="mit_price_cbm_display" 
                                               value="{{ old('mit_price_cbm', $product->category->mit_price_cbm ?? 0) }}" readonly />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-xs text-slate-400">From Category</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Customer Price CBM -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="cust_price_cbm_display">Customer Price</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                        <input type="text" class="input pl-10 price-input" id="cust_price_cbm_display" 
                                               value="{{ old('cust_price_cbm', $product->category->cust_price_cbm ?? 0) }}" readonly />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-xs text-slate-400">From Category</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- KG Pricing -->
                            <div class="border dark:border-slate-600 rounded-lg p-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-300 mb-3">KG Pricing</h4>
                                
                                <!-- Mitra Price KG -->
                                <div class="flex flex-col gap-1 mb-4">
                                    <label class="label mb-1 font-medium" for="mit_price_kg_display">Mitra Price</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                        <input type="text" class="input pl-10 price-input" id="mit_price_kg_display" 
                                               value="{{ old('mit_price_kg', $product->category->mit_price_kg ?? 0) }}" readonly />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-xs text-slate-400">From Category</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Customer Price KG -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="cust_price_kg_display">Customer Price</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                        <input type="text" class="input pl-10 price-input" id="cust_price_kg_display" 
                                               value="{{ old('cust_price_kg', $product->category->cust_price_kg ?? 0) }}" readonly />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-xs text-slate-400">From Category</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end border-t pt-4">
                        <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary ml-2">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content max-w-lg">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn btn-icon btn-sm" data-dismiss="modal">
                    <i class="h-5 w-5" data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="category_name">Category Name</label>
                            <input type="text" class="input" id="category_name" name="category_name" required />
                        </div>
                        
                        <input type="hidden" id="category_mitra_id" name="mitra_id" value="{{ $mitra->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            <div class="flex flex-col gap-1">
                                <label class="label mb-1 font-medium" for="category_mit_price_cbm">Mitra Price (CBM)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                    <input type="text" class="input pl-10 price-input" id="category_mit_price_cbm" name="mit_price_cbm" value="0" />
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-1">
                                <label class="label mb-1 font-medium" for="category_mit_price_kg">Mitra Price (KG)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                    <input type="text" class="input pl-10 price-input" id="category_mit_price_kg" name="mit_price_kg" value="0" />
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-1">
                                <label class="label mb-1 font-medium" for="category_cust_price_cbm">Customer Price (CBM)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                    <input type="text" class="input pl-10 price-input" id="category_cust_price_cbm" name="cust_price_cbm" value="0" />
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-1">
                                <label class="label mb-1 font-medium" for="category_cust_price_kg">Customer Price (KG)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                    <input type="text" class="input pl-10 price-input" id="category_cust_price_kg" name="cust_price_kg" value="0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="saveCategoryBtn" class="btn btn-primary">Save Category</button>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Cleave.js for all price inputs
            $('.price-input').each(function() {
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });
            });
            
            // Handle category selection change - update price fields
            $('#category_product_id').on('change', function() {
                const $selected = $(this).find('option:selected');
                
                if ($selected.val()) {
                    // Format prices with Cleave.js
                    const formatPrice = (price) => {
                        return new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 2
                        }).format(price || 0);
                    };
                    
                    // Get prices from selected category
                    const mitPriceCbm = $selected.data('mit-price-cbm') || 0;
                    const mitPriceKg = $selected.data('mit-price-kg') || 0;
                    const custPriceCbm = $selected.data('cust-price-cbm') || 0;
                    const custPriceKg = $selected.data('cust-price-kg') || 0;
                    
                    // Update display fields
                    $('#mit_price_cbm_display').val(formatPrice(mitPriceCbm));
                    $('#mit_price_kg_display').val(formatPrice(mitPriceKg));
                    $('#cust_price_cbm_display').val(formatPrice(custPriceCbm));
                    $('#cust_price_kg_display').val(formatPrice(custPriceKg));
                } else {
                    // Reset all fields if no category selected
                    $('#mit_price_cbm_display, #mit_price_kg_display, #cust_price_cbm_display, #cust_price_kg_display').val('0');
                }
            });
            
            // Modal handling
            $('#addCategoryBtn').on('click', function() {
                // Reset the form
                $('#categoryForm')[0].reset();
                
                // Show the modal
                $('#addCategoryModal').show();
                
                // Re-initialize Cleave.js for the modal inputs
                $('.price-input').each(function() {
                    new Cleave(this, {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand',
                        numeralDecimalMark: ',',
                        delimiter: '.'
                    });
                });
            });
            
            // Close modal when clicking the close button or cancel
            $('[data-dismiss="modal"]').on('click', function() {
                $('#addCategoryModal').hide();
            });
            
            // Close modal when clicking outside of it
            $(window).on('click', function(event) {
                if ($(event.target).is('#addCategoryModal')) {
                    $('#addCategoryModal').hide();
                }
            });
            
            // Handle save category button
            $('#saveCategoryBtn').on('click', function() {
                // Get form data
                const name = $('#category_name').val();
                const mitraId = $('#category_mitra_id').val();
                
                // Validate required fields
                if (!name) {
                    alert('Category name is required');
                    return;
                }
                
                // Parse formatted number to server format
                const parseFormattedNumber = (value) => {
                    if (!value) return 0;
                    return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
                };
                
                // Get prices
                const mitPriceCbm = parseFormattedNumber($('#category_mit_price_cbm').val());
                const mitPriceKg = parseFormattedNumber($('#category_mit_price_kg').val());
                const custPriceCbm = parseFormattedNumber($('#category_cust_price_cbm').val());
                const custPriceKg = parseFormattedNumber($('#category_cust_price_kg').val());
                
                // Show loading state
                const $btn = $(this);
                const originalText = $btn.text();
                $btn.prop('disabled', true).text('Saving...');
                
                // Create the category via AJAX
                $.ajax({
                    url: '{{ route('category-products.store-ajax') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name,
                        mitra_id: mitraId,
                        mit_price_cbm: mitPriceCbm,
                        mit_price_kg: mitPriceKg,
                        cust_price_cbm: custPriceCbm,
                        cust_price_kg: custPriceKg
                    },
                    success: function(response) {
                        if (response.success) {
                            // Add new option to select
                            const newOption = new Option(
                                `${response.data.name} (${response.data.mitra_name})`, 
                                response.data.id,
                                true,
                                true
                            );
                            
                            // Add data attributes
                            $(newOption).data('mit-price-cbm', response.data.mit_price_cbm);
                            $(newOption).data('mit-price-kg', response.data.mit_price_kg);
                            $(newOption).data('cust-price-cbm', response.data.cust_price_cbm);
                            $(newOption).data('cust-price-kg', response.data.cust_price_kg);
                            
                            // Add to select and trigger change
                            $('#category_product_id').append(newOption).trigger('change');
                            
                            // Close modal
                            $('#addCategoryModal').hide();
                            
                            // Show success message
                            alert('Category created successfully');
                        } else {
                            alert('Failed to create category: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorList = [];
                            
                            for (const field in errors) {
                                errorList.push(errors[field][0]);
                            }
                            
                            errorMessage = errorList.join('\n');
                        }
                        
                        alert('Failed to create category: ' + errorMessage);
                    },
                    complete: function() {
                        // Reset button state
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>