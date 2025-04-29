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

                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table" id="productTable">
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
                                    @include('backend.mitras.warehouses.products.product_row', ['product' => $product, 'level' => 0])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
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
                @endif
            </div>
        </div>
    </div>
    @push('styles')
    <style>
        .swal2-confirm swal2-styled {
        background-color: #4f46e5 !important;
        color: #fff;
        border: #4f46e5 !important;
        border-radius: 0.375rem !important;
    }
    </style>

    @endpush
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
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
                                    
                                    // Remove row from table
                                    $(`tr[data-product-id="${productId}"]`).fadeOut(400, function() {
                                        $(this).remove();
                                        
                                        // Check if table is empty
                                        if ($('#productTable tbody tr').length === 0) {
                                            $('#productTable').replaceWith(`
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