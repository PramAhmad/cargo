<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Category Customer List" page="Customers" />
    <!-- Page Title Ends -->

    <!-- Category Customer List Starts -->
    <div class="space-y-4">
        <!-- Category Customer Header Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row md:gap-y-0">
            <!-- Category Customer Search Starts -->
            <form
                action="{{ route('category-customers.index') }}"
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
                    placeholder="Search category customer" />
                @if(isset($search) && !empty($search))
                    <div class="flex h-full items-center px-2">
                        <a href="{{ route('category-customers.index') }}" class="text-slate-400 hover:text-danger-500">
                            <i class="h-4" data-feather="x-circle"></i>
                        </a>
                    </div>
                @endif
            </form>
            <!-- Category Customer Search Ends -->

            <!-- Category Customer Action Starts -->
            <div class="flex w-full items-center justify-between gap-x-4 md:w-auto">
               
                <a class="btn btn-primary" href="{{ route('category-customers.create') }}" role="button">
                    <i data-feather="plus" height="1rem" width="1rem"></i>
                    <span class="hidden sm:inline-block">Add Category Customer</span>
                </a>
            </div>
            <!-- Category Customer Action Ends -->
        </div>
        <!-- Category Customer Header Ends -->

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        
        @if(isset($search) && !empty($search))
        <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-2">
            <span>Search results for: <span class="font-medium text-primary-500">{{ $search }}</span></span>
            <a href="{{ route('category-customers.index') }}" class="ml-2 text-xs text-danger-500 hover:underline">
                Clear search
            </a>
        </div>
        @endif

        <!-- Category Customer Table Starts -->
        <div class="table-responsive whitespace-nowrap rounded-primary">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-[40%] uppercase">Name</th>
                        <th class="w-[25%] uppercase">Created At</th>
                        <th class="w-[25%] uppercase">Updated At</th>
                        <th class="w-[5%] !text-right uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryCustomers as $categoryCustomer)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-primary bg-primary-500/10 text-primary-500">
                                    <i class="h-5 w-5" data-feather="users"></i>
                                </div>
                                <div>
                                    <h6
                                        class="whitespace-nowrap text-sm font-medium text-slate-700 dark:text-slate-100">
                                        {{ $categoryCustomer->name }}
                                    </h6>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">ID #{{ $categoryCustomer->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>{{ $categoryCustomer->created_at->format('d M Y') }}</td>
                        <td>{{ $categoryCustomer->updated_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex justify-end">
                                <div class="dropdown" data-placement="bottom-start">
                                    <div class="dropdown-toggle">
                                        <i class="w-6 text-slate-400" data-feather="more-horizontal"></i>
                                    </div>
                                    <div class="dropdown-content w-40">
                                        <ul class="dropdown-list">
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('category-customers.show', $categoryCustomer->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="external-link"></i>
                                                    <span>Details</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('category-customers.edit', $categoryCustomer->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <form action="{{ route('category-customers.destroy', $categoryCustomer->id) }}" method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-link w-full text-left" onclick="return confirm('Are you sure you want to delete this category customer?')">
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
                        <td colspan="4" class="text-center py-4">
                            @if(isset($search) && !empty($search))
                                No category customers found matching "{{ $search }}"
                            @else
                                No category customers found
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Category Customer Table Ends -->

        <!-- Category Customer Pagination Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row">
            <p class="text-xs font-normal text-slate-400">
                Showing {{ $categoryCustomers->firstItem() ?? 0 }} to {{ $categoryCustomers->lastItem() ?? 0 }} of {{ $categoryCustomers->total() ?? 0 }} results
            </p>
            
            <!-- Pagination -->
            {{ $categoryCustomers->links('vendor.pagination.tailwind') }}
        </div>
        <!-- Category Customer Pagination Ends -->
    </div>
    <!-- Category Customer List Ends -->
</x-app-layout>