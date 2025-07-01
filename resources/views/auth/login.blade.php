<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-text-input id="email" class="block w-full" type="email" name="email"
                :value="old('email')" required autofocus placeholder="Adresse e-mail ou numéro de tél." />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-text-input id="password" class="block w-full" type="password" name="password"
                required placeholder="Mot de passe" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit"
                class="w-full bg-blue-600  text-white font-bold py-2 px-4 rounded-lg"  style="background-color: #1976D2;"
                 onmouseover="this.style.backgroundColor='#1565c0';"
        onmouseout="this.style.backgroundColor='#1976D2';">
                Se connecter
            </button>
        </div>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
            <div class="text-center mt-2">
                <a href="{{ route('password.request') }}"
                    class="text-sm text-blue-600 hover:underline">Mot de passe oublié ?</a>
            </div>
        @endif
    </form>

    <!-- Divider -->
    <hr class="my-6 border-gray-300">

    <!-- Register Link -->
    <div class="text-center mt-4">
        <a href="{{ route('register') }}"
            class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
            Créer un nouveau compte
        </a>
    </div>
</x-guest-layout>
