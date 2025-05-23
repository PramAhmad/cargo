<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Manage Products" page="Warehouses" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('mitras.edit', $mitra->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Mitra
                </a>
                <span class="mx-3 text-slate-400">|</span>
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-primary-50 dark:bg-slate-700 flex items-center justify-center mr-3">
                        <i class="fas fa-warehouse text-primary-500"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-slate-700 dark:text-slate-300">{{ $warehouse->name }}</h4>
                        <p class="text-sm text-slate-500">
                            <span class="badge {{ $warehouse->type == 'sea' ? 'badge-soft-primary' : 'badge-soft-warning' }}">
                                {{ ucfirst($warehouse->type) }} Freight
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('mitra.warehouses.products.create', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Add Product
            </a>
        </div>

        <div class="card">
            <div class="card-body p-6">
                @if(session('success'))
                    <div class="alert alert-success mb-4">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
                @endif

                <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                    <div class="w-full md:w-1/3">
                        <form method="GET" action="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}">
                            <div class="flex gap-2">
                                <select name="category" class="select w-full" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('category'))
                                    <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" 
                                       class="btn btn-icon btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="w-full md:w-1/3">
                        <form method="GET" action="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <div class="flex gap-2">
                                <input type="text" name="search" class="input w-full" placeholder="Search products..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-icon btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id, 'category' => request('category')]) }}" 
                                       class="btn btn-icon btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

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
                        @if($products->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="table" id="seaProductTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Mitra Price (CBM)</th>
                                            <th>Mitra Price (KG)</th>
                                            <th>Customer Price (CBM)</th>
                                            <th>Customer Price (KG)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            @include('backend.mitras.warehouses.products.product_row', ['product' => $product, 'level' => 0, 'freightType' => 'sea'])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-slate-50 dark:bg-slate-800 p-8 rounded-md text-center">
                                <div class="mb-4">
                                    <i class="fas fa-box text-5xl text-slate-400 mb-4"></i>
                                    <h3 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-2">No Products Found</h3>
                                    <p class="text-slate-500">
                                        @if(request('search'))
                                            No products matching "{{ request('search') }}"
                                        @elseif(request('category'))
                                            No products in this category
                                        @else
                                            This warehouse doesn't have any products yet.
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('mitra.warehouses.products.create', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i> Add First Product
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <!-- AIR Pricing Tab -->
                    <div class="hidden" id="air-pricing" role="tabpanel" aria-labelledby="air-tab">
                        @if($products->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="table" id="airProductTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Mitra Price (CBM)</th>
                                            <th>Mitra Price (KG)</th>
                                            <th>Customer Price (CBM)</th>
                                            <th>Customer Price (KG)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            @include('backend.mitras.warehouses.products.product_row', ['product' => $product, 'level' => 0, 'freightType' => 'air'])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-slate-50 dark:bg-slate-800 p-8 rounded-md text-center">
                                <div class="mb-4">
                                    <i class="fas fa-box text-5xl text-slate-400 mb-4"></i>
                                    <h3 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-2">No Products Found</h3>
                                    <p class="text-slate-500">
                                        @if(request('search'))
                                            No products matching "{{ request('search') }}"
                                        @elseif(request('category'))
                                            No products in this category
                                        @else
                                            This warehouse doesn't have any products yet.
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('mitra.warehouses.products.create', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i> Add First Product
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('styles')
    <style>
        .swal2-confirm.swal2-styled {
            background-color: #4f46e5 !important;
            color: #fff;
            border: #4f46e5 !important;
            border-radius: 0.375rem !important;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Tab functionality for pricing tabs
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
            
            // Handle delete product
            $(document).on('click', '.delete-product-btn', function() {
                const productId = $(this).data('product-id');
                const productName = $(this).data('product-name');
                
                Swal.fire({
                    title: 'Delete Product?',
                    html: `Are you sure you want to delete product <strong>${productName}</strong>?<br>This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Delete',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary mx-5',
                        cancelButton: 'btn btn-secondary'
                    },
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/mitras/{{ $mitra->id }}/warehouses/{{ $warehouse->id }}/products/${productId}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    
                                    // Remove row from both tables
                                    $(`tr[data-product-id="${productId}"]`).fadeOut(400, function() {
                                        $(this).remove();
                                        
                                        // Check if SEA table is empty
                                        if ($('#seaProductTable tbody tr').length === 0) {
                                            $('#sea-pricing .overflow-x-auto').replaceWith(`
                                                <div class="bg-slate-50 dark:bg-slate-800 p-8 rounded-md text-center">
                                                    <div class="mb-4">
                                                        <i class="fas fa-box text-5xl text-slate-400 mb-4"></i>
                                                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-2">No Products Found</h3>
                                                        <p class="text-slate-500">This warehouse doesn't have any products yet.</p>
                                                    </div>
                                                    <a href="{{ route('mitra.warehouses.products.create', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" class="btn btn-primary">
                                                        <i class="fas fa-plus mr-2"></i> Add First Product
                                                    </a>
                                                </div>
                                            `);
                                        }
                                        
                                        // Check if AIR table is empty
                                        if ($('#airProductTable tbody tr').length === 0) {
                                            $('#air-pricing .overflow-x-auto').replaceWith(`
                                                <div class="bg-slate-50 dark:bg-slate-800 p-8 rounded-md text-center">
                                                    <div class="mb-4">
                                                        <i class="fas fa-box text-5xl text-slate-400 mb-4"></i>
                                                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-2">No Products Found</h3>
                                                        <p class="text-slate-500">This warehouse doesn't have any products yet.</p>
                                                    </div>
                                                    <a href="{{ route('mitra.warehouses.products.create', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" class="btn btn-primary">
                                                        <i class="fas fa-plus mr-2"></i> Add First Product
                                                    </a>
                                                </div>
                                            `);
                                        }
                                    });
                                }
                            },
                            error: function(xhr) {
                                const response = xhr.responseJSON;
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to delete product. Please try again.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>