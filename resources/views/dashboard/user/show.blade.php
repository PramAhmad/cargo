<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center gap-x-4">
                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 bg-primary-50 dark:bg-slate-800">
                    @if($user->images)
                        <img src="{{ $user->images }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-primary-100 text-primary-500 dark:bg-slate-700 dark:text-primary-400">
                            <i class="fas fa-user text-2xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $user->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @foreach($user->roles as $role)
                            <span class="badge badge-soft-indigo">{{ $role->name }}</span>
                        @endforeach
                        <span class="badge {{ $user->status->value === 1 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                            {{ ucfirst(strtolower($user->status->name)) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i> Edit User
                </a>
                <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Users
                </a>
                @if($user->isDeletable)
                    <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt mr-2"></i> Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2">
                <!-- Basic Information Card -->
                <div class="card mb-6">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                            <h2 class="card-title">Basic Information</h2>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Full Name</h3>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Email Address</h3>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                    <a href="mailto:{{ $user->email }}" class="text-primary-500 hover:underline">
                                        {{ $user->email }}
                                    </a>
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Phone Number</h3>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                    {{ $user->phone ?? 'Not provided' }}
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Gender</h3>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                    {{ $user->gender ? ucfirst(strtolower($user->gender->name)) : 'Not specified' }}
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Status</h3>
                                <p class="mt-1">
                                    <span class="badge {{ $user->status->value === 1 ? 'badge-success' : 'badge-danger' }}">
                                        {{ ucfirst(strtolower($user->status->name)) }}
                                    </span>
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Last Login</h3>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Roles & Permissions Card -->
                <div class="card mb-6">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-primary-500"></i>
                            <h2 class="card-title">Roles & Permissions</h2>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <!-- Roles Section -->
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-3">Assigned Roles</h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($user->roles as $role)
                                    <div class="px-4 py-2 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                        <span class="font-medium">{{ $role->name }}</span>
                                    </div>
                                @empty
                                    <p class="text-slate-500 dark:text-slate-400">No roles assigned</p>
                                @endforelse
                            </div>
                        </div>
                        
                        <!-- Permissions Section -->
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-3">Permissions (via Roles)</h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($user->getAllPermissions() as $permission)
                                    <span class="px-3 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <p class="text-slate-500 dark:text-slate-400">No permissions assigned</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity Card -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-history mr-2 text-primary-500"></i>
                                <h2 class="card-title">Recent Activity</h2>
                            </div>
                            <a href="#" class="text-sm text-primary-500 hover:text-primary-600 dark:hover:text-primary-400">
                                View All Activity
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        @if(method_exists($user, 'activities') && $user->activities()->count() > 0)
                            <div class="space-y-4">
                                @foreach($user->activities()->latest()->take(5)->get() as $activity)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                                <i class="fas fa-{{ $activity->log_name == 'login' ? 'sign-in-alt' : 'edit' }} text-slate-500"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow">
                                            <p class="text-sm text-slate-900 dark:text-slate-100">
                                                {{ $activity->description }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-6">
                                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                                    <i class="fas fa-history text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 dark:text-slate-400">No recent activity recorded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div>
                <!-- User Account Card -->
                <div class="card mb-6">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="fas fa-user-circle mr-2 text-primary-500"></i>
                            <h2 class="card-title">Account Information</h2>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="fas fa-id-card text-primary-500"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">User ID</h3>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                        {{ $user->id }}
                                    </p>
                                </div>
                            </li>
                            
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="fas fa-calendar-alt text-primary-500"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Joined Date</h3>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $user->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </li>
                            
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="fas fa-check-circle text-primary-500"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Email Verification</h3>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600 dark:text-green-500 flex items-center">
                                                <i class="fas fa-check-circle mr-1"></i> Verified 
                                                <span class="text-xs text-slate-500 dark:text-slate-400 ml-2">
                                                    ({{ $user->email_verified_at->format('M d, Y') }})
                                                </span>
                                            </span>
                                        @else
                                            <span class="text-amber-600 dark:text-amber-500 flex items-center">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Not verified
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Localization Card -->
                <div class="card mb-6">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="fas fa-globe mr-2 text-primary-500"></i>
                            <h2 class="card-title">Localization</h2>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="fas fa-language text-primary-500"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Language</h3>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                        @switch($user->language)
                                            @case('en')
                                                <span class="flex items-center">
                                                    <span class="flag-icon flag-icon-us mr-2"></span>
                                                    English
                                                </span>
                                                @break
                                            @case('id')
                                                <span class="flex items-center">
                                                    <span class="flag-icon flag-icon-id mr-2"></span>
                                                    Bahasa Indonesia
                                                </span>
                                                @break
                                            @case('es')
                                                <span class="flex items-center">
                                                    <span class="flag-icon flag-icon-es mr-2"></span>
                                                    Spanish
                                                </span>
                                                @break
                                            @default
                                                <span class="flex items-center">
                                                    <span class="flag-icon flag-icon-us mr-2"></span>
                                                    English (Default)
                                                </span>
                                        @endswitch
                                    </p>
                                </div>
                            </li>
                            
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="fas fa-clock text-primary-500"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Time Zone</h3>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-100">
                                        {{ $user->time_zone ?? 'UTC (Default)' }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Two-Factor Authentication Card -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="fas fa-lock mr-2 text-primary-500"></i>
                            <h2 class="card-title">Security</h2>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <!-- 2FA Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="fas fa-shield-alt text-primary-500"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-slate-900 dark:text-slate-100">Two-Factor Authentication</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        Adds an additional layer of security to your account
                                    </p>
                                </div>
                            </div>
                            
                            @if(isset($user->two_factor_confirmed_at) && $user->two_factor_confirmed_at)
                                <span class="badge badge-success">Enabled</span>
                            @else
                                <span class="badge badge-danger">Disabled</span>
                            @endif
                        </div>
                        
                        <!-- Password Reset -->
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('admin.user.edit', $user->id) }}#password" class="btn btn-secondary btn-sm w-full">
                                <i class="fas fa-key mr-2"></i> Reset Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>