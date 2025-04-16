<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Mitra List" page="Mitra" />
    <!-- Page Title Ends -->

    <!-- Mitra List Starts -->
    <div class="space-y-4">
        <!-- Mitra Header Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row md:gap-y-0">
            <div class="flex w-full flex-col md:flex-row gap-2 md:gap-3 md:w-auto">
                <!-- Mitra Search Starts -->
                <form
                    action="{{ route('mitras.index') }}"
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
                            <a href="{{ route('mitras.index', ['group_id' => $groupFilter]) }}" class="text-slate-400 hover:text-danger-500">
                                <i class="h-4" data-feather="x-circle"></i>
                            </a>
                        </div>
                    @endif
                </form>
                <!-- Mitra Search Ends -->

                <!-- Mitra Group Filter Starts -->
                <div class="flex w-full items-center gap-2 md:w-auto">
                    <select name="group_id" id="group-filter" class="h-10 rounded-primary border border-transparent bg-white shadow-sm text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-transparent dark:bg-slate-800 dark:focus:border-primary-500 px-3" onchange="applyFilters()">
                        <option value="">All Groups</option>
                        @foreach($mitraGroups as $group)
                            <option value="{{ $group->id }}" {{ $groupFilter == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    @if($groupFilter)
                        <a href="{{ route('mitras.index', ['search' => $search]) }}" class="text-xs text-danger-500 hover:underline flex items-center">
                            <i class="h-3.5 mr-1" data-feather="x"></i>
                            Clear
                        </a>
                    @endif
                </div>
                <!-- Mitra Group Filter Ends -->
            </div>

            <!-- Mitra Action Starts -->
            <div class="flex w-full items-center justify-between gap-x-4 md:w-auto">
                <div class="flex items-center gap-x-4">
                    <button class="btn bg-white font-medium shadow-sm dark:bg-slate-800">
                        <i class="h-4" data-feather="upload"></i>
                        <span class="hidden sm:inline-block">Export</span>
                    </button>
                </div>

                <a class="btn btn-primary" href="{{ route('mitras.create') }}" role="button">
                    <i data-feather="plus" height="1rem" width="1rem"></i>
                    <span class="hidden sm:inline-block">Add Mitra</span>
                </a>
            </div>
            <!-- Mitra Action Ends -->
        </div>
        <!-- Mitra Header Ends -->

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        
        @if(isset($search) && !empty($search) || $groupFilter)
        <div class="flex items-center flex-wrap text-sm text-slate-500 dark:text-slate-400 mb-2">
            @if(isset($search) && !empty($search))
            <div class="mr-4 mb-2">
                <span>Search: <span class="font-medium text-primary-500">{{ $search }}</span></span>
            </div>
            @endif
            
            @if($groupFilter)
            <div class="mr-4 mb-2">
                <span>Group: <span class="font-medium text-primary-500">{{ $mitraGroups->firstWhere('id', $groupFilter)->name ?? '' }}</span></span>
            </div>
            @endif
            
            <a href="{{ route('mitras.index') }}" class="text-xs text-danger-500 hover:underline mb-2">
                Clear All
            </a>
        </div>
        @endif

        <!-- Mitra Table Starts -->
        <div class="table-responsive whitespace-nowrap rounded-primary">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-[5%] uppercase">Code</th>
                        <th class="w-[20%] uppercase">Name</th>
                        <th class="w-[15%] uppercase">Contact</th>
                        <th class="w-[15%] uppercase">Group</th>
                        <th class="w-[15%] uppercase">Financial</th>
                        <th class="w-[10%] uppercase">Status</th>
                        <th class="w-[10%] !text-right uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mitras as $mitra)
                    <tr>
                        <td>{{ $mitra->code }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-primary bg-primary-500/10 text-primary-500">
                                    <i class="h-5 w-5" data-feather="briefcase"></i>
                                </div>
                                <div>
                                    <h6 class="whitespace-nowrap text-sm font-medium text-slate-700 dark:text-slate-100">
                                        {{ $mitra->name }}
                                    </h6>
                                    @if($mitra->address_office_indo)
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ $mitra->address_office_indo }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-sm">{{ $mitra->phone1 }}</span>
                                @if($mitra->email)
                                <span class="text-xs text-slate-500">{{ $mitra->email }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($mitra->mitraGroup)
                            <span class="badge badge-soft-info">
                                {{ $mitra->mitraGroup->name }}
                            </span>
                            @else
                            <span class="text-sm text-slate-500">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-col">
                                @if($mitra->bank)
                                <span class="text-sm">{{ $mitra->bank->name }}</span>
                                @endif
                                <span class="text-xs text-slate-500">Terms: {{ $mitra->syarat_bayar }} days</span>
                            </div>
                        </td>
                        <td>
                            @if($mitra->status)
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
                                                <a href="{{ route('mitras.show', $mitra->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="external-link"></i>
                                                    <span>Details</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('mitras.edit', $mitra->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <form action="{{ route('mitras.destroy', $mitra->id) }}" method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-link w-full text-left" onclick="return confirm('Are you sure you want to delete this mitra?')">
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
                        <td colspan="7" class="text-center py-4">
                            @if(isset($search) && !empty($search) || $groupFilter)
                                No mitra data found matching your criteria.
                            @else
                                No mitra data available.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Mitra Table Ends -->

        <!-- Pagination Starts -->
        @if($mitras->hasPages())
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row">
            <p class="text-xs font-normal text-slate-400">
                Showing {{ $mitras->firstItem() ?? 0 }} to {{ $mitras->lastItem() ?? 0 }} of {{ $mitras->total() ?? 0 }} results
            </p>
            
            {{ $mitras->links() }}
        </div>
        @endif
        <!-- Pagination Ends -->
    </div>
    <!-- Mitra List Ends -->

    <script>
        function applyFilters() {
            const groupFilter = document.getElementById('group-filter').value;
            const searchParam = '{{ $search ?? "" }}';
            
            let url = '{{ route('mitras.index') }}?';
            
            if (searchParam) {
                url += `search=${searchParam}&`;
            }
            
            if (groupFilter) {
                url += `group_id=${groupFilter}`;
            }
            
            window.location.href = url;
        }
    </script>
</x-app-layout>