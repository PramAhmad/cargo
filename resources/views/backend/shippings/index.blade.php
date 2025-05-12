<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Shipping List" page="Shipping Management" />
    <!-- Page Title Ends -->

    <!-- Shipping List Starts -->
    <div class="space-y-4">
        <!-- Shipping Header Starts -->
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row md:gap-y-0">
            <div class="flex w-full flex-col md:flex-row gap-2 md:gap-3 md:w-auto">
                <!-- Shipping Search Starts -->
                <form
                    action="{{ route('shippings.index') }}"
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
                        placeholder="Search by invoice, customer, marking" />
                    @if(isset($search) && !empty($search))
                        <div class="flex h-full items-center px-2">
                            <a href="{{ route('shippings.index', ['status' => $statusFilter ?? '', 'date_from' => $dateFrom ?? '', 'date_to' => $dateTo ?? '']) }}" class="text-slate-400 hover:text-danger-500">
                                <i class="h-4" data-feather="x-circle"></i>
                            </a>
                        </div>
                    @endif
                </form>
                <!-- Shipping Search Ends -->

                <!-- Shipping Status Filter Starts -->
                <div class="flex w-full items-center gap-2 md:w-auto">
                    <select name="status" id="status-filter" class="h-10 rounded-primary border border-transparent bg-white shadow-sm text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-transparent dark:bg-slate-800 dark:focus:border-primary-500 px-3" onchange="applyFilters()">
                        <option value="">All Status</option>
                        @foreach(\App\Enums\ShippingStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ $statusFilter == $status->value ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                    
                    @if($statusFilter)
                        <a href="{{ route('shippings.index', ['search' => $search ?? '', 'date_from' => $dateFrom ?? '', 'date_to' => $dateTo ?? '']) }}" class="text-xs text-danger-500 hover:underline flex items-center">
                            <i class="h-3.5 mr-1" data-feather="x"></i>
                            Clear
                        </a>
                    @endif
                </div>
                <!-- Shipping Status Filter Ends -->
            </div>

            <!-- Date Filter Starts -->
            <div class="flex w-full items-center gap-2 md:w-auto">
                <div class="relative">
                    <input 
                        id="date_range" 
                        type="text" 
                        class="input input-date-range h-10 w-full md:w-64 rounded-primary border border-transparent bg-white shadow-sm text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-transparent dark:bg-slate-800 dark:focus:border-primary-500 px-3"
                        placeholder="Select date range"
                        value="{{ ($dateFrom && $dateTo) ? date('Y-m-d', strtotime($dateFrom)) . ' to ' . date('Y-m-d', strtotime($dateTo)) : '' }}"
                        readonly
                    >
                    <input type="hidden" id="date_from" name="date_from" value="{{ $dateFrom ?? '' }}">
                    <input type="hidden" id="date_to" name="date_to" value="{{ $dateTo ?? '' }}">
                    
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="h-4 w-4" data-feather="calendar"></i>
                    </div>
                </div>
                
                <div class="flex space-x-1">
                    <button type="button" class="date-preset btn btn-xs btn-secondary" data-range="today">Today</button>
                    <button type="button" class="date-preset btn btn-xs btn-secondary" data-range="week">This Week</button>
                    <button type="button" class="date-preset btn btn-xs btn-secondary" data-range="month">This Month</button>
                </div>
                
                <button type="button" id="apply-date-filter" class="btn btn-xs btn-primary">
                    <i class="h-3.5 mr-1" data-feather="filter"></i>
                    Filter
                </button>

                @if($dateFrom || $dateTo)
                    <a href="{{ route('shippings.index', ['search' => $search ?? '', 'status' => $statusFilter ?? '']) }}" class="text-xs text-danger-500 hover:underline flex items-center">
                        <i class="h-3.5 mr-1" data-feather="x"></i>
                        Clear
                    </a>
                @endif
            </div>
            <!-- Date Filter Ends -->

            <!-- Shipping Action Starts -->
           
            <!-- Shipping Action Ends -->
        </div>
        <!-- Shipping Header Ends -->

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
        
        @if(isset($search) && !empty($search) || $statusFilter || $dateFrom || $dateTo)
        <div class="flex items-center flex-wrap text-sm text-slate-500 dark:text-slate-400 mb-2">
            @if(isset($search) && !empty($search))
            <div class="mr-4 mb-2">
                <span>Search: <span class="font-medium text-primary-500">{{ $search }}</span></span>
            </div>
            @endif
            
            @if($statusFilter)
            <div class="mr-4 mb-2">
                <span>Status: <span class="font-medium text-primary-500">{{ \App\Enums\ShippingStatus::tryFrom($statusFilter)->label() }}</span></span>
            </div>
            @endif
            
            @if($dateFrom || $dateTo)
            <div class="mr-4 mb-2">
                <span>Date: 
                    <span class="font-medium text-primary-500">
                        {{ $dateFrom ? date('M d, Y', strtotime($dateFrom)) : 'Any' }} - 
                        {{ $dateTo ? date('M d, Y', strtotime($dateTo)) : 'Now' }}
                    </span>
                </span>
            </div>
            @endif
            
            <a href="{{ route('shippings.index') }}" class="text-xs text-danger-500 hover:underline mb-2">
                Clear All
            </a>
        </div>
        @endif

        <!-- Shipping Table Starts -->
        <div class="table-responsive whitespace-nowrap rounded-primary">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-[15%] uppercase">Invoice No</th>
                        <th class="w-[20%] uppercase">Customer</th>
                        <th class="w-[15%] uppercase">Documents</th>
                        <th class="w-[25%] uppercase">Marking</th>
                        <th class="w-[10%] uppercase">Transaction Date</th>
                        <th class="w-[10%] uppercase">Grand Total</th>
                        <th class="w-[10%] uppercase">Status</th>
                        <th class="w-[5%] !text-right uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shippings as $shipping)
                    <tr>
                        <td>{{ $shipping->invoice }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-primary bg-primary-500/10 text-primary-500">
                                    <i class="h-5 w-5" data-feather="user"></i>
                                </div>
                                <div>
                                    <h6 class="whitespace-nowrap text-sm font-medium text-slate-700 dark:text-slate-100">
                                        {{ $shipping->customer->name }}
                                    </h6>
                                    @if($shipping->customer->address)
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ $shipping->customer->address }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('shippings.surat-jalan', $shipping->id) }}" 
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 hover:bg-blue-200 text-blue-600 transition-colors"
                                   title="Surat Jalan"
                                   target="_blank">
                                    <i class="h-4 w-4" data-feather="file-text"></i>
                                </a>
                                
                                <a href="{{ route('shippings.faktur', $shipping->id) }}" 
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 hover:bg-green-200 text-green-600 transition-colors"
                                   title="Faktur"
                                   target="_blank">
                                    <i class="h-4 w-4" data-feather="file"></i>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="text-wrap max-w-[150px]">
                                <span class="text-sm">{{ $shipping->marking ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm">{{ $shipping->transaction_date ? $shipping->transaction_date->format('d M Y') : '-' }}</span>
                        </td>
                        <td>
                            <span class="text-sm font-medium">{{ number_format($shipping->grand_total, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $shipping->status->getColor() }}">
                                {{ $shipping->status->getLabel() }}
                            </span>
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
                                                <a href="{{ route('shippings.show', $shipping->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="external-link"></i>
                                                    <span>Details</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('shippings.edit', $shipping->id) }}" class="dropdown-link">
                                                    <i class="h-5 text-slate-400" data-feather="edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <form action="{{ route('shippings.destroy', $shipping->id) }}" method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-link w-full text-left" onclick="return confirm('Are you sure you want to delete this shipping data?')">
                                                        <i class="h-5 text-slate-400" data-feather="trash"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            </li>
                                            <li class="dropdown-list-item">
                                                <a href="{{ route('shippings.invoice', $shipping->id) }}" class="dropdown-link" target="_blank">
                                                    <i class="h-5 text-slate-400" data-feather="file-text"></i>
                                                    <span>Print Invoice</span>
                                                </a>
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
                            @if(isset($search) && !empty($search) || $statusFilter || $dateFrom || $dateTo)
                                No shipping data found matching your criteria.
                            @else
                                No shipping data available.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Shipping Table Ends -->

        <!-- Pagination Starts -->
        @if($shippings->hasPages())
        <div class="flex flex-col items-center justify-between gap-y-4 md:flex-row">
            <p class="text-xs font-normal text-slate-400">
                Showing {{ $shippings->firstItem() ?? 0 }} to {{ $shippings->lastItem() ?? 0 }} of {{ $shippings->total() ?? 0 }} results
            </p>
            
            {{ $shippings->links() }}
        </div>
        @endif
        <!-- Pagination Ends -->
    </div>
    <!-- Shipping List Ends -->

    <script>
        function applyFilters() {
            const statusFilter = document.getElementById('status-filter').value;
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            const searchParam = '{{ $search ?? "" }}';
            
            let url = '{{ route('shippings.index') }}?';
            
            if (searchParam) {
                url += `search=${searchParam}&`;
            }
            
            if (statusFilter) {
                url += `status=${statusFilter}&`;
            }
            
            if (dateFrom) {
                url += `date_from=${dateFrom}&`;
            }
            
            if (dateTo) {
                url += `date_to=${dateTo}`;
            }
            
            window.location.href = url;
        }
    </script>
</x-app-layout>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<style>
    .daterangepicker {
        font-family: inherit;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .daterangepicker .ranges li.active {
        background-color: #3b82f6;
    }
    .daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #3b82f6;
    }
    .daterangepicker .drp-buttons .btn {
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    .daterangepicker .drp-buttons .applyBtn {
        background-color: #3b82f6;
        color: white;
    }
    .daterangepicker .drp-buttons .cancelBtn {
        background-color: #f1f5f9;
        color: #334155;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize the daterangepicker
        $('#date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear',
                applyLabel: 'Apply',
                separator: ' to '
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            $('#date_from').val(picker.startDate.format('YYYY-MM-DD'));
            $('#date_to').val(picker.endDate.format('YYYY-MM-DD'));
            applyFilters();
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#date_from').val('');
            $('#date_to').val('');
            applyFilters();
        });

        // Preset date buttons
        $('.date-preset').on('click', function() {
            const range = $(this).data('range');
            let startDate, endDate;
            
            switch(range) {
                case 'today':
                    startDate = moment();
                    endDate = moment();
                    break;
                case 'week':
                    startDate = moment().startOf('week');
                    endDate = moment().endOf('week');
                    break;
                case 'month':
                    startDate = moment().startOf('month');
                    endDate = moment().endOf('month');
                    break;
            }
            
            $('#date_range').val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
            $('#date_from').val(startDate.format('YYYY-MM-DD'));
            $('#date_to').val(endDate.format('YYYY-MM-DD'));
            applyFilters();
        });
    });

    // Update the existing applyFilters function
    function applyFilters() {
        const statusFilter = document.getElementById('status-filter').value;
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;
        const searchParam = '{{ $search ?? "" }}';
        
        let url = '{{ route('shippings.index') }}?';
        
        if (searchParam) {
            url += `search=${searchParam}&`;
        }
        
        if (statusFilter) {
            url += `status=${statusFilter}&`;
        }
        
        if (dateFrom) {
            url += `date_from=${dateFrom}&`;
        }
        
        if (dateTo) {
            url += `date_to=${dateTo}`;
        }
        
        window.location.href = url;
    }
</script>
@endpush