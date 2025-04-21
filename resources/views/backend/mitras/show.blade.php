<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Mitra Details" page="Mitra" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <!-- Mitra Header with Actions -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-500/10 text-primary-500">
                    <i class="h-7 w-7" data-feather="briefcase"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">
                        {{ $mitra->name }}
                        <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">({{ $mitra->code }})</span>
                    </h2>
                    <div class="mt-1 flex items-center gap-3">
                        <span class="badge {{ $mitra->status ? 'badge-success' : 'badge-danger' }}">
                            {{ $mitra->status ? 'Active' : 'Inactive' }}
                        </span>
                        @if($mitra->mitraGroup)
                        <span class="badge badge-soft-info">
                            {{ $mitra->mitraGroup->name }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('mitras.edit', $mitra->id) }}" class="btn btn-primary">
                    <i class="h-4 w-4" data-feather="edit-2"></i>
                    <span>Edit</span>
                </a>
                <form action="{{ route('mitras.destroy', $mitra->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this mitra?')">
                        <i class="h-4 w-4" data-feather="trash-2"></i>
                        <span>Delete</span>
                    </button>
                </form>
                <a href="{{ route('mitras.index') }}" class="btn btn-secondary">
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
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">{{ $mitra->code }}</p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Name</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">{{ $mitra->name }}</p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Mitra Group</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $mitra->mitraGroup->name ?? 'Not assigned' }}
                                    </p>
                                </div>
                                
                                @if($mitra->birthdate)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Birth Date</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($mitra->birthdate)->format('d M Y') }}
                                    </p>
                                </div>
                                @endif
                                
                                @if($mitra->created_date)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Registration Date</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($mitra->created_date)->format('d M Y') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Address</h5>
                                    <div class="mt-1">
                                        @if($mitra->address_office_indo)
                                            <p class="text-base font-medium text-slate-900 dark:text-slate-200 whitespace-pre-line">{{ $mitra->address_office_indo }}</p>
                                        @else
                                            <p class="text-base font-medium text-slate-400 dark:text-slate-500">No address provided</p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($mitra->country)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Country</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $mitra->country }}
                                    </p>
                                </div>
                                @endif
                                
                                @if($mitra->ktp)
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">KTP Number</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $mitra->ktp }}
                                    </p>
                                    {{-- view ktp --}}
                                    <a href="{{ asset('ktp/' . $mitra->ktp) }}" target="_blank" class="text-sm text-primary-500 hover:underline">
                                        View KTP
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Information -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="mr-2 h-5 w-5 text-primary-500" data-feather="credit-card"></i>
                                <h4 class="card-title">Financial Information</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <!-- Payment Terms Section -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Payment Terms</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $mitra->syarat_bayar > 0 ? $mitra->syarat_bayar . ' days' : 'Cash' }}
                                </p>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Due Date Terms</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $mitra->batas_tempo > 0 ? $mitra->batas_tempo . ' days' : 'N/A' }}
                                </p>
                            </div>
                            
                            @if($mitra->npwp)
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">NPWP Number</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $mitra->npwp }}
                                </p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Bank Accounts Section -->
                        <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-300 mb-4">Bank Accounts</h5>
                            
                            @if($mitra->banks && $mitra->banks->count() > 0)
                                <div class="space-y-4">
                                    @foreach($mitra->banks as $bankAccount)
                                        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-md {{ $bankAccount->is_default ? 'border-l-4 border-primary-500' : '' }}">
                                            <div class="flex items-center">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                                    <i class="h-5 w-5" data-feather="credit-card"></i>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <h6 class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                                            {{ $bankAccount->bank->name ?? 'Unknown Bank' }}
                                                            @if($bankAccount->is_default)
                                                                <span class="ml-2 text-xs text-primary-500 bg-primary-50 dark:bg-primary-900/30 py-0.5 px-2 rounded-full">
                                                                    Default
                                                                </span>
                                                            @endif
                                                        </h6>
                                                    </div>
                                                    <div class="mt-1 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
                                                        <div>
                                                            <span class="text-xs text-slate-500 dark:text-slate-400">Account Holder</span>
                                                            <p class="text-sm font-medium">{{ $bankAccount->rek_name ?? 'N/A' }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-xs text-slate-500 dark:text-slate-400">Account Number</span>
                                                            <p class="text-sm font-medium">{{ $bankAccount->rek_no ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-sm text-slate-500 dark:text-slate-400 italic">
                                    No bank accounts registered
                                </div>
                            @endif
                        </div>
                        
                        @if($mitra->tax_address)
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Tax Address</h5>
                            <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200 whitespace-pre-line">
                                {{ $mitra->tax_address }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Products by Warehouse Section -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="mr-2 h-5 w-5 text-primary-500" data-feather="box"></i>
                                <h4 class="card-title">Products by Warehouse</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        @if($mitra->warehouses && $mitra->warehouses->count() > 0)
                            <div class="space-y-6">
                                @foreach($mitra->warehouses as $warehouse)
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="bg-slate-50 dark:bg-slate-800 p-4 border-b">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-300">
                                                        <i class="h-5 w-5" data-feather="home"></i>
                                                    </div>
                                                    <div class="ml-3">
                                                        <h5 class="text-base font-medium text-slate-900 dark:text-slate-100">{{ $warehouse->name }}</h5>
                                                        <p class="text-xs text-slate-500">{{ ucfirst($warehouse->type) }} Warehouse</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="h-4 w-4 mr-1" data-feather="box"></i>
                                                        Manage Products
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-4">
                                            @if($warehouse->products && $warehouse->products->count() > 0) 
                                                <div class="overflow-x-auto">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Category</th>
                                                                <th>Mitra Price</th>
                                                                <th>Customer Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                // Get only parent products (top level)
                                                                $parentProducts = $warehouse->products->whereNull('parent_id')->take(5);
                                                            @endphp
                                                            
                                                            @forelse($parentProducts as $product)
                                                                <tr>
                                                                    <td>{{ $product->name }}</td>
                                                                    <td>
                                                                        @if($product->category)
                                                                            <span class="badge badge-soft-primary">{{ $product->category->name }}</span>
                                                                        @else
                                                                            <span class="text-slate-400">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <div class="flex flex-col">
                                                                            <span class="text-xs text-slate-500">CBM: Rp {{ number_format($product->mit_price_cbm, 0, ',', '.') }}</span>
                                                                            <span class="text-xs text-slate-500">KG: Rp {{ number_format($product->mit_price_kg, 0, ',', '.') }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="flex flex-col">
                                                                            <span class="text-xs text-slate-500">CBM: Rp {{ number_format($product->cust_price_cbm, 0, ',', '.') }}</span>
                                                                            <span class="text-xs text-slate-500">KG: Rp {{ number_format($product->cust_price_kg, 0, ',', '.') }}</span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="4" class="text-center py-4 text-slate-500">No products found for this warehouse</td>
                                                                </tr>
                                                            @endforelse
                                                            
                                                            @if($warehouse->products->count() > 5)
                                                                <tr>
                                                                    <td colspan="4" class="text-center">
                                                                        <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" 
                                                                           class="text-primary-500 text-sm hover:underline">
                                                                            View all {{ $warehouse->products->count() }} products...
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center py-6">
                                                    <div class="inline-flex rounded-full bg-slate-100 p-3 dark:bg-slate-700 mb-3">
                                                        <i class="h-6 w-6 text-slate-500" data-feather="box"></i>
                                                    </div>
                                                    <h5 class="font-medium text-slate-700 dark:text-slate-300 mb-1">No Products Found</h5>
                                                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-3">This warehouse doesn't have any products yet.</p>
                                                    <a href="{{ route('mitra.warehouses.products.create', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="h-4 w-4 mr-1" data-feather="plus"></i>
                                                        Add First Product
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <div class="mb-3 rounded-full bg-slate-100 p-3 dark:bg-slate-700">
                                    <i class="h-6 w-6 text-slate-500 dark:text-slate-400" data-feather="package"></i>
                                </div>
                                <h5 class="mb-1 text-base font-medium text-slate-700 dark:text-slate-300">No Warehouses Found</h5>
                                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-4">
                                    You need to add warehouses before you can manage products.
                                </p>
                                <a href="{{ route('mitra.warehouses.create', $mitra->id) }}" class="btn btn-primary">
                                    <i class="h-4 w-4 mr-1" data-feather="plus"></i>
                                    Add First Warehouse
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Warehouses Section -->
            
                
              
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
                                        {{ $mitra->phone1 }}
                                    </p>
                                </div>
                            </li>
                            
                            @if($mitra->phone2)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="phone"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Secondary Phone</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $mitra->phone2 }}
                                    </p>
                                </div>
                            </li>
                            @endif
                            
                            @if($mitra->email)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="mail"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Email</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        <a href="mailto:{{ $mitra->email }}" class="text-primary-500 hover:underline">
                                            {{ $mitra->email }}
                                        </a>
                                    </p>
                                </div>
                            </li>
                            @endif
                            
                            @if($mitra->website)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="globe"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Website</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        <a href="{{ $mitra->website }}" target="_blank" class="text-primary-500 hover:underline">
                                            {{ $mitra->website }}
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
                        @if($mitra->user)
                        <div class="flex items-center">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-500 text-white">
                                <i class="h-6 w-6" data-feather="user"></i>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $mitra->user->name }}
                                </h5>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $mitra->user->email }}</p>
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
                                    This mitra doesn't have system access
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('mitras.edit', $mitra->id) }}" class="text-sm text-primary-500 hover:underline inline-flex items-center">
                                        <i class="h-4 w-4 mr-1" data-feather="user-plus"></i>
                                        Create user account
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Warehouse List -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="package"></i>
                            <h4 class="card-title">Warehouse List</h4>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if($mitra->warehouses && $mitra->warehouses->count() > 0)
                            <ul class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($mitra->warehouses as $warehouse)
                                    <li class="py-3 first:pt-0 last:pb-0">
                                        <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" 
                                           class="flex items-center hover:bg-slate-50 dark:hover:bg-slate-800 -mx-2 px-2 py-1 rounded">
                                            <div class="h-8 w-8 flex items-center justify-center rounded-md bg-{{ $warehouse->type == 'sea' ? 'blue' : 'orange' }}-100 text-{{ $warehouse->type == 'sea' ? 'blue' : 'orange' }}-600 dark:bg-{{ $warehouse->type == 'sea' ? 'blue' : 'orange' }}-900/30 dark:text-{{ $warehouse->type == 'sea' ? 'blue' : 'orange' }}-400 mr-3">
                                                <i class="h-4 w-4" data-feather="{{ $warehouse->type == 'sea' ? 'anchor' : 'truck' }}"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h5 class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{{ $warehouse->name }}</h5>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $warehouse->products_count ?? $warehouse->products->count() }} products · {{ ucfirst($warehouse->type) }} freight
                                                </p>
                                            </div>
                                            <div class="ml-2">
                                                <i class="h-5 w-5 text-slate-400" data-feather="chevron-right"></i>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                                <a href="{{ route('mitra.warehouses.create', $mitra->id) }}" class="btn btn-sm btn-outline-primary w-full">
                                    <i class="h-4 w-4 mr-1" data-feather="plus"></i>
                                    Add New Warehouse
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="inline-flex rounded-full bg-slate-100 p-3 dark:bg-slate-700 mb-3">
                                    <i class="h-6 w-6 text-slate-500" data-feather="package"></i>
                                </div>
                                <h5 class="font-medium text-slate-700 dark:text-slate-300 mb-1">No Warehouses Yet</h5>
                                <p class="text-slate-500 dark:text-slate-400 text-sm mb-3">Add a warehouse to manage products</p>
                                <a href="{{ route('mitra.warehouses.create', $mitra->id) }}" class="btn btn-sm btn-primary">
                                    <i class="h-4 w-4 mr-1" data-feather="plus"></i>
                                    Add First Warehouse
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Mitra Stats -->
                <div class="card bg-gradient-to-br from-blue-50 to-blue-100 dark:from-slate-800 dark:to-slate-700">
                    <div class="card-body p-6">
                        <h4 class="font-semibold text-lg text-blue-700 dark:text-blue-400 mb-4">Mitra Summary</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-4 shadow-sm">
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Warehouses</h5>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-500 mt-1">
                                    {{ $mitra->warehouses ? $mitra->warehouses->count() : 0 }}
                                </p>
                            </div>
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-4 shadow-sm">
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Products</h5>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-500 mt-1">
                                    @php
                                        $totalProducts = 0;
                                        if ($mitra->warehouses) {
                                            foreach ($mitra->warehouses as $warehouse) {
                                                $totalProducts += $warehouse->products ? $warehouse->products->count() : 0;
                                            }
                                        }
                                    @endphp
                                    {{ $totalProducts }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                  <!-- Warehouse List (Compact) -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="mr-2 h-5 w-5 text-primary-500" data-feather="package"></i>
                                <h4 class="card-title">Warehouses</h4>
                            </div>
                            @if($mitra->status)
                                <a href="{{ route('mitra.warehouses.create', $mitra->id) }}" class="text-sm text-primary-500 hover:underline">
                                    <i class="h-4 w-4 inline-block mr-1" data-feather="plus"></i> Add
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if($mitra->warehouses && $mitra->warehouses->count() > 0)
                            <div class="space-y-3">
                                @foreach($mitra->warehouses as $warehouse)
                                    <div class="bg-slate-50 dark:bg-slate-800 rounded-md overflow-hidden">
                                        <div class="flex flex-col">
                                            @if($warehouse->address_photo)
                                                <div class="h-32">
                                                    <img src="{{ asset($warehouse->address_photo) }}" 
                                                         alt="{{ $warehouse->name }}" 
                                                         class="w-full h-full object-cover" />
                                                </div>
                                            @endif
                                            <div class="p-3">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <h6 class="text-sm font-medium text-slate-800 dark:text-slate-100 flex items-center">
                                                            {{ $warehouse->name }}
                                                            <span class="ml-2 badge {{ $warehouse->type == 'sea' ? 'badge-soft-blue' : 'badge-soft-orange' }} text-xs">
                                                                {{ ucfirst($warehouse->type) }}
                                                            </span>
                                                        </h6>
                                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                            {{ Str::limit($warehouse->address, 40) ?: 'No address' }}
                                                        </p>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <a href="{{ route('mitra.warehouses.edit', [$mitra->id, $warehouse->id]) }}" 
                                                           class="text-info-500 hover:text-info-600">
                                                            <i class="h-4 w-4" data-feather="edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="text-danger-500 hover:text-danger-600 delete-warehouse-btn" 
                                                                data-warehouse-id="{{ $warehouse->id }}" 
                                                                data-mitra-id="{{ $mitra->id }}" 
                                                                data-warehouse-name="{{ $warehouse->name }}">
                                                            <i class="h-4 w-4" data-feather="trash-2"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" 
                                                       class="text-xs text-primary-500 hover:underline flex items-center">
                                                        <i class="h-3.5 w-3.5 mr-1" data-feather="box"></i>
                                                        {{ $warehouse->products_count ?? $warehouse->products->count() }} Products
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <div class="inline-flex rounded-full bg-slate-100 p-2 dark:bg-slate-700 mb-2">
                                    <i class="h-5 w-5 text-slate-500" data-feather="package"></i>
                                </div>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">No warehouses added yet</p>
                                @if($mitra->status)
                                    <a href="{{ route('mitra.warehouses.create', $mitra->id) }}" class="btn btn-sm btn-primary mt-2">
                                        <i class="h-4 w-4 mr-1" data-feather="plus"></i>
                                        Add Warehouse
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Mitra Since -->
                <div class="card">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Mitra Since</h5>
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-100 mt-1">
                                    {{ $mitra->created_date ? \Carbon\Carbon::parse($mitra->created_date)->format('d M Y') : \Carbon\Carbon::parse($mitra->created_at)->format('d M Y') }}
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle warehouse deletion with SweetAlert2
        const deleteButtons = document.querySelectorAll('.delete-warehouse-btn');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const warehouseId = this.getAttribute('data-warehouse-id');
                const mitraId = this.getAttribute('data-mitra-id');
                const warehouseName = this.getAttribute('data-warehouse-name');
                
                Swal.fire({
                    title: 'Delete Warehouse?',
                    html: `Are you sure you want to delete warehouse <strong>${warehouseName}</strong>?<br>This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit form programmatically
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/mitras/${mitraId}/warehouses/${warehouseId}`;
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        form.appendChild(csrfToken);
                        
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush