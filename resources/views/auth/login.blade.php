<x-guest-layout>
    <!-- Session Status -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="card mx-auto w-full max-w-md">
            <div class="card-body px-10 py-12">
                <div class="flex flex-col items-center justify-center">
                    <x-application-logo class=" fill-current text-gray-500" />
                    <h5 class="mt-4">Welcome Back</h5>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Please enter your details</p>
                    <x-auth-session-status class="my-4" :status="session('status')" />
                </div>

                <div class="mt-6 flex flex-col gap-5">
                    <!-- Email -->
                    <div>
                        <label class="label mb-1">Email Or Username</label>
                        <input type="text" class="input" placeholder="Enter Your Email" name="email"
                            :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <!-- Password-->
                    <div class="">
                        <label class="label mb-1">Password</label>
                        <input type="password" class="input" placeholder="Password" type="password" name="password"
                            required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                </div>
                <!-- Remember & Forgot-->
                <div class="mt-2 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <input type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 bg-transparent text-primary-500 shadow-sm transition-all duration-150 checked:hover:shadow-none focus:ring-0 focus:ring-offset-0 enabled:hover:shadow disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-600"
                            id="remember-me" name="remember" />
                        <label for="remember-me" class="label">Remember Me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-sm text-primary-500 hover:underline">Forgot
                        Password</a>
                </div>
                <!-- Login Button -->
                <div class="mt-8">
                    <button class="btn btn-primary w-full py-2.5">{{ __('Log in') }}</button>
                
                </div>
                <!-- Don't Have An Account -->
               
            </div>
        </div>
    </form>
</x-guest-layout>
