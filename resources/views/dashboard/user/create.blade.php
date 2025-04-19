<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Create New User</h1>
            <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information Section -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Basic Information</h3>
                        </div>

                        <!-- Name -->
                        <div class="form-group">
                            <label for="name" class="label label-required">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="input @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="label label-required">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="input @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="label">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="input @error('phone') is-invalid @enderror">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status" class="label label-required">Status</label>
                            <select name="status" id="status" class="select @error('status') is-invalid @enderror">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
                                        {{ ucfirst(strtolower($status->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label for="gender" class="label">Gender</label>
                            <select name="gender" id="gender" class="select @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                @foreach($genders as $gender)
                                    <option value="{{ $gender->value }}" {{ old('gender') == $gender->value ? 'selected' : '' }}>
                                        {{ ucfirst(strtolower($gender->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Profile Image -->
                        <div class="form-group">
                            <label for="image" class="label">Profile Image</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-2xl"></i>
                                </div>
                                <div class="flex-grow">
                                    <input type="file" name="image" id="image" accept="image/*" class="file-input @error('image') is-invalid @enderror">
                                    <p class="text-xs text-gray-500 mt-1">Upload a profile image (JPEG, PNG, GIF - max 2MB)</p>
                                </div>
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Authentication Section -->
                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Authentication</h3>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="label label-required">Password</label>
                            <input type="password" name="password" id="password" class="input @error('password') is-invalid @enderror">
                            <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters</p>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-group">
                            <label for="password_confirmation" class="label label-required">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="input">
                        </div>

                        <!-- Roles Section -->
                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">User Roles</h3>
                        </div>

                        <!-- Roles -->
                        <div class="form-group md:col-span-2">
                            <label class="label label-required">Assign Roles</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                                @foreach($roles as $role)
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}" class="form-check-input"
                                            {{ is_array(old('roles')) && in_array($role->id, old('roles')) ? 'checked' : '' }}>
                                        <label for="role_{{ $role->id }}" class="form-check-label">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Localization Section -->
                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Localization</h3>
                        </div>

                        <!-- Language -->
                        <div class="form-group">
                            <label for="language" class="label">Language</label>
                            <select name="language" id="language" class="select @error('language') is-invalid @enderror">
                                <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="id" {{ old('language') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>French</option>
                                <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>German</option>
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Time Zone -->
                        <div class="form-group">
                            <label for="time_zone" class="label">Time Zone</label>
                            <select name="time_zone" id="time_zone" class="select @error('time_zone') is-invalid @enderror">
                                <option value="UTC" {{ old('time_zone', 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="Asia/Jakarta" {{ old('time_zone') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta</option>
                                <option value="America/New_York" {{ old('time_zone') == 'America/New_York' ? 'selected' : '' }}>America/New York</option>
                                <option value="Europe/London" {{ old('time_zone') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                <option value="Asia/Tokyo" {{ old('time_zone') == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo</option>
                            </select>
                            @error('time_zone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="button" onclick="history.back()" class="btn btn-secondary mr-2">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>