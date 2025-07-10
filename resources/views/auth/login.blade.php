<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan email Anda"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password"/>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

       <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded bg-gray-900 border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-300">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm link-style rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="flex items-center justify-end mt-6">
            <a href="/" class="text-sm link-style hover:text-gray-400">Back to Home</a>
            
            {{-- Tombol Login dengan gaya baru --}}
            <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150" style="background-color: #E3EF26; color: #0C342C;">
                {{ __('Log In') }}
            </button>
        </div>
    </form>
</x-guest-layout>