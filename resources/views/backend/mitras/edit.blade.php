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
                
                <form action="{{ route('mitras.update', $mitra->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Basic Information -->
                        <div class="col-span-1 md:col-span-2 lg:col-span-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Basic Information</h3>
                        </div>
                        
                        <!-- Code -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="code">Code</label>
                            <input type="text" class="input bg-slate-100 dark:bg-slate-800" id="code" value="{{ $mitra->code }}" readonly />
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
                            <input type="text" class="input" id="ktp" name="ktp" value="{{ old('ktp', $mitra->ktp) }}" />
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
                  
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Contact Information</h3>
                        </div>
                        
                        <!-- Office Address -->
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="label mb-1 font-medium" for="address_office_indo">Office Address</label>
                            <textarea class="textarea" id="address_office_indo" name="address_office_indo" rows="2">{{ old('address_office_indo', $mitra->address_office_indo) }}</textarea>
                        </div>
                        
                        <!-- Country -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="country">Country</label>
                            <select id="country" name="country[]" class="select2" multiple>
                                @php 
                                    $selectedCountries = old('country', $mitra->countries ? $mitra->countries->pluck('name')->toArray() : []);
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
                        
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">System Account</h3>
                        </div>
                        
                        <!-- User Account Checkbox -->
                        <div class="col-span-1 md:col-span-3 lg:col-span-3">
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
                        <div id="account_section" class="col-span-1 md:col-span-3 lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6 border-l-4 border-primary-500 pl-4 py-2 {{ old('create_account', $mitra->user_id ? true : false) ? '' : 'hidden' }}">
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
                                <input type="password" class="input" id="password" name="password" {{ $mitra->user_id ? '' : 'required' }} />
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $mitra->user_id ? 'Leave blank to keep current password' : 'Password must be at least 8 characters' }}
                                </p>
                            </div>
                            
                            <div class="flex flex-col gap-1">
                                <label class="label {{ $mitra->user_id ? '' : 'label-required' }} mb-1 font-medium" for="password_confirmation">
                                    Confirm {{ $mitra->user_id ? 'New ' : '' }}Password
                                </label>
                                <input type="password" class="input" id="password_confirmation" name="password_confirmation" {{ $mitra->user_id ? '' : 'required' }} />
                            </div>
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Financial Information</h3>
                        </div>
                        
                        <!-- Bank -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="bank_id">Bank</label>
                            <select id="bank_id" name="bank_id" class="select">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('bank_id', $mitra->bank_id) == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Account Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="atas_nama">Account Name</label>
                            <input type="text" class="input" id="atas_nama" name="atas_nama" value="{{ old('atas_nama', $mitra->atas_nama) }}" />
                        </div>
                        
                        <!-- Account Number -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="no_rek">Account Number</label>
                            <input type="text" class="input" id="no_rek" name="no_rek" value="{{ old('no_rek', $mitra->no_rek) }}" />
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
                    
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('mitras.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Mitra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
 
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            
            // Toggle password fields based on checkbox
            $('#create_account').change(function() {
                if(this.checked) {
                    $('#account_section').removeClass('hidden');
                    // Only make passwords required if no user account exists yet
                    if(!{{ $mitra->user_id ? 'true' : 'false' }}) {
                        $('#password, #password_confirmation').prop('required', true);
                    }
                    
                    // If email is empty, focus on it
                    if($('#email').val() === '') {
                        $('#email').focus();
                    }
                } else {
                    $('#account_section').addClass('hidden');
                    $('#password, #password_confirmation').prop('required', false);
                }
            });
            
            // Handle dark mode for Select2
            if (document.documentElement.classList.contains('dark')) {
                $('<style>').appendTo('head').text(`
                    .select2-container--classic .select2-selection--single,
                    .select2-container--classic .select2-selection--multiple {
                        background-color: #1e293b !important;
                        border-color: #475569 !important;
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
            
            // Show warning if email is empty but user account is checked
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
            
            // Form validation
            $('form').on('submit', function(e) {
                if($('#create_account').is(':checked') && $('#email').val() === '') {
                    e.preventDefault();
                    alert('Email is required for creating a user account.');
                    $('#email').focus();
                    return false;
                }
                
                // If creating a new account (no user ID yet) and checkbox is checked,
                // ensure password is provided
                if($('#create_account').is(':checked') && 
                   !{{ $mitra->user_id ? 'true' : 'false' }} && 
                   $('#password').val() === '') {
                    e.preventDefault();
                    alert('Password is required for creating a new user account.');
                    $('#password').focus();
                    return false;
                }
                
                // Password confirmation match
                if($('#password').val() !== '' && 
                   $('#password').val() !== $('#password_confirmation').val()) {
                    e.preventDefault();
                    alert('Password and confirmation do not match.');
                    $('#password_confirmation').focus();
                    return false;
                }
            });
            
            // If displaying an existing mitra with user account, prevent accidentally
            // disabling the account without explicit confirmation
            if({{ $mitra->user_id ? 'true' : 'false' }}) {
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
        });
    </script>
    @endpush
</x-app-layout>