<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Edit Marketing" page="Marketing" />
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
                
                <form action="{{ route('marketings.update', $marketing->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Basic Information -->
                        <div class="col-span-1 md:col-span-2 lg:col-span-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Basic Information</h3>
                        </div>
                        
                        <!-- Code -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="code">Code</label>
                            <input type="text" class="input" id="code" name="code" value="{{ old('code', $marketing->code) }}" />
                        </div>
                        
                        <!-- Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="name">Name</label>
                            <input type="text" class="input" id="name" name="name" value="{{ old('name', $marketing->name) }}" />
                        </div>
                        
                        <!-- Marketing Group -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="marketing_group_id">Marketing Group</label>
                            <select id="marketing_group_id" name="marketing_group_id" class="select">
                                <option value="">Select Marketing Group</option>
                                @foreach($marketingGroups as $group)
                                    <option value="{{ $group->id }}" {{ old('marketing_group_id', $marketing->marketing_group_id) == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Status -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="status">Status</label>
                            <select id="status" name="status" class="select">
                                <option value="1" {{ old('status', $marketing->status) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $marketing->status) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        
                        <!-- Due Date -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="due_date">Due Date (Days)</label>
                            <input type="number" class="input" id="due_date" name="due_date" value="{{ old('due_date', $marketing->due_date) }}" />
                        </div>
                  
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Contact Information</h3>
                        </div>
                        
                        <!-- Address -->
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="label label-required mb-1 font-medium" for="address">Address</label>
                            <textarea class="textarea" id="address" name="address" rows="3">{{ old('address', $marketing->address) }}</textarea>
                        </div>
                        
                        <!-- City -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="city">City</label>
                            <input type="text" class="input" id="city" name="city" value="{{ old('city', $marketing->city) }}" />
                        </div>
                        
                        <!-- Phone 1 -->
                        <div class="flex flex-col gap-1">
                            <label class="label label-required mb-1 font-medium" for="phone1">Primary Phone</label>
                            <input type="text" class="input" id="phone1" name="phone1" value="{{ old('phone1', $marketing->phone1) }}" />
                        </div>
                        
                        <!-- Phone 2 -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="phone2">Secondary Phone</label>
                            <input type="text" class="input" id="phone2" name="phone2" value="{{ old('phone2', $marketing->phone2) }}" />
                        </div>
                        
                        <!-- Email -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="email">Email</label>
                            <input type="email" class="input" id="email" name="email" value="{{ old('email', $marketing->email) }}" />
                        </div>
                        
                        <!-- Website -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="website">Website</label>
                            <input type="url" class="input" id="website" name="website" value="{{ old('website', $marketing->website) }}" placeholder="https://" />
                        </div>
                        
                        <!-- Birth Date -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="borndate">Birth Date</label>
                            <input type="date" class="input" id="borndate" name="borndate" value="{{ old('borndate', $marketing->borndate) }}" />
                        </div>
                        
                        <!-- System Account Section -->
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">System Account</h3>
                        </div>
                        
                        <!-- User account information -->
                        @if($marketing->user)
                        <div class="col-span-1 md:col-span-3 lg:col-span-3">
                            <div class="flex items-center p-3 bg-primary-50 dark:bg-slate-800 rounded-md">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-500 text-white dark:bg-primary-600">
                                    <i class="h-5 w-5" data-feather="user"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-base font-medium text-slate-700 dark:text-slate-200">Linked User Account</h5>
                                    <p class="text-sm text-slate-500">{{ $marketing->user->name }} ({{ $marketing->user->email }})</p>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-span-1 md:col-span-3 lg:col-span-3">
                            <div class="flex items-center p-3 bg-slate-50 dark:bg-slate-800 rounded-md">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                    <i class="h-5 w-5" data-feather="user-x"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-base font-medium text-slate-700 dark:text-slate-200">No Linked User Account</h5>
                                    <p class="text-sm text-slate-500">This marketing contact doesn't have a system user account.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-3">
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-3">Financial Information</h3>
                        </div>
                        
                        <!-- Bank -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="bank_id">Bank</label>
                            <select id="bank_id" name="bank_id" class="select">
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('bank_id', $marketing->bank_id) == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Account Name -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="atas_nama">Account Name</label>
                            <input type="text" class="input" id="atas_nama" name="atas_nama" value="{{ old('atas_nama', $marketing->atas_nama) }}" />
                        </div>
                        
                        <!-- Account Number -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="no_rek">Account Number</label>
                            <input type="text" class="input" id="no_rek" name="no_rek" value="{{ old('no_rek', $marketing->no_rek) }}" />
                        </div>
                        
                        <!-- KTP -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="ktp">KTP Number</label>
                            <input type="text" class="input" id="ktp" name="ktp" value="{{ old('ktp', $marketing->ktp) }}" />
                        </div>
                        
                        <!-- NPWP -->
                        <div class="flex flex-col gap-1">
                            <label class="label mb-1 font-medium" for="npwp">NPWP Number</label>
                            <input type="text" class="input" id="npwp" name="npwp" value="{{ old('npwp', $marketing->npwp) }}" />
                        </div>
                        
                        <!-- Address Tax -->
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="label mb-1 font-medium" for="address_tax">Tax Address</label>
                            <textarea class="textarea" id="address_tax" name="address_tax" rows="2">{{ old('address_tax', $marketing->address_tax) }}</textarea>
                        </div>
                        
                        <!-- Requirements -->
                        <div class="flex flex-col gap-1 col-span-1 md:col-span-2 lg:col-span-3">
                            <label class="label mb-1 font-medium" for="requirement">Additional Requirements</label>
                            <textarea class="textarea" id="requirement" name="requirement" rows="3">{{ old('requirement', $marketing->requirement) }}</textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('marketings.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>