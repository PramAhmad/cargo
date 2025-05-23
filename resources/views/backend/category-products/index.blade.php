<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Category Product List" page="Products" />
    <!-- Page Title Ends -->

    <!-- Category Product List Starts -->
    <div class="space-y-4">
        <!-- Category Product Header Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row md:gap-y-0">
            <div class="flex w-full flex-col md:flex-row gap-2 md:gap-3 md:w-auto">
                <!-- Category Product Search Starts -->
                <form
                    action="{{ route('category-products.index') }}"
                    method="GET"
                    class="group flex h-10 w-full items-center rounded-primary border border-transparent bg-white shadow-sm focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-inset focus-within:ring-primary-500 dark:border-transparent dark:bg-slate-800 dark:focus-within:border-primary-500 md:w-72">
                    <div class="flex h-full items-center px-2">
                        <i class="h-4 text-slate-400 group-focus-within:text-primary-500" data-feather="search"></i>
                    </div>
                    <input
                        class="h-full w-full border-transparent bg-transparent px-0 text-sm placeholder-slate-400 placeholder:text-sm focus:border-transparent focus:outline-none focus:ring-0"
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}" 
                        placeholder="Search category product" />
                    
                    <input type="hidden" name="mitra_id" value="{{ $mitraFilter ?? '' }}">
                        
                    @if(isset($search) && !empty($search))
                        <div class="flex h-full items-center px-2">
                            <a href="{{ route('category-products.index', ['mitra_id' => $mitraFilter ?? '']) }}" class="text-slate-400 hover:text-danger-500">
                                <i class="h-4" data-feather="x-circle"></i>
                            </a>
                        </div>
                    @endif
                </form>
                <!-- Category Product Search Ends -->
                
                <!-- Mitra Filter Starts -->
                <div class="w-full md:w-64">
                    <select id="mitra-filter" class="select w-full" onchange="applyMitraFilter(this.value)">
                        <option value="">All Mitras</option>
                        @foreach($mitras as $mitra)
                            <option value="{{ $mitra->id }}" {{ ($mitraFilter ?? '') == $mitra->id ? 'selected' : '' }}>
                                {{ $mitra->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Mitra Filter Ends -->
            </div>

            <!-- Category Product Action Starts -->
            <div class="flex w-full items-center justify-between gap-x-4 md:w-auto">
                <a class="btn btn-primary" href="{{ route('category-products.create') }}" role="button">
                    <i data-feather="plus" height="1rem" width="1rem"></i>
                    <span class="hidden sm:inline-block">Add Category Product</span>
                </a>
            </div>
            <!-- Category Product Action Ends -->
        </div>
        <!-- Category Product Header Ends -->

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        
        @if(isset($search) && !empty($search))
        <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-2">
            <span>Search results for: <span class="font-medium text-primary-500">{{ $search }}</span></span>
            <a href="{{ route('category-products.index', ['mitra_id' => $mitraFilter ?? '']) }}" class="ml-2 text-xs text-danger-500 hover:underline">
                Clear search
            </a>
        </div>
        @endif
        
        @if(isset($mitraFilter) && !empty($mitraFilter))
        <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-2">
            <span>Filtered by mitra: <span class="font-medium text-primary-500">
                {{ $mitras->firstWhere('id', $mitraFilter)->name ?? 'Unknown' }}</span>
            </span>
            <a href="{{ route('category-products.index', ['search' => $search ?? '']) }}" class="ml-2 text-xs text-danger-500 hover:underline">
                Clear filter
            </a>
        </div>
        @endif

        <!-- Category Product Table Starts -->
        <div class="table-responsive whitespace-nowrap rounded-primary">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-[20%] uppercase">Name</th>
                        <th class="w-[15%] uppercase">Mitra</th>
                        <th class="w-[25%] uppercase">
                            <div class="flex items-center">
                                <i class="fas fa-ship mr-1 text-blue-500"></i> SEA Pricing
                            </div>
                        </th>
                        <th class="w-[25%] uppercase">
                            <div class="flex items-center">
                                <i class="fas fa-plane mr-1 text-amber-500"></i> AIR Pricing
                            </div>
                        </th>
                        <th class="w-[15%] !text-right uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryProducts as $categoryProduct)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-primary bg-primary-500/10 text-primary-500">
                                    <i class="h-5 w-5" data-feather="box"></i>
                                </div>
                                <div>
                                    <h6
                                        class="whitespace-nowrap text-sm font-medium text-slate-700 dark:text-slate-100">
                                        {{ $categoryProduct->name }}
                                    </h6>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">ID #{{ $categoryProduct->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($categoryProduct->mitra)
                                <span class="badge badge-soft-primary">
                                    {{ $categoryProduct->mitra->name }}
                                </span>
                            @else
                                <span class="badge badge-soft-secondary">
                                    Not Assigned
                                </span>
                            @endif
                        </td>
                        <!-- SEA Pricing -->
                        <td>
                            <div class="flex flex-col space-y-1 border-l-2 border-blue-400 pl-2">
                                <div class="flex justify-between">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        <i class="fas fa-box mr-1 text-blue-500"></i> CBM:
                                    </span>
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-medium">
                                            Mitra: Rp {{ number_format($categoryProduct->mit_price_cbm_sea ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs font-medium">
                                            Cust: Rp {{ number_format($categoryProduct->cust_price_cbm_sea ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        <i class="fas fa-weight mr-1 text-blue-500"></i> KG:
                                    </span>
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-medium">
                                            Mitra: Rp {{ number_format($categoryProduct->mit_price_kg_sea ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs font-medium">
                                            Cust: Rp {{ number_format($categoryProduct->cust_price_kg_sea ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <!-- AIR Pricing -->
                        <td>
                            <div class="flex flex-col space-y-1 border-l-2 border-amber-400 pl-2">
                                <div class="flex justify-between">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        <i class="fas fa-box mr-1 text-amber-500"></i> CBM:
                                    </span>
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-medium">
                                            Mitra: Rp {{ number_format($categoryProduct->mit_price_cbm_air ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs font-medium">
                                            Cust: Rp {{ number_format($categoryProduct->cust_price_cbm_air ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        <i class="fas fa-weight mr-1 text-amber-500"></i> KG:
                                    </span>
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-medium">
                                            Mitra: Rp {{ number_format($categoryProduct->mit_price_kg_air ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs font-medium">
                                            Cust: Rp {{ number_format($categoryProduct->cust_price_kg_air ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex justify-end">
                                <div class="dropdown" data-placement="bottom-start">
                                    <div class="dropdown-toggle">
                                        <i class="w-6 text-slate-400" data-feather="more-horizontal"></i>
                                    </div>
                                    <div class="dropdown-content w-40">
                                        <ul class="dropdown-list">
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('category-products.show', $categoryProduct->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="external-link"></i>
                                                    <span>Details</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('category-products.edit', $categoryProduct->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <form action="{{ route('category-products.destroy', $categoryProduct->id) }}" method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-link w-full text-left" onclick="return confirm('Are you sure you want to delete this category product?')">
                                                        <i class="h-5 text-slate-400" data-feather="trash"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            @if(isset($search) && !empty($search))
                                No category products found matching "{{ $search }}"
                            @elseif(isset($mitraFilter) && !empty($mitraFilter))
                                No category products found for this mitra
                            @else
                                No category products found
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Category Product Table Ends -->

        <!-- Category Product Pagination Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row">
            <p class="text-xs font-normal text-slate-400">
                Showing {{ $categoryProducts->firstItem() ?? 0 }} to {{ $categoryProducts->lastItem() ?? 0 }} of {{ $categoryProducts->total() ?? 0 }} results
            </p>
            
            <!-- Pagination -->
            {{ $categoryProducts->links('vendor.pagination.tailwind') }}
        </div>
        <!-- Category Product Pagination Ends -->
    </div>
    <!-- Category Product List Ends -->
    
    @push('scripts')
    <script>
        function applyMitraFilter(mitraId) {
            const currentUrl = new URL(window.location.href);
            const searchParams = currentUrl.searchParams;
            
            if (mitraId) {
                searchParams.set('mitra_id', mitraId);
            } else {
                searchParams.delete('mitra_id');
            }
            
            // Preserve search parameter if it exists
            if (searchParams.has('search') && !searchParams.get('search')) {
                searchParams.delete('search');
            }
            
            window.location.href = currentUrl.toString();
        }
    </script>
    @endpush
</x-app-layout>