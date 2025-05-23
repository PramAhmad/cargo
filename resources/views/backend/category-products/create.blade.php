<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Add Category Product" page="Products" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <div class="card">
            <div class="card-body p-6">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('category-products.store') }}" method="POST">
                    @csrf
                    <div class="grid w-full grid-cols-1 gap-4 py-2 md:grid-cols-2">
                        <div class="flex w-full flex-col md:w-auto">
                            <label class="label label-required mb-1 font-medium" for="name">Category Product Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name') }}" required />
                        </div>
                        
                        <div class="flex w-full flex-col md:w-auto">
                            <label class="label label-required mb-1 font-medium" for="mitra_id">Mitra</label>
                            <select class="select" id="mitra_id" name="mitra_id" required>
                                <option value="">Select Mitra</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" {{ old('mitra_id') == $mitra->id ? 'selected' : '' }}>
                                        {{ $mitra->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Pricing Tab Navigation -->
                    <div class="mt-6">
                        <h3 class="font-medium text-lg mb-3">Pricing Information</h3>
                        
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
                                        <i class="fas fa-ship mr-1"></i> SEA Pricing
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
                                        <i class="fas fa-plane mr-1"></i> AIR Pricing
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
                                            Configure pricing for SEA shipments
                                        </p>
                                        <button type="button" id="copy-sea-to-air" class="btn btn-sm btn-light-primary ml-auto">
                                            <i class="fas fa-copy mr-1"></i> Copy to AIR
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="grid w-full grid-cols-1 gap-4 py-2 md:grid-cols-2">
                                    <div class="flex w-full flex-col md:w-auto">
                                        <label class="label label-required mb-1 font-medium" for="mit_price_cbm_sea">Mitra Price (CBM)</label>
                                        <div class="relative">
                                          
                                            <input type="text" class="input pl-10 price-input" id="mit_price_cbm_sea" name="mit_price_cbm_sea" 
                                                value="{{ old('mit_price_cbm_sea', 0) }}" required />
                                            <input type="hidden" id="mit_price_cbm_sea_raw" name="mit_price_cbm_sea_raw" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex w-full flex-col md:w-auto">
                                        <label class="label label-required mb-1 font-medium" for="mit_price_kg_sea">Mitra Price (KG)</label>
                                        <div class="relative">
                                          
                                            <input type="text" class="input pl-10 price-input" id="mit_price_kg_sea" name="mit_price_kg_sea" 
                                                value="{{ old('mit_price_kg_sea', 0) }}" required />
                                            <input type="hidden" id="mit_price_kg_sea_raw" name="mit_price_kg_sea_raw" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex w-full flex-col md:w-auto">
                                        <label class="label label-required mb-1 font-medium" for="cust_price_cbm_sea">Customer Price (CBM)</label>
                                        <div class="relative">
                                          
                                            <input type="text" class="input pl-10 price-input" id="cust_price_cbm_sea" name="cust_price_cbm_sea" 
                                                value="{{ old('cust_price_cbm_sea', 0) }}" required />
                                            <input type="hidden" id="cust_price_cbm_sea_raw" name="cust_price_cbm_sea_raw" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex w-full flex-col md:w-auto">
                                        <label class="label label-required mb-1 font-medium" for="cust_price_kg_sea">Customer Price (KG)</label>
                                        <div class="relative">
                                          
                                            <input type="text" class="input pl-10 price-input" id="cust_price_kg_sea" name="cust_price_kg_sea" 
                                                value="{{ old('cust_price_kg_sea', 0) }}" required />
                                            <input type="hidden" id="cust_price_kg_sea_raw" name="cust_price_kg_sea_raw" />
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
                                            Configure pricing for AIR shipments
                                        </p>
                                        <button type="button" id="copy-air-to-sea" class="btn btn-sm btn-light-warning ml-auto">
                                            <i class="fas fa-copy mr-1"></i> Copy to SEA
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="grid w-full grid-cols-1 gap-4 py-2 md:grid-cols-2">
                                    <div class="flex w-full flex-col md:w-auto">
                                        <label class="label label-required mb-1 font-medium" for="mit_price_cbm_air">Mitra Price (CBM)</label>
                                        <div class="relative">
                                          
                                            <input type="text" class="input pl-10 price-input" id="mit_price_cbm_air" name="mit_price_cbm_air" 
                                                value="{{ old('mit_price_cbm_air', 0) }}" required />
                                            <input type="hidden" id="mit_price_cbm_air_raw" name="mit_price_cbm_air_raw" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex w-full flex-col md:w-auto">
                                        <label class="label label-required mb-1 font-medium" for="mit_price_kg_air">Mitra Price (KG)</label>
                                        <div class="relative">
                                          
                                            <input type="text" class="input pl-10 price-input" id="mit_price_kg_air" name="mit_price_kg_air" 
                                                value="{{ old('mit_price_kg_air', 0) }}" required />
                                            <input type="hidden" id="mit_price_kg_air_raw" name="mit_price_kg_air_raw" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex w-full flex-col md:w-auto">
                                        <label class="label label-required mb-1 font-medium" for="cust_price_cbm_air">Customer Price (CBM)</label>
                                        <div class="relative">
                                          
                                            <input type="text" class="input pl-10 price-input" id="cust_price_cbm_air" name="cust_price_cbm_air" 
                                                value="{{ old('cust_price_cbm_air', 0) }}" required />
                                            <input type="hidden" id="cust_price_cbm_air_raw" name="cust_price_cbm_air_raw" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex w-full flex-col md:w-auto">
                                        <label class="label label-required mb-1 font-medium" for="cust_price_kg_air">Customer Price (KG)</label>
                                        <div class="relative">
                                          
                                            <input type="text" class="input pl-10 price-input" id="cust_price_kg_air" name="cust_price_kg_air" 
                                                value="{{ old('cust_price_kg_air', 0) }}" required />
                                            <input type="hidden" id="cust_price_kg_air_raw" name="cust_price_kg_air_raw" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('category-products.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
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
            const cleaveInstances = {};
            
            $('.price-input').each(function() {
                const inputId = $(this).attr('id');
                cleaveInstances[inputId] = new Cleave('#' + inputId, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.',
                    numeralDecimalScale: 2,
                    onValueChanged: function(e) {
                        // Store raw value in hidden field for form submission
                        const rawValue = e.target.rawValue;
                        $('#' + inputId + '_raw').val(rawValue);
                    }
                });
            });

            // Tab functionality
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
            
            // Copy functionality from SEA to AIR with formatted values
            $('#copy-sea-to-air').click(function() {
                // Get displayed values
                const mitCbmSea = $('#mit_price_cbm_sea').val();
                const mitKgSea = $('#mit_price_kg_sea').val();
                const custCbmSea = $('#cust_price_cbm_sea').val();
                const custKgSea = $('#cust_price_kg_sea').val();
                
                // Get raw values for hidden fields
                const mitCbmSeaRaw = $('#mit_price_cbm_sea_raw').val();
                const mitKgSeaRaw = $('#mit_price_kg_sea_raw').val();
                const custCbmSeaRaw = $('#cust_price_cbm_sea_raw').val();
                const custKgSeaRaw = $('#cust_price_kg_sea_raw').val();
                
                // Set displayed values for AIR fields
                cleaveInstances['mit_price_cbm_air'].setRawValue(mitCbmSeaRaw);
                cleaveInstances['mit_price_kg_air'].setRawValue(mitKgSeaRaw);
                cleaveInstances['cust_price_cbm_air'].setRawValue(custCbmSeaRaw);
                cleaveInstances['cust_price_kg_air'].setRawValue(custKgSeaRaw);
                
                // Set raw values for hidden fields
                $('#mit_price_cbm_air_raw').val(mitCbmSeaRaw);
                $('#mit_price_kg_air_raw').val(mitKgSeaRaw);
                $('#cust_price_cbm_air_raw').val(custCbmSeaRaw);
                $('#cust_price_kg_air_raw').val(custKgSeaRaw);

                // Show the AIR tab after copying
                $('#air-tab').click();
            });
            
            // Copy functionality from AIR to SEA with formatted values
            $('#copy-air-to-sea').click(function() {
                // Get displayed values
                const mitCbmAir = $('#mit_price_cbm_air').val();
                const mitKgAir = $('#mit_price_kg_air').val();
                const custCbmAir = $('#cust_price_cbm_air').val();
                const custKgAir = $('#cust_price_kg_air').val();
                
                // Get raw values for hidden fields
                const mitCbmAirRaw = $('#mit_price_cbm_air_raw').val();
                const mitKgAirRaw = $('#mit_price_kg_air_raw').val();
                const custCbmAirRaw = $('#cust_price_cbm_air_raw').val();
                const custKgAirRaw = $('#cust_price_kg_air_raw').val();
                
                // Set displayed values for SEA fields
                cleaveInstances['mit_price_cbm_sea'].setRawValue(mitCbmAirRaw);
                cleaveInstances['mit_price_kg_sea'].setRawValue(mitKgAirRaw);
                cleaveInstances['cust_price_cbm_sea'].setRawValue(custCbmAirRaw);
                cleaveInstances['cust_price_kg_sea'].setRawValue(custKgAirRaw);
                
                // Set raw values for hidden fields
                $('#mit_price_cbm_sea_raw').val(mitCbmAirRaw);
                $('#mit_price_kg_sea_raw').val(mitKgAirRaw);
                $('#cust_price_cbm_sea_raw').val(custCbmAirRaw);
                $('#cust_price_kg_sea_raw').val(custKgAirRaw);
                
                // Show the SEA tab after copying
                $('#sea-tab').click();
            });
            
            // Form submission preparation
            $('form').submit(function() {
                // Replace formatted values with raw values before submission
                $('.price-input').each(function() {
                    const inputId = $(this).attr('id');
                    const rawValue = $('#' + inputId + '_raw').val();
                    
                    // If raw value exists, use it; otherwise, use 0
                    $(this).val(rawValue || '0');
                });
                
                return true;
            });
        });
    </script>
    @endpush
</x-app-layout>