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
                
                <form action="{{ route('customers.update', $customer->id) }}" method="POST">
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
                            <input type="text" class="input" id="code" name="code" value="{{ old('code', $customer->code) }}" />
                        </div>
                        
                        <!-- Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="name">Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name', $customer->name) }}" />
                        </div>
                        
                        <!-- Customer Type -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="type">Customer Type</label>
                            <select id="type" name="type" class="select">
                                <option value="individual" {{ old('type', $customer->type) == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="company" {{ old('type', $customer->type) == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="internal" {{ old('type', $customer->type) == 'internal' ? 'selected' : '' }}>Internal</option>
                            </select>
                        </div>
                        
                        <!-- Marketing -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="marketing_id">Marketing</label>
                            <select id="marketing_id" name="marketing_id" class="select">
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
                            <select id="customer_group_id" name="customer_group_id" class="select">
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
                            <select id="customer_category_id" name="customer_category_id" class="select">
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
                            <select id="status" name="status" class="select">
                                <option value="1" {{ old('status', $customer->status) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $customer->status) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        
                        <!-- Created Date -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="created_date">Registration Date</label>
                            <input type="date" class="input" id="created_date" name="created_date" value="{{ old('created_date', $customer->created_date ? $customer->created_date->format('Y-m-d') : '') }}" />
                        </div>
                  
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Contact Information</h3>
                        </div>
                        
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
                            <input type="text" class="input" id="phone1" name="phone1" value="{{ old('phone1', $customer->phone1) }}" />
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
                        
                        <!-- Birth Date -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="borndate">Birth Date</label>
                            <input type="date" class="input" id="borndate" name="borndate" value="{{ old('borndate', $customer->borndate ? $customer->borndate->format('Y-m-d') : '') }}" />
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">System Account</h3>
                        </div>
                        
                        <!-- User Account Section -->
                        <div class="col-span-1 md:col-span-3 lg:col-span-3">
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
                                <div class="col-span-1 md:col-span-3 lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6 border-l-4 border-primary-500 pl-4 py-2">
                                    <div class="col-span-1 md:col-span-2">
                                        <p class="text-sm text-primary-600 font-medium mb-3">
                                            <i class="inline-block h-4 w-4 mr-1" data-feather="alert-circle"></i>
                                            This customer doesn't have a user account. Please create one now by setting a password.
                                        </p>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1">
                                        <label class="label label-required mb-1 font-medium" for="password">Password</label>
                                        <input type="password" class="input" id="password" name="password" />
                                        <p class="text-xs text-slate-500 mt-1">Password must be at least 8 characters</p>
                                    </div>
                                    
                                    <div class="flex flex-col gap-1">
                                        <label class="label label-required mb-1 font-medium" for="password_confirmation">Confirm Password</label>
                                        <input type="password" class="input" id="password_confirmation" name="password_confirmation" />
                                    </div>
                                </div>
                            @endif
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
                                    <option value="{{ $bank->id }}" {{ old('bank_id', $customer->bank_id) == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Account Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="atas_nama">Account Name</label>
                            <input type="text" class="input" id="atas_nama" name="atas_nama" value="{{ old('atas_nama', $customer->atas_nama) }}" />
                        </div>
                        
                        <!-- Account Number -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="no_rek">Account Number</label>
                            <input type="text" class="input" id="no_rek" name="no_rek" value="{{ old('no_rek', $customer->no_rek) }}" />
                        </div>
                        
                        <!-- NPWP -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="npwp">NPWP</label>
                            <input type="text" class="input" id="npwp" name="npwp" value="{{ old('npwp', $customer->npwp) }}" />
                        </div>
                        
                        <!-- Tax Address -->
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="label mb-1 font-medium" for="tax_address">Tax Address</label>
                            <textarea class="textarea" id="tax_address" name="tax_address" rows="2">{{ old('tax_address', $customer->tax_address) }}</textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</x-app-layout>