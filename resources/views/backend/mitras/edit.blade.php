<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Edit Mitra" page="Mitra" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <div class="card">
            <div class="card-body p-6">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="{{ route('mitras.update', $mitra->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="mitraFormTabs" role="tablist">
                                <li class="mr-2" role="presentation">
                                    <button class="inline-flex items-center p-4 border-b-2 rounded-t-lg active" id="basic-tab" data-tab="basic-content" type="button" role="tab" aria-controls="basic" aria-selected="true">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Basic Information
                                    </button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="inline-flex items-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="contact-tab" data-tab="contact-content" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                        <i class="fas fa-address-book mr-2"></i>
                                        Contact Information
                                    </button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="inline-flex items-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="financial-tab" data-tab="financial-content" type="button" role="tab" aria-controls="financial" aria-selected="false">
                                        <i class="fas fa-money-bill-wave mr-2"></i>
                                        Financial Information
                                    </button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="inline-flex items-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="warehouse-tab" data-tab="warehouse-content" type="button" role="tab" aria-controls="warehouse" aria-selected="false">
                                        <i class="fas fa-warehouse mr-2"></i>
                                        Warehouses
                                    </button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="inline-flex items-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="shipping-rates-tab" data-tab="shipping-rates-content" type="button" role="tab" aria-controls="shipping-rates" aria-selected="false">
                                        <i class="fas fa-tags mr-2"></i>
                                        Shipping Rates
                                    </button>
                                </li>
                                {{-- <li role="presentation">
                                    <button class="inline-flex items-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="account-tab" data-tab="account-content" type="button" role="tab" aria-controls="account" aria-selected="false">
                                        <i class="fas fa-user-shield mr-2"></i>
                                        System Account
                                    </button>
                                </li> --}}
                            </ul>
                        </div>
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Basic Information Tab -->
                        <div class="tab-pane active" id="basic-content" role="tabpanel" aria-labelledby="basic-tab">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Code -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="code">Marking Code</label>
                                    <input type="text" class="input"  name="code" id="code" value="{{ $mitra->code }}"  />
                                </div>
                                
                                <!-- Name -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="name">Name</label>
                                    <input type="text" class="input" id="name" name="name" value="{{ old('name', $mitra->name) }}" required />
                                </div>
                                
                                <!-- Mitra Group -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="mitra_group_id">Mitra Group</label>
                                    <select id="mitra_group_id" name="mitra_group_id" class="select" required>
                                        <option value="">Select Mitra Group</option>
                                        @foreach($mitraGroups as $group)
                                            <option value="{{ $group->id }}" {{ old('mitra_group_id', $mitra->mitra_group_id) == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Status -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="status">Status</label>
                                    <select id="status" name="status" class="select" required>
                                        <option value="1" {{ old('status', $mitra->status) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $mitra->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                
                                <!-- Created Date -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="created_date">Registration Date</label>
                                    <input type="date" class="input" id="created_date" name="created_date" 
                                           value="{{ old('created_date', $mitra->created_date ? $mitra->created_date->format('Y-m-d') : '') }}" />
                                </div>

                                <!-- Birthdate -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="birthdate">Birth Date</label>
                                    <input type="date" class="input" id="birthdate" name="birthdate" 
                                           value="{{ old('birthdate', $mitra->birthdate ? $mitra->birthdate->format('Y-m-d') : '') }}" />
                                </div>
                                
                                <!-- KTP -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="ktp">KTP</label>
                                    <input type="file" class="input" id="ktp" name="ktp" />
                                    @if($mitra->ktp)
                                        <div class="mt-2 flex items-center">
                                            <i class="fas fa-file  mr-2"></i>
                                            <span class="text-sm">Current file: {{ basename($mitra->ktp) }}</span>
                                            <a href="{{ asset('ktp/' .$mitra->ktp) }}" target="_blank" class="text-primary-500 hover:underline ml-2">View</a>
                                        </div>
                                    @endif
                                </div>
                                                                
                                <!-- Payment Terms -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="syarat_bayar">Payment Terms (Days)</label>
                                    <input type="number" class="input" id="syarat_bayar" name="syarat_bayar" 
                                           value="{{ old('syarat_bayar', $mitra->syarat_bayar) }}" min="0" />
                                </div>
                                
                                <!-- Due Date Terms -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="batas_tempo">Due Date Terms (Days)</label>
                                    <input type="number" class="input" id="batas_tempo" name="batas_tempo" 
                                           value="{{ old('batas_tempo', $mitra->batas_tempo) }}" min="0" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information Tab -->
                        <div class="tab-pane hidden" id="contact-content" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Office Address -->
                                <div class="flex flex-col gap-1 md:col-span-2">
                                    <label class="label mb-1 font-medium" for="address_office_indo">Office Address</label>
                                    <textarea class="textarea" id="address_office_indo" name="address_office_indo" rows="2">{{ old('address_office_indo', $mitra->address_office_indo) }}</textarea>
                                </div>
                                
                                <!-- Country -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="country">Country</label>
                                    <select id="country" name="country[]" class="tom-select" multiple="">
                                        @php 
                                            $selectedCountries = old('country', $mitra->mitraCountry ? $mitra->mitraCountry->pluck('name')->toArray() : []);
                                        @endphp
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" 
                                                {{ in_array($country, $selectedCountries) ? 'selected' : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Phone 1 -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="phone1">Primary Phone</label>
                                    <input type="text" class="input" id="phone1" name="phone1" value="{{ old('phone1', $mitra->phone1) }}" required />
                                </div>
                                
                                <!-- Phone 2 -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="phone2">Secondary Phone</label>
                                    <input type="text" class="input" id="phone2" name="phone2" value="{{ old('phone2', $mitra->phone2) }}" />
                                </div>
                                
                                <!-- Email -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="email">Email</label>
                                    <input type="email" class="input" id="email" name="email" value="{{ old('email', $mitra->email) }}" />
                                </div>
                                
                                <!-- Website -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="website">Website</label>
                                    <input type="url" class="input" id="website" name="website" value="{{ old('website', $mitra->website) }}" placeholder="https://" />
                                </div>
                                
                                <!-- NPWP -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="npwp">NPWP</label>
                                    <input type="text" class="input" id="npwp" name="npwp" value="{{ old('npwp', $mitra->npwp) }}" />
                                </div>
                                
                                <!-- Tax Address -->
                                <div class="flex flex-col gap-1 md:col-span-2">
                                    <label class="label mb-1 font-medium" for="tax_address">Tax Address</label>
                                    <textarea class="textarea" id="tax_address" name="tax_address" rows="2">{{ old('tax_address', $mitra->tax_address) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Financial Information Tab -->
                        <div class="tab-pane hidden" id="financial-content" role="tabpanel" aria-labelledby="financial-tab">
                            <!-- Bank Accounts Section -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200">Bank Accounts</h3>
                                    <button type="button" id="addBankAccount" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus mr-1"></i> Add Bank Account
                                    </button>
                                </div>
                                
                                <div id="bankAccountsContainer">
                                    @php
                                        // Get bank accounts from old input or from the model
                                        $bankAccounts = old('bank_accounts', $mitra->banks ? $mitra->banks->toArray() : [['id' => 0]]);
                                    @endphp
                                    
                                    @forelse($bankAccounts as $index => $bankAccount)
                                        <div class="bank-account-record bg-slate-50 dark:bg-slate-800 p-4 rounded-md mb-4" data-record-id="{{ $index }}">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="text-md font-medium text-slate-700 dark:text-slate-200">Bank Account #<span class="record-number">{{ $index + 1 }}</span></h4>
                                                <div class="flex items-center">
                                                <div class="flex items-center mr-4">
    <input type="checkbox" id="is_default_{{ $index }}" name="bank_accounts[{{ $index }}][is_default]" value="1" class="default-bank-checkbox mr-2" 
        {{ old("bank_accounts.$index.is_default", isset($bankAccount['is_default']) ? $bankAccount['is_default'] : false) ? 'checked' : '' }}>
    <label for="is_default_{{ $index }}" class="text-sm">Default Account</label>
</div>
                                                    <button type="button" class="remove-bank-account text-red-500 hover:text-red-700" {{ $index === 0 && count($bankAccounts) <= 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- If this is an existing record, keep the ID -->
                                            @if(isset($bankAccount['id']) && !is_numeric($index))
                                                <input type="hidden" name="bank_accounts[{{ $index }}][id]" value="{{ $bankAccount['id'] }}">
                                            @endif
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="flex flex-col gap-1">
                                                    <label class="label mb-1 font-medium" for="bank_id_{{ $index }}">Bank</label>
                                                    <select id="bank_id_{{ $index }}" name="bank_accounts[{{ $index }}][bank_id]" class="select">
                                                        <option value="">Select Bank</option>
                                                        @foreach($banks as $bank)
                                                            <option value="{{ $bank->id }}" 
                                                                {{ old("bank_accounts.$index.bank_id", 
                                                                    isset($bankAccount['bank_id']) ? $bankAccount['bank_id'] : null) == $bank->id ? 'selected' : '' }}>
                                                                {{ $bank->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="flex flex-col gap-1">
                                                    <label class="label mb-1 font-medium" for="rek_name_{{ $index }}">Account Name</label>
                                                    <input type="text" class="input" id="rek_name_{{ $index }}" name="bank_accounts[{{ $index }}][rek_name]" 
                                                        value="{{ old("bank_accounts.$index.rek_name", isset($bankAccount['rek_name']) ? $bankAccount['rek_name'] : '') }}" />
                                                </div>
                                                
                                                <div class="flex flex-col gap-1">
                                                    <label class="label mb-1 font-medium" for="rek_no_{{ $index }}">Account Number</label>
                                                    <input type="text" class="input" id="rek_no_{{ $index }}" name="bank_accounts[{{ $index }}][rek_no]" 
                                                        value="{{ old("bank_accounts.$index.rek_no", isset($bankAccount['rek_no']) ? $bankAccount['rek_no'] : '') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <!-- Default empty bank account record if none exists -->
                                        <div class="bank-account-record bg-slate-50 dark:bg-slate-800 p-4 rounded-md mb-4" data-record-id="0">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="text-md font-medium text-slate-700 dark:text-slate-200">Bank Account #<span class="record-number">1</span></h4>
                                                <div class="flex items-center">
                                                    <div class="flex items">
                                                        <input type="checkbox" id="is_default_0" name="bank_accounts[0][is_default]" value="1" class="default-bank-checkbox mr-2" 
                                                            {{ old('bank_accounts.0.is_default', false) ? 'checked' : '' }}>
                                                        <label for="is_default_0" class="text-sm">Default Account</label>
                                                    </div>
                                                    <button type="button" class="remove-bank-account text-red-500 hover:text-red-700" disabled>
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="flex flex-col gap-1">
                                                    <label class="label mb-1 font-medium" for="bank_id_0">Bank</label>
                                                    <select id="bank_id_0" name="bank_accounts[0][bank_id]" class="select">
                                                        <option value="">Select Bank</option>
                                                        @foreach($banks as $bank)
                                                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="flex flex-col gap-1">
                                                    <label class="label mb-1 font-medium" for="rek_name_0">Account Name</label>
                                                    <input type="text" class="input" id="rek_name_0" name="bank_accounts[0][rek_name]" value="" />
                                                </div>
                                                
                                                <div class="flex flex-col gap-1">
                                                    <label class="label mb-1 font-medium" for="rek_no_0">Account Number</label>
                                                    <input type="text" class="input" id="rek_no_0" name="bank_accounts[0][rek_no]" value="" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        <!-- System Account Tab -->
                        {{-- <div class="tab-pane hidden" id="account-content" role="tabpanel" aria-labelledby="account-tab">
                            <!-- User Account Checkbox -->
                            <div class="mb-6">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input id="create_account" name="create_account" type="checkbox" class="checkbox" 
                                               {{ old('create_account', $mitra->user_id ? true : false) ? 'checked' : '' }} />
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="create_account" class="font-medium text-primary-600 cursor-pointer">
                                            {{ $mitra->user_id ? 'Maintain user account' : 'Create user account' }}
                                        </label>
                                        <p class="text-slate-500">{{ $mitra->user_id ? 'Uncheck to unlink user account' : 'This will allow the mitra to log in to the system' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Account Section -->
                            <div id="account_section" class="grid grid-cols-1 md:grid-cols-2 gap-6 border-l-4 border-primary-500 pl-4 py-4 {{ old('create_account', $mitra->user_id ? true : false) ? '' : 'hidden' }}">
                                <div class="col-span-1 md:col-span-2">
                                    <p class="text-sm text-primary-600 font-medium mb-3">
                                        <i class="inline-block h-4 w-4 mr-1" data-feather="alert-circle"></i>
                                        {{ $mitra->user_id ? 'Update user account password (optional)' : 'Create a user account for this mitra' }}
                                    </p>
                                    
                                    @if($mitra->user)
                                    <div class="flex items-center p-3 bg-primary-50 dark:bg-slate-800 rounded-md mb-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-500 text-white dark:bg-primary-600">
                                            <i class="h-5 w-5" data-feather="user"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-200">Linked User Account</h5>
                                            <p class="text-sm text-slate-500">{{ $mitra->user->name }} ({{ $mitra->user->email }})</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label {{ $mitra->user_id ? '' : 'label-required' }} mb-1 font-medium" for="password">
                                        {{ $mitra->user_id ? 'New Password' : 'Password' }}
                                    </label>
                                    <input type="password" class="input" id="password" name="password"  />
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $mitra->user_id ? 'Leave blank to keep current password' : 'Password must be at least 8 characters' }}
                                    </p>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label {{ $mitra->user_id ? '' : 'label-required' }} mb-1 font-medium" for="password_confirmation">
                                        Confirm {{ $mitra->user_id ? 'New ' : '' }}Password
                                    </label>
                                    <input type="password" class="input" id="password_confirmation" name="password_confirmation"  />
                                </div>
                            </div>
                        </div>
                         --}}
                        <!-- Warehouse Tab -->
                        <div class="tab-pane hidden" id="warehouse-content" role="tabpanel" aria-labelledby="warehouse-tab">
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200">Mitra Warehouses</h3>
                                    <a href="{{ route('mitra.warehouses.create', $mitra->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus mr-1"></i> Add Warehouse
                                    </a>
                                </div>
                                
                              
                                
                                @if($warehouses->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Country</th>
            <th>Address</th>
            <th>Products</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($warehouses as $warehouse)
            <tr>
                <td>{{ $warehouse->name }}</td>
                <td>
                    <span class="badge {{ $warehouse->type == 'sea' ? 'badge badge-soft-primary' : 'badge-soft-warning ' }}">
                        {{ ucfirst($warehouse->type) }}
                    </span>
                </td>
                <td>
                    @if($warehouse->countries)
                        <span class="badge badge-soft-info">
                            {{ $warehouse->countries?->pluck('name')->implode(', ') }}
                        </span>
                    @else
                        <span class="badge badge-soft-danger">
                            No Country
                        </span>
                    @endif
                </td>
                <td>{{ Str::limit($warehouse->address, 50) }}</td>
                <td>
                    <span class="badge badge-soft-success">
                        {{ $warehouse->products_count ?? 0 }} products
                    </span>
                </td>
                <td>
                    <div class="flex space-x-2">
                        <a href="{{ route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" 
                           class="btn btn-sm btn-success" 
                           title="Manage Products">
                            <i class="fas fa-box"></i>
                        </a>
                        <a href="{{ route('mitra.warehouses.edit', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id]) }}" 
                           class="btn btn-sm btn-info"
                           title="Edit Warehouse">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" 
                                class="btn btn-sm btn-danger delete-warehouse-btn" 
                                data-warehouse-id="{{ $warehouse->id }}" 
                                data-mitra-id="{{ $mitra->id }}" 
                                data-warehouse-name="{{ $warehouse->name }}"
                                title="Delete Warehouse">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
                                    </div>
                                @else
                                    <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-md text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-warehouse text-4xl text-slate-400"></i>
                                        </div>
                                        <h4 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-1">No Warehouses Found</h4>
                                        <p class="text-slate-500 mb-4">This mitra doesn't have any warehouses yet.</p>
                                        <a href="{{ route('mitra.warehouses.create', $mitra->id) }}" class="btn btn-primary">
                                            <i class="fas fa-plus mr-1"></i> Add First Warehouse
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Shipping Rates Tab -->
                        <div class="tab-pane hidden" id="shipping-rates-content" role="tabpanel" aria-labelledby="shipping-rates-tab">
    <div class="p-4 mb-6 bg-blue-50 dark:bg-slate-700 rounded-lg border border-blue-100 dark:border-slate-600">
        <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-4">
            <i class="fas fa-tags mr-2 text-blue-500"></i>
            Default Shipping Rates
        </h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
            These rates will be used as default values when creating shipments for this mitra.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Volume-based Rate (CBM) -->
            <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3">
                        <i class="fas fa-cube text-blue-500"></i>
                    </div>
                    <h4 class="text-md font-medium text-slate-700 dark:text-slate-300">Volume-Based Rate (CBM)</h4>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="label mb-1 text-sm font-medium" for="harga_ongkir_cbm">Price per Cubic Meter</label>
                    <div class="relative">
                      
                        <input type="text" class="input pl-10" id="harga_ongkir_cbm" name="harga_ongkir_cbm" 
                               value="{{ old('harga_ongkir_cbm', $mitra->harga_ongkir_cbm) }}"  />
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Used for shipping cost calculations based on volume (CBM)</p>
                </div>
            </div>
            
            <!-- Weight-based Rate (KG) -->
            <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3">
                        <i class="fas fa-weight text-green-500"></i>
                    </div>
                    <h4 class="text-md font-medium text-slate-700 dark:text-slate-300">Weight-Based Rate (WG)</h4>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="label mb-1 text-sm font-medium" for="max_wg">Maximum Weight (KG)</label>
                    <div class="relative">
                   
                        <input type="text" class="input pl-10" id="max_wg" name="max_wg" 
                               value="{{ old('max_wg', $mitra->max_wg) }}"  />
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Maximum weight this mitra can handle (KG)</p>
                    
                    <label class="label mb-1 text-sm font-medium mt-3" for="harga_ongkir_wg">Price per Kilogram</label>
                    <div class="relative">
                      
                        <input type="text" class="input pl-10" id="harga_ongkir_wg" name="harga_ongkir_wg" 
                               value="{{ old('harga_ongkir_wg', $mitra->harga_ongkir_wg) }}"  />
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Used for shipping cost calculations based on weight (KG)</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800/30">
        <div class="flex items-start">
            <div class="mr-3 text-yellow-500">
                <i class="fas fa-info-circle text-xl"></i>
            </div>
            <div>
                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">About Shipping Rates</h4>
                <p class="text-xs text-yellow-700 dark:text-yellow-500 mt-1">
                    Setting these rates will allow the system to automatically calculate shipping costs when creating shipments.
                    For CBM-based shipping, costs are calculated by multiplying the volume (m³) by the rate per cubic meter.
                    For weight-based shipping, costs are calculated by multiplying the gross weight (kg) by the rate per kilogram.
                    The maximum weight setting helps ensure shipments don't exceed the mitra's capacity.
                </p>
            </div>
        </div>
    </div>
</div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-between border-t pt-4">
                        <div>
                            <button type="button" id="prevTabButton" class="btn btn-secondary" disabled>
                                <i class="fas fa-arrow-left mr-1"></i> Previous
                            </button>
                            <button type="button" id="nextTabButton" class="btn btn-info ml-2">
                                Next <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('mitras.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary ml-2">Update Mitra</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    
    
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="sweetalert2.min.css">
<script src="sweetalert2.min.js"></script>

<style>
    .swal2-confirm swal2-styled {
        background-color: #4f46e5 !important;
        color: #fff;
        border: #4f46e5 !important;
        border-radius: 0.375rem !important;
    }
    .tab-pane.active {
        display: block;
    }
    .tab-pane.hidden {
        display: none;
    }
    #mitraFormTabs button {
        color: #64748b;
    }
    #mitraFormTabs button.active {
        color: rgb(139 92 246 / var(--tw-bg-opacity));
        border-color: rgb(139 92 246 / var(--tw-bg-opacity));
    }
    .dark #mitraFormTabs button {
        color: #94a3b8;
    }
    .dark #mitraFormTabs button.active {
        color: #60a5fa;
        border-color: #60a5fa;
    }
    
    /* Notification animations */
    .notification {
        animation: slide-in 0.3s ease-out forwards;
    }
    @keyframes slide-in {
        0% {
            transform: translateX(100%);
            opacity: 0;
        }
        100% {
            transform: translateX(0);
            opacity: (1);
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script>
    // cleave shpipping rates
    var cleave = new Cleave('#harga_ongkir_cbm', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.'
    });
    var cleave = new Cleave('#harga_ongkir_wg', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.'
    });

    // max wg
    var cleave = new Cleave('#max_wg', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.'
    });
</script>


<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: 'Select Country',
            allowClear: true,
            width: '100%',
            tags: true,
            theme: document.documentElement.classList.contains('dark') ? 'classic' : 'default'
        });
        
        // Tab functionality
        const tabs = ['basic-tab', 'contact-tab', 'shipping-rates-tab', 'financial-tab', 'warehouse-tab', 'account-tab'];
        const tabContents = ['basic-content', 'contact-content', 'shipping-rates-content', 'financial-content', 'warehouse-content', 'account-content'];
        let currentTabIndex = 0;
        
        function showTab(index) {
            // Hide all tabs first
            $('#mitraFormTabs button').removeClass('active').attr('aria-selected', 'false');
            $('.tab-pane').addClass('hidden').removeClass('active');
            
            // Show the selected tab
            $(`#${tabs[index]}`).addClass('active').attr('aria-selected', 'true');
            $(`#${tabContents[index]}`).removeClass('hidden').addClass('active');
            
            // Update prev/next buttons
            $('#prevTabButton').prop('disabled', index === 0);
            $('#nextTabButton').prop('disabled', index === tabs.length - 1);
            $('#nextTabButton').text(index === tabs.length - 2 ? 'Finish' : 'Next');
            
            currentTabIndex = index;
        }
        
        // Tab click event
        $('#mitraFormTabs button').on('click', function() {
            const tabId = $(this).attr('id');
            const index = tabs.indexOf(tabId);
            showTab(index);
        });
        
        // Next button
        $('#nextTabButton').on('click', function() {
            if (currentTabIndex < tabs.length - 1) {
                showTab(currentTabIndex + 1);
            }
        });
        
        // Previous button
        $('#prevTabButton').on('click', function() {
            if (currentTabIndex > 0) {
                showTab(currentTabIndex - 1);
            }
        });
        
        // Toggle password fields based on checkbox
        $('#create_account').change(function() {
            if(this.checked) {
                $('#account_section').removeClass('hidden');
                // Only make passwords required if no user account exists yet
                if(!`{{ $mitra->user_id ? 'true' : 'false' }}`) {
                    $('#password, #password_confirmation').prop('required', true);
                }
            } else {
                $('#account_section').addClass('hidden');
                $('#password, #password_confirmation').prop('required', false);
            }
        });
        
        // Bank account section
        let bankCounter = `{{ count(old('bank_accounts', $mitra->banks ? $mitra->banks->toArray() : [0])) }}`;
        
        // Add bank account
        $('#addBankAccount').on('click', function() {
            // Clone the first bank account template
            const $template = $('.bank-account-record').first().clone();
            
            // Update IDs and names with new counter
            bankCounter++;
            $template.attr('data-record-id', bankCounter);
            $template.find('.record-number').text(bankCounter + 1);
            
            // Update all input and select elements with new index
            $template.find('input, select').each(function() {
                const originalName = $(this).attr('name');
                if (originalName) {
                    const newName = originalName.replace(/\[\d+\]/g, `[${bankCounter}]`);
                    $(this).attr('name', newName);
                }
                
                const originalId = $(this).attr('id');
                if (originalId) {
                    const newId = originalId.replace(/_\d+$/, `_${bankCounter}`);
                    $(this).attr('id', newId);
                }
                
                // Clear values except for hidden inputs
                if ($(this).attr('type') !== 'hidden') {
                    $(this).val('');
                }
                
                // If it's a checkbox
                if ($(this).attr('type') === 'checkbox') {
                    $(this).prop('checked', false);
                }
            });
            
            // Remove any hidden id field (we don't want to associate with an existing record)
            $template.find('input[name^="bank_accounts"][name$="[id]"]').remove();
            
            // Update labels
            $template.find('label').each(function() {
                const originalFor = $(this).attr('for');
                if (originalFor) {
                    const newFor = originalFor.replace(/_\d+$/, `_${bankCounter}`);
                    $(this).attr('for', newFor);
                }
            });
            
            // Enable delete button
            $template.find('.remove-bank-account').prop('disabled', false);
            
            // Add to container
            $('#bankAccountsContainer').append($template);
            
            // Default bank logic - uncheck others if this is checked
            updateDefaultBankLogic();
        });
        
        // Remove bank account
        $(document).on('click', '.remove-bank-account', function() {
            if ($('.bank-account-record').length > 1) {
                const $record = $(this).closest('.bank-account-record');
                
                // If this is an existing record (has an ID), mark it for deletion instead of removing immediately
                const bankId = $record.find('input[name$="[id]"]').val();
                if (bankId) {
                    $record.hide();
                    $record.append(`<input type="hidden" name="deleted_bank_accounts[]" value="${bankId}">`);
                } else {
                    $record.remove();
                }
                
                // Re-number visible records
                $('.bank-account-record:visible').each(function(index) {
                    $(this).find('.record-number').text(index + 1);
                });
                
                // If deleting the default account, set the first visible one as default
                if ($record.find('.default-bank-checkbox').prop('checked') && $('.bank-account-record:visible').length > 0) {
                    $('.bank-account-record:visible').first().find('.default-bank-checkbox').prop('checked', true);
                }
            }
        });
        
        // Default bank logic
        function updateDefaultBankLogic() {
            $(document).on('change', '.default-bank-checkbox', function() {
                if ($(this).prop('checked')) {
                    // Uncheck all other default checkboxes
                    $('.default-bank-checkbox').not(this).prop('checked', false);
                }
            });
        }
        
        // Call it initially
        updateDefaultBankLogic();
        
        // Email warning if user account is checked but email is empty
        $('#email').on('change keyup', function() {
            if($('#create_account').is(':checked') && $(this).val() === '') {
                if(!$('#email-warning').length) {
                    $('<p id="email-warning" class="text-xs text-danger-500 mt-1">Email is required for user account</p>')
                        .insertAfter($(this));
                }
            } else {
                $('#email-warning').remove();
            }
        });
        
        // If displaying an existing mitra with user account, prevent accidentally
        // disabling the account without explicit confirmation
        if(`{{ $mitra->user_id ? 'true' : 'false' }}`) {
            var originalChecked = $('#create_account').is(':checked');
            
            $('#create_account').on('change', function() {
                if(originalChecked && !$(this).is(':checked')) {
                    if(!confirm('Are you sure you want to unlink this user account? This action cannot be undone.')) {
                        $(this).prop('checked', true);
                        return false;
                    }
                }
            });
        }
        
        // Form validation
        $('form').on('submit', function(e) {
            if($('#create_account').is(':checked') && $('#email').val() === '') {
                e.preventDefault();
                alert('Email is required for creating a user account.');
                
                // Switch to the contact tab where email field is located
                showTab(1);
                $('#email').focus();
                return false;
            }
            
            // If creating a new account (no user ID yet) and checkbox is checked,
            // ensure password is provided
            if($('#create_account').is(':checked') && 
               !`{{ $mitra->user_id ? 'true' : 'false' }}` && 
               $('#password').val() === '') {
                e.preventDefault();
                alert('Password is required for creating a new user account.');
                
                // Switch to the account tab
                showTab(5); // Menggunakan index 5 untuk account tab
                $('#password').focus();
                return false;
            }
            
            // Password confirmation match
            if($('#password').val() !== '' && 
               $('#password').val() !== $('#password_confirmation').val()) {
                e.preventDefault();
                alert('Password and confirmation do not match.');
                
                // Switch to the account tab
                showTab(5); // Menggunakan index 5 untuk account tab
                $('#password_confirmation').focus();
                return false;
            }
        });
        
        // Handle dark mode for Select2
        if (document.documentElement.classList.contains('dark')) {
            $('<style>').appendTo('head').text(`
                .select2-container--classic .select2-selection--single,
                .select2-container--classic .select2-selection--multiple {
                    background-color: #1e293b !important;
                    border-color: #475569 !important;
                    color: #e2e8f0 !important;
                }
                .select2-container--classic .select2-selection--single .select2-selection__rendered,
                .select2-container--classic .select2-selection--multiple .select2-selection__rendered {
                    color: #e2e8f0 !important;
                }
                .select2-container--classic .select2-dropdown {
                    background-color: #1e293b !important;
                    border-color: #475569 !important;
                }
                .select2-container--classic .select2-results__option {
                    color: #e2e8f0 !important;
                }
                .select2-container--classic .select2-results__option--highlighted.select2-results__option--selectable {
                    background-color: #0f172a !important;
                }
                .select2-container--classic .select2-selection--multiple .select2-selection__choice {
                    background-color: #334155 !important;
                    border-color: #475569 !important;
                    color: #e2e8f0 !important;
                }
            `);
        }
        
        // Warehouse delete functionality with SweetAlert2
        $(document).on('click', '.delete-warehouse-btn', function() {
            const warehouseId = $(this).data('warehouse-id');
            const mitraId = $(this).data('mitra-id');
            const warehouseName = $(this).data('warehouse-name');
            
            Swal.fire({
                title: 'Delete Warehouse?',
                html: `Are you sure you want to delete warehouse <strong>${warehouseName}</strong>?<br>This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#4f46e5",
                cancelButtonColor: "#d33",
                buttonsStyling:false,
                customClass: {
                    confirmButton: 'btn btn-primary mx-5',
                    cancelButton: 'btn btn-secondary'
                },
                confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/mitras/${mitraId}/warehouses/${warehouseId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Show success notification
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Warehouse has been deleted.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Remove warehouse row from table
                            setTimeout(function() {
                                const $row = $(`.delete-warehouse-btn[data-warehouse-id="${warehouseId}"]`).closest('tr');
                                $row.fadeOut(400, function() {
                                    $(this).remove();
                                    
                                    // If no more warehouses, show empty state
                                    if ($('#warehouse-content table tbody tr').length === 0) {
                                        $('#warehouse-content table').closest('.overflow-x-auto').replaceWith(`
                                            <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-md text-center">
                                                <div class="mb-3">
                                                    <i class="fas fa-warehouse text-4xl text-slate-400"></i>
                                                </div>
                                                <h4 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-1">No Warehouses Found</h4>
                                                <p class="text-slate-500 mb-4">This mitra doesn't have any warehouses yet.</p>
                                                <a href="{{ route('mitra.warehouses.create', $mitra->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-plus mr-1"></i> Add First Warehouse
                                                </a>
                                            </div>
                                        `);
                                    }
                                });
                            }, 300);
                        },
                        error: function(error) {
                            console.error('Error deleting warehouse:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to delete warehouse. Please try again.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
        
        // Show first tab on load
        showTab(0);
    });
</script>
@endpush
</x-app-layout>