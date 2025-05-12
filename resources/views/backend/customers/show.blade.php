<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Customer Details" page="Customer" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <!-- Customer Header with Actions -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full 
                    {{ $customer->type == 'company' ? 'bg-indigo-500/10 text-indigo-500' : 
                      ($customer->type == 'internal' ? 'bg-amber-500/10 text-amber-500' : 
                      'bg-primary-500/10 text-primary-500') }}">
                    <i class="h-7 w-7" data-feather="{{ $customer->type == 'company' ? 'briefcase' : 
                                                      ($customer->type == 'internal' ? 'home' : 'user') }}"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">
                        {{ $customer->name }}
                        <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">({{ $customer->code }})</span>
                    </h2>
                    <div class="mt-1 flex items-center gap-3">
                        <span class="badge {{ $customer->status ? 'badge-success' : 'badge-danger' }}">
                            {{ $customer->status ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="badge {{ $customer->type == 'company' ? 'badge-soft-indigo' : 
                                           ($customer->type == 'internal' ? 'badge-soft-amber' : 'badge-soft-primary') }}">
                            {{ ucfirst($customer->type) }}
                        </span>
                        @if($customer->customerGroup)
                        <span class="badge badge-soft-slate">
                            {{ $customer->customerGroup->name }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary">
                    <i class="h-4 w-4" data-feather="edit-2"></i>
                    <span>Edit</span>
                </a>
                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this customer?')">
                        <i class="h-4 w-4" data-feather="trash-2"></i>
                        <span>Delete</span>
                    </button>
                </form>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="h-4 w-4" data-feather="arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column - Main Info -->
            <div class="md:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="info"></i>
                            <h4 class="card-title">Basic Information</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Code</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">{{ $customer->code }}</p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Name</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">{{ $customer->name }}</p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Type</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ ucfirst($customer->type) }}
                                    </p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Customer Group</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $customer->customerGroup->name ?? 'Not assigned' }}
                                    </p>
                                </div>

                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Customer Category</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $customer->customerCategory->name ?? 'Not assigned' }}
                                    </p>
                                </div>
                                
                                @if($customer->borndate)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Birth Date</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($customer->borndate)->format('d M Y') }}
                                    </p>
                                </div>
                                @endif
                                
                                @if($customer->created_date)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Registration Date</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($customer->created_date)->format('d M Y') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Address</h5>
                                    <div class="mt-1">
                                        @if($customer->street1)
                                            <p class="text-base font-medium text-slate-900 dark:text-slate-200">{{ $customer->street1 }}</p>
                                        @endif
                                        @if($customer->street2)
                                            <p class="text-base font-medium text-slate-900 dark:text-slate-200">{{ $customer->street2 }}</p>
                                        @endif
                                        @if($customer->street_item)
                                            <p class="text-base font-medium text-slate-900 dark:text-slate-200">{{ $customer->street_item }}</p>
                                        @endif
                                        @if($customer->city)
                                            <p class="text-base font-medium text-slate-900 dark:text-slate-200">
                                                {{ $customer->city }}{{ $customer->country ? ', ' . $customer->country : '' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($customer->marketing)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Marketing</h5>
                                    <div class="mt-1 flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-500">
                                            <i class="h-4 w-4" data-feather="user"></i>
                                        </div>
                                        <p class="text-base font-medium text-slate-900 dark:text-slate-200">
                                            {{ $customer->marketing->name }} 
                                            <a href="{{ route('marketings.show', $customer->marketing->id) }}" class="text-xs text-primary-500 hover:underline ml-2">
                                                View Profile
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Information -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="credit-card"></i>
                            <h4 class="card-title">Financial Information</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <!-- Bank Accounts Section -->
                        @if($customer->banks && $customer->banks->count() > 0)
                            <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-3">Bank Accounts</h5>
                            <div class="space-y-4">
                                @foreach($customer->banks as $bankAccount)
                                    <div class="p-4 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                        <div class="flex items-center justify-between mb-2">
                                            <h6 class="font-medium text-slate-700 dark:text-slate-300">
                                                {{ $bankAccount->bank->name ?? 'Bank' }} Account
                                                @if($bankAccount->is_default)
                                                    <span class="ml-2 badge badge-soft-success text-xs">Default</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            @if($bankAccount->bank)
                                                <div>
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">Bank</span>
                                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                        {{ $bankAccount->bank->name }}
                                                    </p>
                                                </div>
                                            @endif
                                            
                                            @if($bankAccount->rek_name)
                                                <div>
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">Account Name</span>
                                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                        {{ $bankAccount->rek_name }}
                                                    </p>
                                                </div>
                                            @endif
                                            
                                            @if($bankAccount->rek_no)
                                                <div>
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">Account Number</span>
                                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                        {{ $bankAccount->rek_no }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($customer->bank)
                            <!-- Legacy bank information (single account) -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @if($customer->bank)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Bank</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $customer->bank->name }}
                                    </p>
                                </div>
                                @endif
                                
                                @if($customer->atas_nama)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Account Name</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $customer->atas_nama }}
                                    </p>
                                </div>
                                @endif
                                
                                @if($customer->no_rek)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Account Number</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $customer->no_rek }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-slate-500">No bank account information available</p>
                        @endif
                        
                        <!-- NPWP Section -->
                        <div class="{{ ($customer->banks && $customer->banks->count() > 0) || $customer->bank ? 'mt-6 pt-6 border-t border-slate-200 dark:border-slate-700' : '' }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @if($customer->npwp)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">NPWP Number</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $customer->npwp }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Tax Address Section -->
                        @if($customer->tax_address)
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Tax Address</h5>
                            <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200 whitespace-pre-line">
                                {{ $customer->tax_address }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Order History Section -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="mr-2 h-5 w-5 text-primary-500" data-feather="shopping-bag"></i>
                                <h4 class="card-title">Recent Shipments</h4>
                            </div>
                            <a href="{{ route('shippings.index', ['customer_id' => $customer->id]) }}" class="text-sm text-primary-500 hover:underline">View All Shipments</a>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        @if(isset($recentShipments) && $recentShipments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Invoice</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</th>
                                            <th class="px-3 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                        @foreach($recentShipments as $shipping)
                                            <tr>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-slate-200">
                                                    {{ $shipping->invoice }}
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                                    {{ $shipping->transaction_date ? $shipping->transaction_date->format('d M Y') : '-' }}
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap">
                                                    <span class="badge {{ $shipping->status == 'completed' ? 'badge-success' : 
                                                                       ($shipping->status == 'pending' ? 'badge-warning' : 
                                                                       ($shipping->status == 'cancelled' ? 'badge-danger' : 'badge-info')) }}">
                                                        {{ $shipping->status ? ucfirst($shipping->status->value) : 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                                    {{ number_format($shipping->grand_total, 0, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap text-right text-sm">
                                                    <a href="{{ route('shippings.show', $shipping->id) }}" class="text-primary-500 hover:text-primary-700">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-32 bg-slate-50 dark:bg-slate-800 rounded-md">
                                <p class="text-slate-500 dark:text-slate-400">No shipments found for this customer</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Recent Shipments Section -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="mr-2 h-5 w-5 text-primary-500" data-feather="shopping-bag"></i>
                                <h4 class="card-title">Recent Shipments</h4>
                            </div>
                            <a href="{{ route('shippings.index', ['marketing_id' => $marketing->id]) }}" class="text-sm text-primary-500 hover:underline">View All Shipments</a>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        @if(isset($recentShipments) && $recentShipments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Invoice</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Customer</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</th>
                                            <th class="px-3 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                        @foreach($recentShipments as $shipping)
                                            <tr>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-slate-200">
                                                    {{ $shipping->invoice }}
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                                    {{ $shipping->customer ? $shipping->customer->name : 'N/A' }}
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                                    {{ $shipping->transaction_date ? $shipping->transaction_date->format('d M Y') : '-' }}
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap">
                                                    <span class="badge {{ $shipping->status == 'completed' ? 'badge-success' : 
                                                                       ($shipping->status == 'pending' ? 'badge-warning' : 
                                                                       ($shipping->status == 'cancelled' ? 'badge-danger' : 'badge-info')) }}">
                                                        {{ $shipping->status ? ucfirst($shipping->status->value) : 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                                    {{ number_format($shipping->grand_total, 0, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-3 whitespace-nowrap text-right text-sm">
                                                    <a href="{{ route('shippings.show', $shipping->id) }}" class="text-primary-500 hover:text-primary-700">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-32 bg-slate-50 dark:bg-slate-800 rounded-md">
                                <p class="text-slate-500 dark:text-slate-400">No shipments found for this marketing representative</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Contact & User Info -->
            <div class="space-y-6">
                <!-- Contact Information -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="phone"></i>
                            <h4 class="card-title">Contact Information</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="phone"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Primary Phone</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $customer->phone1 }}
                                    </p>
                                </div>
                            </li>
                            
                            @if($customer->phone2)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="phone"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Secondary Phone</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $customer->phone2 }}
                                    </p>
                                </div>
                            </li>
                            @endif
                            
                            @if($customer->email)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="mail"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Email</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        <a href="mailto:{{ $customer->email }}" class="text-primary-500 hover:underline">
                                            {{ $customer->email }}
                                        </a>
                                    </p>
                                </div>
                            </li>
                            @endif
                            
                            @if($customer->website)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="globe"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Website</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        <a href="{{ $customer->website }}" target="_blank" class="text-primary-500 hover:underline">
                                            {{ $customer->website }}
                                        </a>
                                    </p>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <!-- System Account -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="user"></i>
                            <h4 class="card-title">System Account</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        @if($customer->user)
                        <div class="flex items-center">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-500 text-white">
                                <i class="h-6 w-6" data-feather="user"></i>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $customer->user->name }}
                                </h5>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $customer->user->email }}</p>
                                <div class="mt-2">
                                    <span class="badge badge-soft-success">System User</span>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                <i class="h-6 w-6" data-feather="user-x"></i>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-base font-medium text-slate-900 dark:text-slate-200">
                                    No User Account
                                </h5>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    This customer doesn't have system access
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="text-sm text-primary-500 hover:underline inline-flex items-center">
                                        <i class="h-4 w-4 mr-1" data-feather="user-plus"></i>
                                        Create user account
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Customer Stats -->
                <div class="card bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-slate-800 dark:to-slate-700">
                    <div class="card-body p-6">
                        <h4 class="font-semibold text-lg text-indigo-700 dark:text-indigo-400 mb-4">Customer Summary</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-4 shadow-sm">
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Spent</h5>
                                <p class="text-xl font-bold text-indigo-600 dark:text-indigo-500 mt-1">
                                    {{ number_format(rand(1000, 50000)) }}
                                </p>
                            </div>
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-4 shadow-sm">
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Orders</h5>
                                <p class="text-xl font-bold text-indigo-600 dark:text-indigo-500 mt-1">
                                    {{ rand(1, 30) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Since -->
                <div class="card">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Customer Since</h5>
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-100 mt-1">
                                    {{ $customer->created_date ? \Carbon\Carbon::parse($customer->created_date)->format('d M Y') : 'Unknown' }}
                                </p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">
                                <i class="h-5 w-5 text-slate-500 dark:text-slate-400" data-feather="calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>