<x-app-layout>
    <!-- Page Title Starts -->
    
    <x-page-title header="Mitra Group List" page="Partners" />

    <!-- Page Title Ends -->

    <!-- Mitra Group List Starts -->
    <div class="space-y-4">
        <!-- Mitra Group Header Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row md:gap-y-0">
            <!-- Mitra Group Search Starts -->
            <form
                action="{{ route('mitra-groups.index') }}"
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
                    placeholder="Search mitra group" />
                @if(isset($search) && !empty($search))
                    <div class="flex h-full items-center px-2">
                        <a href="{{ route('mitra-groups.index') }}" class="text-slate-400 hover:text-danger-500">
                            <i class="h-4" data-feather="x-circle"></i>
                        </a>
                    </div>
                @endif
            </form>
            <!-- Mitra Group Search Ends -->

            <!-- Mitra Group Action Starts -->
            <div class="flex w-full items-center justify-between gap-x-4 md:w-auto">
                <div class="flex items-center gap-x-4">
                    <button class="btn bg-white font-medium shadow-sm dark:bg-slate-800">
                        <i class="h-4" data-feather="upload"></i>
                        <span class="hidden sm:inline-block">Export</span>
                    </button>
                </div>

                <a class="btn btn-primary" href="{{ route('mitra-groups.create') }}" role="button">
                    <i data-feather="plus" height="1rem" width="1rem"></i>
                    <span class="hidden sm:inline-block">Add Mitra Group</span>
                </a>
            </div>
            <!-- Mitra Group Action Ends -->
        </div>
        <!-- Mitra Group Header Ends -->

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        
        @if(isset($search) && !empty($search))
        <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-2">
            <span>Search results for: <span class="font-medium text-primary-500">{{ $search }}</span></span>
            <a href="{{ route('mitra-groups.index') }}" class="ml-2 text-xs text-danger-500 hover:underline">
                Clear search
            </a>
        </div>
        @endif

        <!-- Mitra Group Table Starts -->
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
                    @forelse($mitraGroups as $mitraGroup)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-primary bg-primary-500/10 text-primary-500">
                                    <i class="h-5 w-5" data-feather="briefcase"></i>
                                </div>
                                <div>
                                    <h6
                                        class="whitespace-nowrap text-sm font-medium text-slate-700 dark:text-slate-100">
                                        {{ $mitraGroup->name }}
                                    </h6>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">ID #{{ $mitraGroup->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>{{ $mitraGroup->created_at->format('d M Y') }}</td>
                        <td>{{ $mitraGroup->updated_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex justify-end">
                                <div class="dropdown" data-placement="bottom-start">
                                    <div class="dropdown-toggle">
                                        <i class="w-6 text-slate-400" data-feather="more-horizontal"></i>
                                    </div>
                                    <div class="dropdown-content w-40">
                                        <ul class="dropdown-list">
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('mitra-groups.show', $mitraGroup->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="external-link"></i>
                                                    <span>Details</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('mitra-groups.edit', $mitraGroup->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <form action="{{ route('mitra-groups.destroy', $mitraGroup->id) }}" method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-link w-full text-left" onclick="return confirm('Are you sure you want to delete this mitra group?')">
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
                                No mitra groups found matching "{{ $search }}"
                            @else
                                No mitra groups found
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Mitra Group Table Ends -->

        <!-- Mitra Group Pagination Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row">
            <p class="text-xs font-normal text-slate-400">
                Showing {{ $mitraGroups->firstItem() ?? 0 }} to {{ $mitraGroups->lastItem() ?? 0 }} of {{ $mitraGroups->total() ?? 0 }} results
            </p>
            
            <!-- Pagination -->
            {{ $mitraGroups->links('vendor.pagination.tailwind') }}
        </div>
        <!-- Mitra Group Pagination Ends -->
    </div>
    <!-- Mitra Group List Ends -->
</x-app-layout>