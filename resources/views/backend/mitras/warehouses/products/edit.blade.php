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
                                            data-mit-price-cbm-sea="{{ $category->mit_price_cbm_sea ?? 0 }}"
                                            data-mit-price-kg-sea="{{ $category->mit_price_kg_sea ?? 0 }}"
                                            data-cust-price-cbm-sea="{{ $category->cust_price_cbm_sea ?? 0 }}"
                                            data-cust-price-kg-sea="{{ $category->cust_price_kg_sea ?? 0 }}"
                                            data-mit-price-cbm-air="{{ $category->mit_price_cbm_air ?? 0 }}"
                                            data-mit-price-kg-air="{{ $category->mit_price_kg_air ?? 0 }}"
                                            data-cust-price-cbm-air="{{ $category->cust_price_cbm_air ?? 0 }}"
                                            data-cust-price-kg-air="{{ $category->cust_price_kg_air ?? 0 }}"
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
                        
                        <!-- Pricing Tab Navigation -->
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                            <ul class="flex flex-wrap -mb-px" id="pricing-tabs" role="tablist">
                                <li class="mr-2" role="presentation">
                                    <button type="button" 
                                            class="inline-block p-4 border-b-2 border-primary-500 rounded-t-lg font-medium text-primary-500" 
                                            id="sea-tab" 
                                            data-tabs-target="#sea-pricing" 
                                            role="tab" 
                                            aria-controls="sea-pricing" 
                                            aria-selected="true">
                                        <i class="fas fa-ship mr-1 text-blue-500"></i> SEA Pricing
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button type="button" 
                                            class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-medium" 
                                            id="air-tab" 
                                            data-tabs-target="#air-pricing" 
                                            role="tab" 
                                            aria-controls="air-pricing" 
                                            aria-selected="false">
                                        <i class="fas fa-plane mr-1 text-amber-500"></i> AIR Pricing
                                    </button>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Pricing Tab Contents -->
                        <div id="pricing-tab-content">
                            <!-- SEA Pricing Tab -->
                            <div class="block" id="sea-pricing" role="tabpanel" aria-labelledby="sea-tab">
                                <div class="p-3 mb-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border border-blue-100 dark:border-blue-800/50">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                        <p class="text-sm text-blue-700 dark:text-blue-300">
                                            Pricing for SEA shipments
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- CBM Pricing SEA -->
                                    <div class="border dark:border-slate-600 rounded-lg p-4">
                                        <h4 class="font-medium text-slate-700 dark:text-slate-300 mb-3">
                                            <i class="fas fa-box mr-1 text-blue-500"></i> CBM Pricing
                                        </h4>
                                        
                                        <!-- Mitra Price CBM SEA -->
                                        <div class="flex flex-col gap-1 mb-4">
                                            <label class="label mb-1 font-medium" for="mit_price_cbm_sea_display">Mitra Price</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" class="input pl-10 price-input" id="mit_price_cbm_sea_display" 
                                                       value="{{ old('mit_price_cbm_sea', $product->category->mit_price_cbm_sea ?? 0) }}" data-target="mit_price_cbm_sea" readonly />
                                                <input type="hidden" name="mit_price_cbm_sea" id="mit_price_cbm_sea" value="{{ old('mit_price_cbm_sea', $product->category->mit_price_cbm_sea ?? 0) }}" />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-xs text-slate-400">From Category</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Customer Price CBM SEA -->
                                        <div class="flex flex-col gap-1">
                                            <label class="label mb-1 font-medium" for="cust_price_cbm_sea_display">Customer Price</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" class="input pl-10 price-input" id="cust_price_cbm_sea_display" 
                                                       value="{{ old('cust_price_cbm_sea', $product->category->cust_price_cbm_sea ?? 0) }}" data-target="cust_price_cbm_sea" readonly />
                                                <input type="hidden" name="cust_price_cbm_sea" id="cust_price_cbm_sea" value="{{ old('cust_price_cbm_sea', $product->category->cust_price_cbm_sea ?? 0) }}" />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-xs text-slate-400">From Category</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- KG Pricing SEA -->
                                    <div class="border dark:border-slate-600 rounded-lg p-4">
                                        <h4 class="font-medium text-slate-700 dark:text-slate-300 mb-3">
                                            <i class="fas fa-weight mr-1 text-blue-500"></i> KG Pricing
                                        </h4>
                                        
                                        <!-- Mitra Price KG SEA -->
                                        <div class="flex flex-col gap-1 mb-4">
                                            <label class="label mb-1 font-medium" for="mit_price_kg_sea_display">Mitra Price</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" class="input pl-10 price-input" id="mit_price_kg_sea_display" 
                                                       value="{{ old('mit_price_kg_sea', $product->category->mit_price_kg_sea ?? 0) }}" data-target="mit_price_kg_sea" readonly />
                                                <input type="hidden" name="mit_price_kg_sea" id="mit_price_kg_sea" value="{{ old('mit_price_kg_sea', $product->category->mit_price_kg_sea ?? 0) }}" />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-xs text-slate-400">From Category</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Customer Price KG SEA -->
                                        <div class="flex flex-col gap-1">
                                            <label class="label mb-1 font-medium" for="cust_price_kg_sea_display">Customer Price</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" class="input pl-10 price-input" id="cust_price_kg_sea_display" 
                                                       value="{{ old('cust_price_kg_sea', $product->category->cust_price_kg_sea ?? 0) }}" data-target="cust_price_kg_sea" readonly />
                                                <input type="hidden" name="cust_price_kg_sea" id="cust_price_kg_sea" value="{{ old('cust_price_kg_sea', $product->category->cust_price_kg_sea ?? 0) }}" />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-xs text-slate-400">From Category</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- AIR Pricing Tab -->
                            <div class="hidden" id="air-pricing" role="tabpanel" aria-labelledby="air-tab">
                                <div class="p-3 mb-4 bg-amber-50 dark:bg-amber-900/10 rounded-lg border border-amber-100 dark:border-amber-800/50">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-amber-500 mr-2"></i>
                                        <p class="text-sm text-amber-700 dark:text-amber-300">
                                            Pricing for AIR shipments
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- CBM Pricing AIR -->
                                    <div class="border dark:border-slate-600 rounded-lg p-4">
                                        <h4 class="font-medium text-slate-700 dark:text-slate-300 mb-3">
                                            <i class="fas fa-box mr-1 text-amber-500"></i> CBM Pricing
                                        </h4>
                                        
                                        <!-- Mitra Price CBM AIR -->
                                        <div class="flex flex-col gap-1 mb-4">
                                            <label class="label mb-1 font-medium" for="mit_price_cbm_air_display">Mitra Price</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" class="input pl-10 price-input" id="mit_price_cbm_air_display" 
                                                       value="{{ old('mit_price_cbm_air', $product->category->mit_price_cbm_air ?? 0) }}" data-target="mit_price_cbm_air" readonly />
                                                <input type="hidden" name="mit_price_cbm_air" id="mit_price_cbm_air" value="{{ old('mit_price_cbm_air', $product->category->mit_price_cbm_air ?? 0) }}" />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-xs text-slate-400">From Category</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Customer Price CBM AIR -->
                                        <div class="flex flex-col gap-1">
                                            <label class="label mb-1 font-medium" for="cust_price_cbm_air_display">Customer Price</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" class="input pl-10 price-input" id="cust_price_cbm_air_display" 
                                                       value="{{ old('cust_price_cbm_air', $product->category->cust_price_cbm_air ?? 0) }}" data-target="cust_price_cbm_air" readonly />
                                                <input type="hidden" name="cust_price_cbm_air" id="cust_price_cbm_air" value="{{ old('cust_price_cbm_air', $product->category->cust_price_cbm_air ?? 0) }}" />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-xs text-slate-400">From Category</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- KG Pricing AIR -->
                                    <div class="border dark:border-slate-600 rounded-lg p-4">
                                        <h4 class="font-medium text-slate-700 dark:text-slate-300 mb-3">
                                            <i class="fas fa-weight mr-1 text-amber-500"></i> KG Pricing
                                        </h4>
                                        
                                        <!-- Mitra Price KG AIR -->
                                        <div class="flex flex-col gap-1 mb-4">
                                            <label class="label mb-1 font-medium" for="mit_price_kg_air_display">Mitra Price</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" class="input pl-10 price-input" id="mit_price_kg_air_display" 
                                                       value="{{ old('mit_price_kg_air', $product->category->mit_price_kg_air ?? 0) }}" data-target="mit_price_kg_air" readonly />
                                                <input type="hidden" name="mit_price_kg_air" id="mit_price_kg_air" value="{{ old('mit_price_kg_air', $product->category->mit_price_kg_air ?? 0) }}" />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-xs text-slate-400">From Category</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Customer Price KG AIR -->
                                        <div class="flex flex-col gap-1">
                                            <label class="label mb-1 font-medium" for="cust_price_kg_air_display">Customer Price</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" class="input pl-10 price-input" id="cust_price_kg_air_display" 
                                                       value="{{ old('cust_price_kg_air', $product->category->cust_price_kg_air ?? 0) }}" data-target="cust_price_kg_air" readonly />
                                                <input type="hidden" name="cust_price_kg_air" id="cust_price_kg_air" value="{{ old('cust_price_kg_air', $product->category->cust_price_kg_air ?? 0) }}" />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <span class="text-xs text-slate-400">From Category</span>
                                                </div>
                                            </div>
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
                        
                        <!-- Pricing Tab Navigation in Modal -->
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-2">
                            <ul class="flex flex-wrap -mb-px" id="modal-pricing-tabs" role="tablist">
                                <li class="mr-2" role="presentation">
                                    <button type="button" 
                                            class="inline-block p-3 border-b-2 border-primary-500 rounded-t-lg font-medium text-primary-500 text-sm" 
                                            id="modal-sea-tab" 
                                            data-tabs-target="#modal-sea-pricing" 
                                            role="tab" 
                                            aria-controls="modal-sea-pricing" 
                                            aria-selected="true">
                                        <i class="fas fa-ship mr-1 text-blue-500"></i> SEA Pricing
                                    </button>
                                </li>
                                <li role="presentation">
                                    <button type="button" 
                                            class="inline-block p-3 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-medium text-sm" 
                                            id="modal-air-tab" 
                                            data-tabs-target="#modal-air-pricing" 
                                            role="tab" 
                                            aria-controls="modal-air-pricing" 
                                            aria-selected="false">
                                        <i class="fas fa-plane mr-1 text-amber-500"></i> AIR Pricing
                                    </button>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Pricing Tab Content in Modal -->
                        <div id="modal-pricing-tab-content">
                            <!-- SEA Pricing Tab in Modal -->
                            <div class="block" id="modal-sea-pricing" role="tabpanel" aria-labelledby="modal-sea-tab">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-1">
                                        <label class="label mb-1 font-medium" for="category_mit_price_cbm_sea">Mitra Price (CBM)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" class="input pl-10 price-input" id="category_mit_price_cbm_sea" name="mit_price_cbm_sea" value="0" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1">
                                        <label class="label mb-1 font-medium" for="category_mit_price_kg_sea">Mitra Price (KG)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" class="input pl-10 price-input" id="category_mit_price_kg_sea" name="mit_price_kg_sea" value="0" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1">
                                        <label class="label mb-1 font-medium" for="category_cust_price_cbm_sea">Customer Price (CBM)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" class="input pl-10 price-input" id="category_cust_price_cbm_sea" name="cust_price_cbm_sea" value="0" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1">
                                        <label class="label mb-1 font-medium" for="category_cust_price_kg_sea">Customer Price (KG)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" class="input pl-10 price-input" id="category_cust_price_kg_sea" name="cust_price_kg_sea" value="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- AIR Pricing Tab in Modal -->
                            <div class="hidden" id="modal-air-pricing" role="tabpanel" aria-labelledby="modal-air-tab">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-1">
                                        <label class="label mb-1 font-medium" for="category_mit_price_cbm_air">Mitra Price (CBM)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" class="input pl-10 price-input" id="category_mit_price_cbm_air" name="mit_price_cbm_air" value="0" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1">
                                        <label class="label mb-1 font-medium" for="category_mit_price_kg_air">Mitra Price (KG)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" class="input pl-10 price-input" id="category_mit_price_kg_air" name="mit_price_kg_air" value="0" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1">
                                        <label class="label mb-1 font-medium" for="category_cust_price_cbm_air">Customer Price (CBM)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" class="input pl-10 price-input" id="category_cust_price_cbm_air" name="cust_price_cbm_air" value="0" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1">
                                        <label class="label mb-1 font-medium" for="category_cust_price_kg_air">Customer Price (KG)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center px-3 pointer-events-none">
                                                <span class="text-gray-500">Rp</span>
                                            </div>
                                            <input type="text" class="input pl-10 price-input" id="category_cust_price_kg_air" name="cust_price_kg_air" value="0" />
                                        </div>
                                    </div>
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
                    if (targetField) {
                        $('#' + targetField).val(numericValue);
                    }
                });
                
                return true;
            });
            
            // Tab functionality for main pricing tabs
            const tabButtons = document.querySelectorAll('#pricing-tabs button');
            const tabContents = document.querySelectorAll('#pricing-tab-content > div');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const target = document.querySelector(button.dataset.tabsTarget);
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('block');
                    });
                    
                    // Remove active state from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-primary-500', 'text-primary-500');
                        btn.classList.add('border-transparent');
                        btn.setAttribute('aria-selected', 'false');
                    });
                    
                    // Show selected tab content
                    target.classList.remove('hidden');
                    target.classList.add('block');
                    
                    // Set active state on clicked button
                    button.classList.remove('border-transparent');
                    button.classList.add('border-primary-500', 'text-primary-500');
                    button.setAttribute('aria-selected', 'true');
                });
            });
            
            // Tab functionality for modal pricing tabs
            const modalTabButtons = document.querySelectorAll('#modal-pricing-tabs button');
            const modalTabContents = document.querySelectorAll('#modal-pricing-tab-content > div');
            
            modalTabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const target = document.querySelector(button.dataset.tabsTarget);
                    
                    // Hide all tab contents
                    modalTabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('block');
                    });
                    
                    // Remove active state from all buttons
                    modalTabButtons.forEach(btn => {
                        btn.classList.remove('border-primary-500', 'text-primary-500');
                        btn.classList.add('border-transparent');
                        btn.setAttribute('aria-selected', 'false');
                    });
                    
                    // Show selected tab content
                    target.classList.remove('hidden');
                    target.classList.add('block');
                    
                    // Set active state on clicked button
                    button.classList.remove('border-transparent');
                    button.classList.add('border-primary-500', 'text-primary-500');
                    button.setAttribute('aria-selected', 'true');
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
                    
                    // Get SEA prices from selected category
                    const mitPriceCbmSea = $selected.data('mit-price-cbm-sea') || 0;
                    const mitPriceKgSea = $selected.data('mit-price-kg-sea') || 0;
                    const custPriceCbmSea = $selected.data('cust-price-cbm-sea') || 0;
                    const custPriceKgSea = $selected.data('cust-price-kg-sea') || 0;
                    
                    // Get AIR prices from selected category
                    const mitPriceCbmAir = $selected.data('mit-price-cbm-air') || 0;
                    const mitPriceKgAir = $selected.data('mit-price-kg-air') || 0;
                    const custPriceCbmAir = $selected.data('cust-price-cbm-air') || 0;
                    const custPriceKgAir = $selected.data('cust-price-kg-air') || 0;
                    
                    // Update SEA display fields
                    $('#mit_price_cbm_sea_display').val(formatPrice(mitPriceCbmSea));
                    $('#mit_price_kg_sea_display').val(formatPrice(mitPriceKgSea));
                    $('#cust_price_cbm_sea_display').val(formatPrice(custPriceCbmSea));
                    $('#cust_price_kg_sea_display').val(formatPrice(custPriceKgSea));
                    
                    // Update AIR display fields
                    $('#mit_price_cbm_air_display').val(formatPrice(mitPriceCbmAir));
                    $('#mit_price_kg_air_display').val(formatPrice(mitPriceKgAir));
                    $('#cust_price_cbm_air_display').val(formatPrice(custPriceCbmAir));
                    $('#cust_price_kg_air_display').val(formatPrice(custPriceKgAir));
                    
                    // Update SEA hidden fields
                    $('#mit_price_cbm_sea').val(mitPriceCbmSea);
                    $('#mit_price_kg_sea').val(mitPriceKgSea);
                    $('#cust_price_cbm_sea').val(custPriceCbmSea);
                    $('#cust_price_kg_sea').val(custPriceKgSea);
                    
                    // Update AIR hidden fields
                    $('#mit_price_cbm_air').val(mitPriceCbmAir);
                    $('#mit_price_kg_air').val(mitPriceKgAir);
                    $('#cust_price_cbm_air').val(custPriceCbmAir);
                    $('#cust_price_kg_air').val(custPriceKgAir);
                } else {
                    // Reset all SEA fields if no category selected
                    $('#mit_price_cbm_sea_display, #mit_price_kg_sea_display, #cust_price_cbm_sea_display, #cust_price_kg_sea_display').val('0');
                    $('#mit_price_cbm_sea, #mit_price_kg_sea, #cust_price_cbm_sea, #cust_price_kg_sea').val('0');
                    
                    // Reset all AIR fields if no category selected
                    $('#mit_price_cbm_air_display, #mit_price_kg_air_display, #cust_price_cbm_air_display, #cust_price_kg_air_display').val('0');
                    $('#mit_price_cbm_air, #mit_price_kg_air, #cust_price_cbm_air, #cust_price_kg_air').val('0');
                }
            });
            
            // Trigger change to initialize values if a category is already selected (e.g. from old input)
            $('#category_product_id').trigger('change');
            
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
                
                // Get SEA prices
                const mitPriceCbmSea = parseFormattedNumber($('#category_mit_price_cbm_sea').val());
                const mitPriceKgSea = parseFormattedNumber($('#category_mit_price_kg_sea').val());
                const custPriceCbmSea = parseFormattedNumber($('#category_cust_price_cbm_sea').val());
                const custPriceKgSea = parseFormattedNumber($('#category_cust_price_kg_sea').val());
                
                // Get AIR prices
                const mitPriceCbmAir = parseFormattedNumber($('#category_mit_price_cbm_air').val());
                const mitPriceKgAir = parseFormattedNumber($('#category_mit_price_kg_air').val());
                const custPriceCbmAir = parseFormattedNumber($('#category_cust_price_cbm_air').val());
                const custPriceKgAir = parseFormattedNumber($('#category_cust_price_kg_air').val());
                
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
                        // SEA pricing
                        mit_price_cbm_sea: mitPriceCbmSea,
                        mit_price_kg_sea: mitPriceKgSea,
                        cust_price_cbm_sea: custPriceCbmSea,
                        cust_price_kg_sea: custPriceKgSea,
                        // AIR pricing
                        mit_price_cbm_air: mitPriceCbmAir,
                        mit_price_kg_air: mitPriceKgAir,
                        cust_price_cbm_air: custPriceCbmAir,
                        cust_price_kg_air: custPriceKgAir
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
                            
                            // Add SEA data attributes
                            $(newOption).data('mit-price-cbm-sea', response.data.mit_price_cbm_sea);
                            $(newOption).data('mit-price-kg-sea', response.data.mit_price_kg_sea);
                            $(newOption).data('cust-price-cbm-sea', response.data.cust_price_cbm_sea);
                            $(newOption).data('cust-price-kg-sea', response.data.cust_price_kg_sea);
                            
                            // Add AIR data attributes
                            $(newOption).data('mit-price-cbm-air', response.data.mit_price_cbm_air);
                            $(newOption).data('mit-price-kg-air', response.data.mit_price_kg_air);
                            $(newOption).data('cust-price-cbm-air', response.data.cust_price_cbm_air);
                            $(newOption).data('cust-price-kg-air', response.data.cust_price_kg_air);
                            
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