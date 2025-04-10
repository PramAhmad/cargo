<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Add Marketing" page="Marketing" />
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
                
                <form action="{{ route('marketings.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Basic Information -->
                        <div class="col-span-1 md:col-span-2 lg:col-span-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Basic Information</h3>
                        </div>
                        
                        <!-- Code -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="code">Code</label>
                            <input type="text" class="input" id="code" name="code" value="{{ old('code') }}" />
                        </div>
                        
                        <!-- Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="name">Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name') }}" />
                        </div>
                        
                        <!-- Marketing Group -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="marketing_group_id">Marketing Group</label>
                            <select id="marketing_group_id" name="marketing_group_id" class="select">
                                <option value="">Select Marketing Group</option>
                                @foreach($marketingGroups as $group)
                                    <option value="{{ $group->id }}" {{ old('marketing_group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Status -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="status">Status</label>
                            <select id="status" name="status" class="select">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        
                        <!-- Due Date -->
                  
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Contact Information</h3>
                        </div>
                        
                        <!-- Address -->
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="label label-required mb-1 font-medium" for="address">Address</label>
                            <textarea class="textarea" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        </div>
                        
                        <!-- City -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="city">City</label>
                            <input type="text" class="input" id="city" name="city" value="{{ old('city') }}" />
                        </div>
                        
                        <!-- Phone 1 -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="phone1">Primary Phone</label>
                            <input type="text" class="input" id="phone1" name="phone1" value="{{ old('phone1') }}" />
                        </div>
                        
                        <!-- Phone 2 -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="phone2">Secondary Phone</label>
                            <input type="text" class="input" id="phone2" name="phone2" value="{{ old('phone2') }}" />
                        </div>
                        
                        <!-- Email -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="email">Email</label>
                            <input type="email" class="input" id="email" name="email" value="{{ old('email') }}" />
                        </div>
                        
                        <!-- Website -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="website">Website</label>
                            <input type="url" class="input" id="website" name="website" value="{{ old('website') }}" placeholder="https://" />
                        </div>
                        
                        <!-- Birth Date -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="borndate">Birth Date</label>
                            <input type="date" class="input" id="borndate" name="borndate" value="{{ old('borndate') }}" />
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">System Account</h3>
                        </div>
                        
                        <!-- Create User Account -->
                        <div class="col-span-1 md:col-span-3 lg:col-span-3">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="create_user" name="create_user" value="1" class="checkbox" {{ old('create_user') ? 'checked' : '' }} />
                                <label for="create_user" class="cursor-pointer font-medium text-slate-700 dark:text-slate-200">
                                    Create user account for this marketing contact
                                </label>
                            </div>
                        </div>
                        
                        <!-- User Account Password -->
                        <div id="user_account_fields" class="col-span-1 md:col-span-3 lg:col-span-3 {{ old('create_user') ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-6 border-l-4 border-primary-500 pl-4 py-2">
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
                                <p class="text-sm text-slate-500">
                                    <i class="inline-block h-4 w-4 mr-1 text-primary-500" data-feather="info"></i>
                                    Creating a user account will allow this marketing contact to log into the system. 
                                    They will be given a "Marketing" role with limited permissions.
                                </p>
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
                                    <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Account Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="atas_nama">Account Name</label>
                            <input type="text" class="input" id="atas_nama" name="atas_nama" value="{{ old('atas_nama') }}" />
                        </div>
                        
                        <!-- Account Number -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="no_rek">Account Number</label>
                            <input type="text" class="input" id="no_rek" name="no_rek" value="{{ old('no_rek') }}" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="due_date">Due Date (Days)</label>
                            <input type="number" class="input" id="due_date" name="due_date" value="{{ old('due_date') }}" />
                        </div>
                        
                        <!-- KTP -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="ktp">KTP</label>
                            <input type="text" class="input" id="ktp" name="ktp" value="{{ old('ktp') }}" />
                        </div>
                        
                        <!-- NPWP -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="npwp">NPWP</label>
                            <input type="text" class="input" id="npwp" name="npwp" value="{{ old('npwp') }}" />
                        </div>
                        
                        <!-- Address Tax -->
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="label mb-1 font-medium" for="address_tax">Tax Address</label>
                            <textarea class="textarea" id="address_tax" name="address_tax" rows="2">{{ old('address_tax') }}</textarea>
                        </div>
                        
                        <!-- Requirements -->
                        <div class="flex flex-col gap-1 col-span-1 md:col-span-2 lg:col-span-3">
                            <label class="label mb-1 font-medium" for="requirement">Additional Requirements</label>
                            <textarea class="textarea" id="requirement" name="requirement" rows="3">{{ old('requirement') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('marketings.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createUserCheckbox = document.getElementById('create_user');
            const userAccountFields = document.getElementById('user_account_fields');
            const emailField = document.getElementById('email');
            
            function toggleUserFields() {
                if (createUserCheckbox.checked) {
                    userAccountFields.classList.remove('hidden');
                    emailField.setAttribute('required', 'required');
                } else {
                    userAccountFields.classList.add('hidden');
                    emailField.removeAttribute('required');
                }
            }
            
            // Initial state
            toggleUserFields();
            
            // Listen for changes
            createUserCheckbox.addEventListener('change', toggleUserFields);
        });
    </script>
</x-app-layout>