<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Customer List" page="Customer" />
    <!-- Page Title Ends -->

    <!-- Customer List Starts -->
    <div class="space-y-4">
        <!-- Customer Header Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row md:gap-y-0">
            <div class="flex w-full flex-col md:flex-row gap-2 md:gap-3 md:w-auto">
                <!-- Customer Search Starts -->
                <form
                    action="{{ route('customers.index') }}"
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
                        placeholder="Search by name, code, email" />
                    @if(isset($search) && !empty($search))
                        <div class="flex h-full items-center px-2">
                            <a href="{{ route('customers.index', ['type' => $typeFilter, 'marketing_id' => $marketingFilter]) }}" class="text-slate-400 hover:text-danger-500">
                                <i class="h-4" data-feather="x-circle"></i>
                            </a>
                        </div>
                    @endif
                </form>
                <!-- Customer Search Ends -->

                <!-- Customer Filters Start -->
                <div class="flex w-full items-center gap-2 md:w-auto">
                    <select name="type" id="type-filter" class="h-10 rounded-primary border border-transparent bg-white shadow-sm text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-transparent dark:bg-slate-800 dark:focus:border-primary-500 px-3" onchange="applyFilters()">
                        <option value="">All Types</option>
                        <option value="individual" {{ $typeFilter == 'individual' ? 'selected' : '' }}>Individual</option>
                        <option value="company" {{ $typeFilter == 'company' ? 'selected' : '' }}>Company</option>
                        <option value="internal" {{ $typeFilter == 'internal' ? 'selected' : '' }}>Internal</option>
                    </select>
                    
                    <select name="marketing_id" id="marketing-filter" class="h-10 rounded-primary border border-transparent bg-white shadow-sm text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-transparent dark:bg-slate-800 dark:focus:border-primary-500 px-3" onchange="applyFilters()">
                        <option value="">All Marketing</option>
                        @foreach($marketings as $marketing)
                            <option value="{{ $marketing->id }}" {{ $marketingFilter == $marketing->id ? 'selected' : '' }}>
                                {{ $marketing->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    @if($typeFilter || $marketingFilter)
                        <a href="{{ route('customers.index', ['search' => $search]) }}" class="text-xs text-danger-500 hover:underline flex items-center">
                            <i class="h-3.5 mr-1" data-feather="x"></i>
                            Clear
                        </a>
                    @endif
                </div>
                <!-- Customer Filters End -->
            </div>

            <!-- Customer Action Starts -->
            <div class="flex w-full items-center justify-between gap-x-4 md:w-auto">
                <div class="flex items-center gap-x-4">
                    <button class="btn bg-white font-medium shadow-sm dark:bg-slate-800">
                        <i class="h-4" data-feather="upload"></i>
                        <span class="hidden sm:inline-block">Export</span>
                    </button>
                </div>

                <a class="btn btn-primary" href="{{ route('customers.create') }}" role="button">
                    <i data-feather="plus" height="1rem" width="1rem"></i>
                    <span class="hidden sm:inline-block">Add Customer</span>
                </a>
            </div>
            <!-- Customer Action Ends -->
        </div>
        <!-- Customer Header Ends -->

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        
        @if(isset($search) && !empty($search) || $typeFilter || $marketingFilter)
        <div class="flex items-center flex-wrap text-sm text-slate-500 dark:text-slate-400 mb-2">
            @if(isset($search) && !empty($search))
            <div class="mr-4 mb-2">
                <span>Search: <span class="font-medium text-primary-500">{{ $search }}</span></span>
            </div>
            @endif
            
            @if($typeFilter)
            <div class="mr-4 mb-2">
                <span>Type: <span class="font-medium text-primary-500">{{ ucfirst($typeFilter) }}</span></span>
            </div>
            @endif
            
            @if($marketingFilter)
            <div class="mr-4 mb-2">
                <span>Marketing: <span class="font-medium text-primary-500">{{ $marketings->firstWhere('id', $marketingFilter)->name ?? '' }}</span></span>
            </div>
            @endif
            
            <a href="{{ route('customers.index') }}" class="text-xs text-danger-500 hover:underline mb-2">
                Clear All
            </a>
        </div>
        @endif

        <!-- Customer Table Starts -->
        <div class="table-responsive whitespace-nowrap rounded-primary">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-[5%] uppercase">Code</th>
                        <th class="w-[20%] uppercase">Name</th>
                        <th class="w-[15%] uppercase">Contact</th>
                        <th class="w-[10%] uppercase">Type</th>
                        <th class="w-[15%] uppercase">Group / Category</th>
                        <th class="w-[15%] uppercase">Marketing</th>
                        <th class="w-[10%] uppercase">Status</th>
                        <th class="w-[10%] !text-right uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->code }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-primary {{ $customer->type == 'company' ? 'bg-indigo-500/10 text-indigo-500' : ($customer->type == 'internal' ? 'bg-amber-500/10 text-amber-500' : 'bg-primary-500/10 text-primary-500') }}">
                                    <i class="h-5 w-5" data-feather="{{ $customer->type == 'company' ? 'briefcase' : ($customer->type == 'internal' ? 'home' : 'user') }}"></i>
                                </div>
                                <div>
                                    <h6 class="whitespace-nowrap text-sm font-medium text-slate-700 dark:text-slate-100">
                                        {{ $customer->name }}
                                    </h6>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ $customer->city }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-sm">{{ $customer->phone1 }}</span>
                                @if($customer->email)
                                <span class="text-xs text-slate-500">{{ $customer->email }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $customer->type == 'company' ? 'badge-soft-success' : ($customer->type == 'internal' ? 'badge-soft-secondary' : 'badge-soft-primary') }}">
                                {{ ucfirst($customer->type) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-sm">{{ $customer->customerGroup->name ?? 'N/A' }}</span>
                                <span class="text-xs text-slate-500">{{ $customer->customerCategory->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            @if($customer->marketing)
                                <div class="flex items-center gap-2">
                                    <div class="h-7 w-7 flex items-center justify-center rounded-full bg-primary-100 text-primary-500 dark:bg-slate-700">
                                        <i class="h-3.5 w-3.5" data-feather="user"></i>
                                    </div>
                                    <span class="text-sm">{{ $customer->marketing->name }}</span>
                                </div>
                            @else
                                <span class="text-sm text-slate-500">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($customer->status)
                                <span class="badge badge-soft-success">Active</span>
                            @else
                                <span class="badge badge-soft-danger">Inactive</span>
                            @endif
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
                                                <a href="{{ route('customers.show', $customer->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="external-link"></i>
                                                    <span>Details</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('customers.edit', $customer->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-link w-full text-left" onclick="return confirm('Are you sure you want to delete this customer?')">
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
                        <td colspan="8" class="text-center py-4">
                            @if(isset($search) && !empty($search) || $typeFilter || $marketingFilter)
                                No customers found matching your criteria.
                            @else
                                No customer data available.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Customer Table Ends -->

        <!-- Pagination Starts -->
        @if($customers->hasPages())
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row">
            <p class="text-xs font-normal text-slate-400">
                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() ?? 0 }} results
            </p>
            
            {{ $customers->links() }}
        </div>
        @endif
        <!-- Pagination Ends -->
    </div>
    <!-- Customer List Ends -->

    <script>
        function applyFilters() {
            const typeFilter = document.getElementById('type-filter').value;
            const marketingFilter = document.getElementById('marketing-filter').value;
            const searchParam = '{{ $search ?? "" }}';
            
            let url = '{{ route('customers.index') }}?';
            
            if (searchParam) {
                url += `search=${encodeURIComponent(searchParam)}&`;
            }
            
            if (typeFilter) {
                url += `type=${encodeURIComponent(typeFilter)}&`;
            }
            
            if (marketingFilter) {
                url += `marketing_id=${encodeURIComponent(marketingFilter)}&`;
            }
            
            // Remove trailing & if present
            if (url.endsWith('&')) {
                url = url.slice(0, -1);
            }
            
            window.location.href = url;
        }
    </script>
</x-app-layout>