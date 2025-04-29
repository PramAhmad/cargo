<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Edit Customer" page="Customer" />
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
                
                <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="customerFormTabs" role="tablist">
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
                            <li role="presentation">
                                <button class="inline-flex items-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="account-tab" data-tab="account-content" type="button" role="tab" aria-controls="account" aria-selected="false">
                                    <i class="fas fa-user-shield mr-2"></i>
                                    System Account
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Basic Information Tab -->
                        <div class="tab-pane active" id="basic-content" role="tabpanel" aria-labelledby="basic-tab">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Code -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="code">Code</label>
                                    <input type="text" class="input" id="code" name="code" value="{{ old('code', $customer->code) }}" />
                                </div>
                                
                                <!-- Name -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="name">Name</label>
                                    <input type="text" class="input" id="name" name="name" value="{{ old('name', $customer->name) }}" required />
                                </div>
                                
                                <!-- Customer Type -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="type">Customer Type</label>
                                    <select id="type" name="type" class="select" required>
                                        <option value="individual" {{ old('type', $customer->type) == 'individual' ? 'selected' : '' }}>Individual</option>
                                        <option value="company" {{ old('type', $customer->type) == 'company' ? 'selected' : '' }}>Company</option>
                                        <option value="internal" {{ old('type', $customer->type) == 'internal' ? 'selected' : '' }}>Internal</option>
                                    </select>
                                </div>
                                
                                <!-- Marketing -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="marketing_id">Marketing</label>
                                    <select id="marketing_id" name="marketing_id" class="select" required>
                                        <option value="">Select Marketing</option>
                                        @foreach($marketings as $marketing)
                                            <option value="{{ $marketing->id }}" {{ old('marketing_id', $customer->marketing_id) == $marketing->id ? 'selected' : '' }}>
                                                {{ $marketing->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Customer Group -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="customer_group_id">Customer Group</label>
                                    <select id="customer_group_id" name="customer_group_id" class="select" required>
                                        <option value="">Select Customer Group</option>
                                        @foreach($customerGroups as $group)
                                            <option value="{{ $group->id }}" {{ old('customer_group_id', $customer->customer_group_id) == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Customer Category -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="customer_category_id">Customer Category</label>
                                    <select id="customer_category_id" name="customer_category_id" class="select" required>
                                        <option value="">Select Customer Category</option>
                                        @foreach($customerCategories as $category)
                                            <option value="{{ $category->id }}" {{ old('customer_category_id', $customer->customer_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Status -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="status">Status</label>
                                    <select id="status" name="status" class="select" required>
                                        <option value="1" {{ old('status', $customer->status) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $customer->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                
                                <!-- Created Date -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="created_date">Registration Date</label>
                                    <input type="date" class="input" id="created_date" name="created_date" value="{{ old('created_date', $customer->created_date ? $customer->created_date->format('Y-m-d') : '') }}" />
                                </div>
                                
                                <!-- Birth Date -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="borndate">Birth Date</label>
                                    <input type="date" class="input" id="borndate" name="borndate" value="{{ old('borndate', $customer->borndate ? $customer->borndate->format('Y-m-d') : '') }}" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information Tab -->
                        <div class="tab-pane hidden" id="contact-content" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Street 1 -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="street1">Address Line 1</label>
                                    <input type="text" class="input" id="street1" name="street1" value="{{ old('street1', $customer->street1) }}" />
                                </div>
                                
                                <!-- Street 2 -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="street2">Address Line 2</label>
                                    <input type="text" class="input" id="street2" name="street2" value="{{ old('street2', $customer->street2) }}" />
                                </div>
                                
                                <!-- Street Item -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="street_item">Address Detail</label>
                                    <input type="text" class="input" id="street_item" name="street_item" value="{{ old('street_item', $customer->street_item) }}" />
                                </div>
                                
                                <!-- City -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="city">City</label>
                                    <input type="text" class="input" id="city" name="city" value="{{ old('city', $customer->city) }}" />
                                </div>
                                
                                <!-- Country -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="country">Country</label>
                                    <input type="text" class="input" id="country" name="country" value="{{ old('country', $customer->country ?? 'Indonesia') }}" />
                                </div>
                                
                                <!-- Phone 1 -->
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="phone1">Primary Phone</label>
                                    <input type="text" class="input" id="phone1" name="phone1" value="{{ old('phone1', $customer->phone1) }}" required />
                                </div>
                                
                                <!-- Phone 2 -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="phone2">Secondary Phone</label>
                                    <input type="text" class="input" id="phone2" name="phone2" value="{{ old('phone2', $customer->phone2) }}" />
                                </div>
                                
                                <!-- Email -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="email">Email</label>
                                    <input type="email" class="input" id="email" name="email" value="{{ old('email', $customer->email) }}" />
                                </div>
                                
                                <!-- Website -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="website">Website</label>
                                    <input type="url" class="input" id="website" name="website" value="{{ old('website', $customer->website) }}" placeholder="https://" />
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
                                    @if($customer->banks->count() > 0)
                                        @foreach($customer->banks as $index => $bankAccount)
                                            <div class="bank-account-record bg-slate-50 dark:bg-slate-800 p-4 rounded-md mb-4" data-record-id="{{ $index }}">
                                                <div class="flex justify-between items-center mb-3">
                                                    <h4 class="text-md font-medium text-slate-700 dark:text-slate-200">Bank Account #<span class="record-number">{{ $index + 1 }}</span></h4>
                                                    <div class="flex items-center">
                                                        <div class="flex items-center mr-4">
                                                            <input type="checkbox" id="is_default_{{ $index }}" name="bank_accounts[{{ $index }}][is_default]" value="1" class="default-bank-checkbox mr-2" {{ $bankAccount->is_default ? 'checked' : '' }}>
                                                            <label for="is_default_{{ $index }}" class="text-sm">Default Account</label>
                                                        </div>
                                                        <button type="button" class="remove-bank-account text-red-500 hover:text-red-700" {{ $customer->banks->count() <= 1 ? 'disabled' : '' }}>
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div class="flex flex-col gap-1">
                                                        <label class="label mb-1 font-medium" for="bank_id_{{ $index }}">Bank</label>
                                                        <input type="hidden" name="bank_accounts[{{ $index }}][id]" value="{{ $bankAccount->id }}">
                                                        <select id="bank_id_{{ $index }}" name="bank_accounts[{{ $index }}][bank_id]" class="select">
                                                            <option value="">Select Bank</option>
                                                            @foreach($banks as $bank)
                                                                <option value="{{ $bank->id }}" {{ old('bank_accounts.' . $index . '.bank_id', $bankAccount->bank_id) == $bank->id ? 'selected' : '' }}>
                                                                    {{ $bank->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="flex flex-col gap-1">
                                                        <label class="label mb-1 font-medium" for="rek_name_{{ $index }}">Account Name</label>
                                                        <input type="text" class="input" id="rek_name_{{ $index }}" name="bank_accounts[{{ $index }}][rek_name]" value="{{ old('bank_accounts.' . $index . '.rek_name', $bankAccount->rek_name) }}" />
                                                    </div>
                                                    
                                                    <div class="flex flex-col gap-1">
                                                        <label class="label mb-1 font-medium" for="rek_no_{{ $index }}">Account Number</label>
                                                        <input type="text" class="input" id="rek_no_{{ $index }}" name="bank_accounts[{{ $index }}][rek_no]" value="{{ old('bank_accounts.' . $index . '.rek_no', $bankAccount->rek_no) }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <!-- Default empty bank account if none exists -->
                                        <div class="bank-account-record bg-slate-50 dark:bg-slate-800 p-4 rounded-md mb-4" data-record-id="0">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="text-md font-medium text-slate-700 dark:text-slate-200">Bank Account #<span class="record-number">1</span></h4>
                                                <div class="flex items-center">
                                                    <div class="flex items-center mr-4">
                                                        <input type="checkbox" id="is_default_0" name="bank_accounts[0][is_default]" value="1" class="default-bank-checkbox mr-2" checked>
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
                                    @endif
                                </div>
                            </div>
                            
                            <!-- NPWP & Tax Address -->
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- NPWP -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="npwp">NPWP</label>
                                    <input type="text" class="input" id="npwp" name="npwp" value="{{ old('npwp', $customer->npwp) }}" />
                                </div>
                                
                                <!-- Tax Address -->
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="tax_address">Tax Address</label>
                                    <textarea class="textarea" id="tax_address" name="tax_address" rows="2">{{ old('tax_address', $customer->tax_address) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- System Account Tab -->
                        <div class="tab-pane hidden" id="account-content" role="tabpanel" aria-labelledby="account-tab">
                            <!-- User Account Section -->
                            <div>
                                @if($customer->user)
                                    <div class="flex items-center p-3 bg-primary-50 dark:bg-slate-800 rounded-md">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-500 text-white dark:bg-primary-600">
                                            <i class="h-5 w-5" data-feather="user"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-200">Linked User Account</h5>
                                            <p class="text-sm text-slate-500">{{ $customer->user->name }} ({{ $customer->user->email }})</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Password Update Section -->
                                    <div class="mt-4 border-t pt-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-200">Update Password</h5>
                                            <button type="button" id="togglePasswordUpdate" class="text-xs text-primary-600 hover:text-primary-800 focus:outline-none">
                                                <span id="passwordUpdateButtonText">Show Password Fields</span>
                                            </button>
                                        </div>
                                        
                                        <div id="passwordUpdateSection" class="hidden">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-l-4 border-primary-500 pl-4 py-2 mt-2">
                                                <div class="flex flex-col gap-1">
                                                    <label class="label mb-1 font-medium" for="password">New Password</label>
                                                    <input type="password" class="input" id="password" name="password" />
                                                    <p class="text-xs text-slate-500 mt-1">Password must be at least 8 characters</p>
                                                </div>
                                                
                                                <div class="flex flex-col gap-1">
                                                    <label class="label mb-1 font-medium" for="password_confirmation">Confirm New Password</label>
                                                    <input type="password" class="input" id="password_confirmation" name="password_confirmation" />
                                                </div>
                                                
                                                <div class="col-span-1 md:col-span-2">
                                                    <p class="text-sm text-amber-600">
                                                        <i class="inline-block h-4 w-4 mr-1" data-feather="alert-triangle"></i>
                                                        Leave blank if you don't want to change the password.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-4 p-3 bg-amber-50 dark:bg-slate-800 rounded-md mb-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500 text-white dark:bg-amber-600">
                                            <i class="h-5 w-5" data-feather="user-x"></i>
                                        </div>
                                        <div class="ml-2">
                                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-200">No User Account Linked</h5>
                                            <p class="text-sm text-slate-500">Create a user account to allow system access</p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-l-4 border-primary-500 pl-4 py-3">
                                        <div class="col-span-1 md:col-span-2">
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" id="create_user" name="create_user" value="1" class="checkbox" {{ old('create_user') ? 'checked' : '' }} />
                                                <label for="create_user" class="cursor-pointer font-medium text-slate-700 dark:text-slate-200">
                                                    Create user account for this customer
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div id="user_account_fields" class="col-span-1 md:col-span-2 {{ old('create_user') ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="flex flex-col gap-1">
                                                <label class="label mb-1 font-medium" for="password">Password</label>
                                                <input type="password" class="input" id="password" name="password" />
                                                <p class="text-xs text-slate-500 mt-1">Password must be at least 8 characters</p>
                                            </div>
                                            
                                            <div class="flex flex-col gap-1">
                                                <label class="label mb-1 font-medium" for="password_confirmation">Confirm Password</label>
                                                <input type="password" class="input" id="password_confirmation" name="password_confirmation" />
                                            </div>
                                            
                                            <div class="col-span-1 md:col-span-2">
                                                <p class="text-sm text-slate-600">
                                                    <i class="inline-block h-4 w-4 mr-1 text-primary-500" data-feather="info"></i>
                                                    A new user account will be created with the customer's email address.
                                                    Make sure the email field is filled with a valid email.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
                        <div>
                            <button type="button" id="prevTabButton" class="btn btn-secondary" disabled>
                                <i class="fas fa-arrow-left mr-1"></i> Previous
                            </button>
                            <button type="button" id="nextTabButton" class="btn btn-info ml-2">
                                Next <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary ml-2">Update Customer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .tab-pane.active {
            display: block;
        }
        .tab-pane.hidden {
            display: none;
        }
        #customerFormTabs button {
            color: #64748b;
        }
        #customerFormTabs button.active {
            color: rgb(139 92 246 / var(--tw-bg-opacity));
            border-color: rgb(139 92 246 / var(--tw-bg-opacity));
        }
        .dark #customerFormTabs button {
            color: #94a3b8;
        }
        .dark #customerFormTabs button.active {
            color: #60a5fa;
            border-color: #60a5fa;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
        
            
            // Tab functionality
            const tabs = ['basic-tab', 'contact-tab', 'financial-tab', 'account-tab'];
            const tabContents = ['basic-content', 'contact-content', 'financial-content', 'account-content'];
            let currentTabIndex = 0;
            
            function showTab(index) {
                // Hide all tabs first
                $('#customerFormTabs button').removeClass('active').attr('aria-selected', 'false');
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
            $('#customerFormTabs button').on('click', function() {
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
            $('#create_user').change(function() {
                if(this.checked) {
                    $('#user_account_fields').removeClass('hidden');
                    $('#password, #password_confirmation').prop('required', true);
                } else {
                    $('#user_account_fields').addClass('hidden');
                    $('#password, #password_confirmation').prop('required', false);
                }
            });
            
            // Toggle password update fields
            const toggleButton = document.getElementById('togglePasswordUpdate');
            const passwordSection = document.getElementById('passwordUpdateSection');
            const buttonText = document.getElementById('passwordUpdateButtonText');
            
            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    if (passwordSection.classList.contains('hidden')) {
                        passwordSection.classList.remove('hidden');
                        buttonText.textContent = 'Hide Password Fields';
                    } else {
                        passwordSection.classList.add('hidden');
                        buttonText.textContent = 'Show Password Fields';
                    }
                });
            }
            
            // Bank account section
            let bankCounter = {{ count(old('bank_accounts', $customer->banks ?? [0])) - 1 }};
            
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
                        const newId = originalId.replace(/_\d+$/g, `_${bankCounter}`);
                        $(this).attr('id', newId);
                    }
                    
                    // Clear values except for hidden fields
                    if ($(this).attr('type') !== 'hidden') {
                        $(this).val('');
                    }
                    
                    // If it's a checkbox
                    if ($(this).attr('type') === 'checkbox') {
                        $(this).prop('checked', false);
                    }
                    
                    // Remove any ID field from cloned records
                    if ($(this).attr('name') && $(this).attr('name').includes('[id]')) {
                        $(this).remove();
                    }
                });
                
                // Update labels
                $template.find('label').each(function() {
                    const originalFor = $(this).attr('for');
                    if (originalFor) {
                        const newFor = originalFor.replace(/_\d+$/g, `_${bankCounter}`);
                        $(this).attr('for', newFor);
                    }
                });
                
                // Enable delete button
                $template.find('.remove-bank-account').prop('disabled', false);
                
                // Reinitialize select2 for the new elements
            
                
                // Add to container
                $('#bankAccountsContainer').append($template);
                
                // Default bank logic - uncheck others if this is checked
                updateDefaultBankLogic();
            });
            
            // Remove bank account
            $(document).on('click', '.remove-bank-account', function() {
                if ($('.bank-account-record').length > 1) {
                    // Get the record being removed
                    const $record = $(this).closest('.bank-account-record');
                    
                    // Check if the record being deleted has an ID
                    const recordId = $record.find('input[name$="[id]"]').val();
                    if (recordId) {
                        // Add to deleted_bank_accounts array if it has an ID
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'deleted_bank_accounts[]',
                            value: recordId
                        }).appendTo('form');
                    }
                    
                    // Remove the record from the DOM
                    $record.remove();
                    
                    // Re-number visible records
                    $('.bank-account-record').each(function(index) {
                        $(this).find('.record-number').text(index + 1);
                    });
                    
                    // Ensure at least one record has default checked if all are unchecked
                    const hasDefault = $('.default-bank-checkbox:checked').length > 0;
                    if (!hasDefault && $('.bank-account-record').length > 0) {
                        $('.bank-account-record:first-child').find('.default-bank-checkbox').prop('checked', true);
                    }
                }
            });
            
            // Default bank logic
            function updateDefaultBankLogic() {
                $(document).on('change', '.default-bank-checkbox', function() {
                    if ($(this).prop('checked')) {
                        // Uncheck all other default checkboxes
                        $('.default-bank-checkbox').not(this).prop('checked', false);
                    } else {
                        // If unchecking the only checked checkbox, prevent that
                        const checkedCount = $('.default-bank-checkbox:checked').length;
                        if (checkedCount === 0) {
                            $(this).prop('checked', true);
                        }
                    }
                });
            }
            
            // Call it initially
            updateDefaultBankLogic();
            
            // Enable/disable delete button based on number of bank accounts
            function updateRemoveButtons() {
                if ($('.bank-account-record').length <= 1) {
                    $('.remove-bank-account').prop('disabled', true);
                } else {
                    $('.remove-bank-account').prop('disabled', false);
                }
            }
            
            // Call initially
            updateRemoveButtons();
            
            // Handle dark mode for Select2
            if (document.documentElement.classList.contains('dark')) {
                $('<style>').appendTo('head').text(`
                    .select2-container--default .select2-selection--single {
                        background-color: #1e293b !important;
                        border-color: #475569 !important;
                        color: #e2e8f0 !important;
                    }
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        color: #e2e8f0 !important;
                    }
                    .select2-dropdown {
                        background-color: #1e293b !important;
                        border-color: #475569 !important;
                    }
                    .select2-search__field {
                        background-color: #1e293b !important;
                        color: #e2e8f0 !important;
                    }
                    .select2-container--default .select2-results__option {
                        color: #e2e8f0 !important;
                    }
                    .select2-container--default .select2-results__option--highlighted[aria-selected] {
                        background-color: #0f172a !important;
                    }
                `);
            }
            
            // Show first tab on load
            showTab(0);
        });
    </script>
    @endpush
</x-app-layout>