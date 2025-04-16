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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @if($mitra->bank)
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Bank</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $mitra->bank->name }}
                                </p>
                            </div>
                            @endif
                            
                            @if($mitra->atas_nama)
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Account Name</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $mitra->atas_nama }}
                                </p>
                            </div>
                            @endif
                            
                            @if($mitra->no_rek)
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Account Number</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $mitra->no_rek }}
                                </p>
                            </div>
                            @endif
                            
                            @if($mitra->npwp)
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">NPWP Number</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $mitra->npwp }}
                                </p>
                            </div>
                            @endif
                            
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
                
                <!-- Collaboration History Section -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="mr-2 h-5 w-5 text-primary-500" data-feather="bar-chart-2"></i>
                                <h4 class="card-title">Collaboration History</h4>
                            </div>
                            <a href="#" class="text-sm text-primary-500 hover:underline">View All Projects</a>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="flex items-center justify-center h-32 bg-slate-50 dark:bg-slate-800 rounded-md">
                            <p class="text-slate-500 dark:text-slate-400">No projects found with this mitra</p>
                        </div>
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
                
                <!-- Mitra Stats -->
                <div class="card bg-gradient-to-br from-blue-50 to-blue-100 dark:from-slate-800 dark:to-slate-700">
                    <div class="card-body p-6">
                        <h4 class="font-semibold text-lg text-blue-700 dark:text-blue-400 mb-4">Mitra Summary</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-4 shadow-sm">
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Projects</h5>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-500 mt-1">
                                    {{ number_format(rand(1, 20)) }}
                                </p>
                            </div>
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-4 shadow-sm">
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Active Projects</h5>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-500 mt-1">
                                    {{ rand(0, 5) }}
                                </p>
                            </div>
                        </div>
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
                
                @if($mitra->status)
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="zap"></i>
                            <h4 class="card-title">Quick Actions</h4>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="h-4 w-4 mr-1" data-feather="mail"></i>
                                <span>Email</span>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="h-4 w-4 mr-1" data-feather="phone"></i>
                                <span>Call</span>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="h-4 w-4 mr-1" data-feather="file-text"></i>
                                <span>New Project</span>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="h-4 w-4 mr-1" data-feather="message-circle"></i>
                                <span>Message</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>