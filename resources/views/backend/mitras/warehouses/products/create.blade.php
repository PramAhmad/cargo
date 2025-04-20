<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Add Product" page="Warehouses" />
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

                <form action="{{ route('mitra.warehouses.products.store', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" method="POST" id="productForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="name">Product Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name') }}" required />
                        </div>
                        
                        <!-- Parent Product -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="parent_id">Parent Product</label>
                            <select id="parent_id" name="parent_id" class="select">
                                <option value="">None (Top Level Product)</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                                        {{ str_repeat('— ', $p->level) }} {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Optional. Select a parent to create a sub-product.</p>
                        </div>
                        
                        <!-- Category Product -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="category_product_id">Product Category</label>
                            <select id="category_product_id" name="category_product_id" class="select">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_product_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Mitra Price CBM -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="mit_price_cbm_display">Mitra Price (CBM)</label>
                            <div class="relative">
                           
                                <input type="text" class="input pl-10 price-input" id="mit_price_cbm_display" 
                                       value="{{ old('mit_price_cbm', '0') }}" data-target="mit_price_cbm" />
                                <input type="hidden" name="mit_price_cbm" id="mit_price_cbm" value="{{ old('mit_price_cbm', '0') }}" />
                            </div>
                        </div>
                        
                        <!-- Mitra Price KG -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="mit_price_kg_display">Mitra Price (KG)</label>
                            <div class="relative">
                           
                                <input type="text" class="input pl-10 price-input" id="mit_price_kg_display" 
                                       value="{{ old('mit_price_kg', '0') }}" data-target="mit_price_kg" />
                                <input type="hidden" name="mit_price_kg" id="mit_price_kg" value="{{ old('mit_price_kg', '0') }}" />
                            </div>
                        </div>
                        
                        <!-- Customer Price CBM -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="cust_price_cbm_display">Customer Price (CBM)</label>
                            <div class="relative">
                           
                                <input type="text" class="input pl-10 price-input" id="cust_price_cbm_display" 
                                       value="{{ old('cust_price_cbm', '0') }}" data-target="cust_price_cbm" />
                                <input type="hidden" name="cust_price_cbm" id="cust_price_cbm" value="{{ old('cust_price_cbm', '0') }}" />
                            </div>
                        </div>
                        
                        <!-- Customer Price KG -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="cust_price_kg_display">Customer Price (KG)</label>
                            <div class="relative">
                           
                                <input type="text" class="input pl-10 price-input" id="cust_price_kg_display" 
                                       value="{{ old('cust_price_kg', '0') }}" data-target="cust_price_kg" />
                                <input type="hidden" name="cust_price_kg" id="cust_price_kg" value="{{ old('cust_price_kg', '0') }}" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end border-t pt-4">
                        <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary ml-2">Save Product</button>
                    </div>
                </form>
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
            
            // Handle form submission - convert formatted values to numbers
            $('#productForm').on('submit', function() {
                $('.price-input').each(function() {
                    const displayValue = $(this).val();
                    // Convert "10.000,50" to "10000.50" for server processing
                    let numericValue = displayValue.replace(/\./g, '').replace(',', '.');
                    
                    // If empty or NaN, default to 0
                    if (!numericValue || isNaN(parseFloat(numericValue))) {
                        numericValue = '0';
                    }
                    
                    // Update the hidden input with the numeric value
                    const targetField = $(this).data('target');
                    $('#' + targetField).val(numericValue);
                });
                
                return true;
            });
        });
    </script>
    @endpush
</x-app-layout>