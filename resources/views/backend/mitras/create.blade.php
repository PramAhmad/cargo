<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Create Mitra" page="Mitra" />
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
                
                <form action="{{ route('mitras.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
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
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center justify-between">
                                        <label class="label label-required mb-1 font-medium" for="name">Name</label>
                                    </div>
                                    <input type="text" class="input" id="name" name="name" value="{{ old('name') }}" required />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center justify-between">
                                        <label class="label mb-1 font-medium" for="code">Marking Code</label>
                                        <button type="button" id="generateCode" class="text-xs text-primary-600 hover:text-primary-700">
                                            Generate Code
                                        </button>
                                    </div>
                                    <input type="text" class="input" id="code" name="code" value="{{ old('code') }}" />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="mitra_group_id">Mitra Group</label>
                                    <select id="mitra_group_id" name="mitra_group_id" class="select" required>
                                        <option value="">Select Mitra Group</option>
                                        @foreach($mitraGroups as $group)
                                            <option value="{{ $group->id }}" {{ old('mitra_group_id') == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="status">Status</label>
                                    <select id="status" name="status" class="select" required>
                                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="created_date">Registration Date</label>
                                    <input type="date" class="input" id="created_date" name="created_date" value="{{ old('created_date') }}" />
                                </div>
                        
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="birthdate">Birth Date</label>
                                    <input type="date" class="input" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="ktp">KTP</label>
                                    <input type="file" class="input" id="ktp" name="ktp" value="{{ old('ktp') }}" />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="syarat_bayar">Payment Terms (Days)</label>
                                    <input type="number" class="input" id="syarat_bayar" name="syarat_bayar" value="{{ old('syarat_bayar', 0) }}" min="0" />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="batas_tempo">Due Date Terms (Days)</label>
                                    <input type="number" class="input" id="batas_tempo" name="batas_tempo" value="{{ old('batas_tempo', 0) }}" min="0" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information Tab -->
                        <div class="tab-pane hidden" id="contact-content" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div class="flex flex-col gap-1 md:col-span-2">
                                    <label class="label mb-1 font-medium" for="address_office_indo">Office Address</label>
                                    <textarea class="textarea" id="address_office_indo" name="address_office_indo" rows="2">{{ old('address_office_indo') }}</textarea>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="country">Country</label>
                                    <select id="country" name="country[]" class="tom-select" multiple="">
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" 
                                                {{ is_array(old('country')) && in_array($country, old('country')) ? 'selected' : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="phone1">Primary Phone</label>
                                    <input type="text" class="input" id="phone1" name="phone1" value="{{ old('phone1') }}" required />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="phone2">Secondary Phone</label>
                                    <input type="text" class="input" id="phone2" name="phone2" value="{{ old('phone2') }}" />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="email">Email</label>
                                    <input type="email" class="input" id="email" name="email" value="{{ old('email') }}" />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="website">Website</label>
                                    <input type="url" class="input" id="website" name="website" value="{{ old('website') }}" placeholder="https://" />
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label mb-1 font-medium" for="npwp">NPWP</label>
                                    <input type="text" class="input" id="npwp" name="npwp" value="{{ old('npwp') }}" />
                                </div>
                                
                                <div class="flex flex-col gap-1 md:col-span-2">
                                    <label class="label mb-1 font-medium" for="tax_address">Tax Address</label>
                                    <textarea class="textarea" id="tax_address" name="tax_address" rows="2">{{ old('tax_address') }}</textarea>
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
                                    <!-- Initial bank account record (will be cloned) -->
                                    <div class="bank-account-record bg-slate-50 dark:bg-slate-800 p-4 rounded-md mb-4" data-record-id="0">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-md font-medium text-slate-700 dark:text-slate-200">Bank Account #<span class="record-number">1</span></h4>
                                            <div class="flex items-center">
                                                <div class="flex items-center mr-4">
                                                    <input type="checkbox" id="is_default_0" name="bank_accounts[0][is_default]" value="1" class="default-bank-checkbox mr-2" {{ old('bank_accounts.0.is_default') ? 'checked' : '' }}>
                                                    <label for="is_default_0" class="text-sm">Default Account</label>
                                                </div>
                                                <button type="button" class="remove-bank-account text-red-500 hover:text-red-700" {{ isset(old('bank_accounts')[0]) ? '' : 'disabled' }}>
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
                                                        <option value="{{ $bank->id }}" {{ old('bank_accounts.0.bank_id') == $bank->id ? 'selected' : '' }}>
                                                            {{ $bank->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="flex flex-col gap-1">
                                                <label class="label mb-1 font-medium" for="rek_name_0">Account Name</label>
                                                <input type="text" class="input" id="rek_name_0" name="bank_accounts[0][rek_name]" value="{{ old('bank_accounts.0.rek_name') }}" />
                                            </div>
                                            
                                            <div class="flex flex-col gap-1">
                                                <label class="label mb-1 font-medium" for="rek_no_0">Account Number</label>
                                                <input type="text" class="input" id="rek_no_0" name="bank_accounts[0][rek_no]" value="{{ old('bank_accounts.0.rek_no') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- System Account Tab -->
                        <div class="tab-pane hidden" id="account-content" role="tabpanel" aria-labelledby="account-tab">
                            <!-- User Account Checkbox -->
                            <div class="mb-6">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input id="create_account" name="create_account" type="checkbox" class="checkbox" 
                                               {{ old('create_account') ? 'checked' : '' }} />
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="create_account" class="font-medium text-primary-600 cursor-pointer">
                                            Create user account for this mitra
                                        </label>
                                        <p class="text-slate-500">This will allow the mitra to log in to the system</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Account Section -->
                            <div id="account_section" class="grid grid-cols-1 md:grid-cols-2 gap-6 border-l-4 border-primary-500 pl-4 py-4 {{ old('create_account') ? '' : 'hidden' }}">
                                <div class="col-span-1 md:col-span-2">
                                    <p class="text-sm text-primary-600 font-medium mb-3">
                                        <i class="inline-block h-4 w-4 mr-1" data-feather="alert-circle"></i>
                                        Create a user account for this mitra using the provided email.
                                    </p>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="password">Password</label>
                                    <input type="password" class="input" id="password" name="password" {{ old('create_account') ? 'required' : '' }} />
                                    <p class="text-xs text-slate-500 mt-1">Password must be at least 8 characters</p>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="label label-required mb-1 font-medium" for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="input" id="password_confirmation" name="password_confirmation" {{ old('create_account') ? 'required' : '' }} />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-between  pt-4">
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
                            <button type="submit" class="btn btn-primary ml-2">Create Mitra</button>
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
    </style>
    @endpush
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            const tabs = ['basic-tab', 'contact-tab', 'financial-tab', 'account-tab'];
            const tabContents = ['basic-content', 'contact-content', 'financial-content', 'account-content'];
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
                    $('#password, #password_confirmation').prop('required', true);
                } else {
                    $('#account_section').addClass('hidden');
                    $('#password, #password_confirmation').prop('required', false);
                }
            });
            
            // Bank account section
            let bankCounter = {{ count(old('bank_accounts', [0])) }};
            
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
                    const newName = originalName.replace(/\[0\]/g, `[${bankCounter}]`);
                    
                    const originalId = $(this).attr('id');
                    const newId = originalId.replace(/_0$/, `_${bankCounter}`);
                    
                    $(this).attr('name', newName);
                    $(this).attr('id', newId);
                    $(this).val(''); // Clear values
                    
                    // If it's a checkbox
                    if ($(this).attr('type') === 'checkbox') {
                        $(this).prop('checked', false);
                    }
                });
                
                // Update labels
                $template.find('label').each(function() {
                    const originalFor = $(this).attr('for');
                    if (originalFor) {
                        const newFor = originalFor.replace(/_0$/, `_${bankCounter}`);
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
                    $(this).closest('.bank-account-record').remove();
                    
                    // Re-number visible records
                    $('.bank-account-record').each(function(index) {
                        $(this).find('.record-number').text(index + 1);
                    });
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
            
            // Generate code button
            $('#generateCode').on('click', function() {
                // Generate a random code
                const randomCode = 'MT' + Math.floor(100000 + Math.random() * 900000);
                $('#code').val(randomCode);
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
                `);
            }
            
            // Show first tab on load
            showTab(0);
        });
    </script>
    @endpush
</x-app-layout>