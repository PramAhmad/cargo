<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Category Product Details" page="Products" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <div class="card">
            <div class="card-header bg-primary-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                <h5 class="card-title">
                    <i class="fas fa-tag mr-2 text-primary-500"></i>
                    {{ $categoryProduct->name }}
                </h5>
            </div>
            <div class="card-body p-6">
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-1">{{ $categoryProduct->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Mitra:</span> {{ $categoryProduct->mitra->name ?? 'N/A' }}
                            </p>
                        </div>
                        
                        <div class="mt-4 md:mt-0">
                            <div class="flex space-x-2">
                                <a href="{{ route('category-products.edit', $categoryProduct->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <a href="{{ route('category-products.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pricing Tabs -->
                <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
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
                
                <!-- Tab Contents -->
                <div id="pricing-tab-content">
                    <!-- SEA Pricing Tab -->
                    <div class="block" id="sea-pricing" role="tabpanel" aria-labelledby="sea-tab">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Mitra SEA Pricing -->
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-blue-200 dark:border-blue-800/30 p-4">
                                <h4 class="text-lg font-medium mb-4 text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-ship mr-2"></i> Mitra SEA Pricing
                                </h4>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Price per CBM:</span>
                                        <span class="font-medium text-lg">Rp {{ number_format($categoryProduct->mit_price_cbm_sea, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Price per KG:</span>
                                        <span class="font-medium text-lg">Rp {{ number_format($categoryProduct->mit_price_kg_sea, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Customer SEA Pricing -->
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-blue-200 dark:border-blue-800/30 p-4">
                                <h4 class="text-lg font-medium mb-4 text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-users mr-2"></i> Customer SEA Pricing
                                </h4>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Price per CBM:</span>
                                        <span class="font-medium text-lg">Rp {{ number_format($categoryProduct->cust_price_cbm_sea, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Price per KG:</span>
                                        <span class="font-medium text-lg">Rp {{ number_format($categoryProduct->cust_price_kg_sea, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- AIR Pricing Tab -->
                    <div class="hidden" id="air-pricing" role="tabpanel" aria-labelledby="air-tab">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Mitra AIR Pricing -->
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-amber-200 dark:border-amber-800/30 p-4">
                                <h4 class="text-lg font-medium mb-4 text-amber-600 dark:text-amber-400">
                                    <i class="fas fa-plane mr-2"></i> Mitra AIR Pricing
                                </h4>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Price per CBM:</span>
                                        <span class="font-medium text-lg">Rp {{ number_format($categoryProduct->mit_price_cbm_air, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Price per KG:</span>
                                        <span class="font-medium text-lg">Rp {{ number_format($categoryProduct->mit_price_kg_air, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Customer AIR Pricing -->
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-amber-200 dark:border-amber-800/30 p-4">
                                <h4 class="text-lg font-medium mb-4 text-amber-600 dark:text-amber-400">
                                    <i class="fas fa-users mr-2"></i> Customer AIR Pricing
                                </h4>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Price per CBM:</span>
                                        <span class="font-medium text-lg">Rp {{ number_format($categoryProduct->cust_price_cbm_air, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Price per KG:</span>
                                        <span class="font-medium text-lg">Rp {{ number_format($categoryProduct->cust_price_kg_air, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</x-app-layout>