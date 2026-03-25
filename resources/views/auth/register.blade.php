@extends('auth.template')

@section('title', 'Inscription — ' . config('app.name'))

@section('eyebrow')
    <div class="anim-fade-up flex items-center gap-2 mb-4">
        <div class="h-px w-8 rounded-full bg-primary"></div>
        <span class="text-xs font-semibold uppercase tracking-widest text-primary">Nouveau compte</span>
    </div>
@endsection

@section('header')Créer un compte.@endsection

@section('description', 'Remplissez le formulaire ci-dessous pour commencer.')

@section('content')
    {{-- Google --}}
    <div class="anim-fade-up mb-1.5">
        <a href="{{ route('login.google') }}"
            class="group w-full flex items-center justify-center gap-3 rounded-xl border border-gray-200 dark:border-white/10
                   bg-white dark:bg-white/5 px-4 py-3.5
                   text-sm font-semibold text-gray-700 dark:text-gray-300
                   transition-all duration-200
                   hover:border-gray-300 dark:hover:border-white/20 hover:bg-gray-50 dark:hover:bg-white/10
                   hover:-translate-y-0.5 hover:shadow-md">
            <svg class="size-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                    fill="#4285F4" />
                <path
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                    fill="#34A853" />
                <path
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                    fill="#FBBC05" />
                <path
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                    fill="#EA4335" />
            </svg>
            <span>Continuer avec Google</span>
        </a>
    </div>

    {{-- Divider --}}
    <div class="anim-fade-up-1 relative flex items-center gap-3">
        <div class="flex-1 h-px bg-gray-200 dark:bg-white/10"></div>
        <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">ou</span>
        <div class="flex-1 h-px bg-gray-200 dark:bg-white/10"></div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Prénom + Nom (2 colonnes) --}}
        <div class="anim-fade-up-1 grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label for="name"
                    class="block text-xs font-semibold tracking-widest uppercase text-gray-400 dark:text-gray-500">
                    Prénom
                </label>
                <div class="relative group">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 transition-all duration-200 group-focus-within:text-primary">
                        <i class="ti ti-user text-sm"></i>
                    </span>
                    <input id="name" name="name" type="text" autocomplete="given-name" required
                        value="{{ old('name') }}" class="w-full pl-9!" placeholder="Jean">
                </div>
                @error('name')
                    <p class="text-xs text-red-500 flex items-center gap-1">
                        <i class="ti ti-alert-circle-filled"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="last_name"
                    class="block text-xs font-semibold tracking-widest uppercase text-gray-400 dark:text-gray-500">
                    Nom
                </label>
                <div class="relative group">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 transition-all duration-200 group-focus-within:text-primary">
                        <i class="ti ti-user text-sm"></i>
                    </span>
                    <input id="last_name" name="last_name" type="text" autocomplete="family-name" required
                        value="{{ old('last_name') }}" class="w-full pl-9!" placeholder="Dupont">
                </div>
                @error('last_name')
                    <p class="text-xs text-red-500 flex items-center gap-1">
                        <i class="ti ti-alert-circle-filled"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="anim-fade-up-2 space-y-1.5">
            <label for="email"
                class="block text-xs font-semibold tracking-widest uppercase text-gray-400 dark:text-gray-500">
                Adresse e-mail
            </label>
            <div class="relative group">
                <span
                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 transition-all duration-200 group-focus-within:text-primary">
                    <i class="ti ti-mail text-base"></i>
                </span>
                <input id="email" name="email" type="email" autocomplete="email" required
                    value="{{ old('email') }}" class="w-full pl-10!" placeholder="">
            </div>
            @error('email')
                <p class="text-xs text-red-500 flex items-center gap-1">
                    <i class="ti ti-alert-circle-filled"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="anim-fade-up-3 space-y-1.5">
            <label for="password"
                class="block text-xs font-semibold tracking-widest uppercase text-gray-400 dark:text-gray-500">
                Mot de passe
            </label>
            <div class="relative group">
                <span
                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 transition-all duration-200 group-focus-within:text-primary">
                    <i class="ti ti-lock text-base"></i>
                </span>
                <input id="password" name="password" type="password" autocomplete="new-password" required
                    class="w-full pl-10! pr-11" placeholder="Min. 8 caractères" minlength="8">
                <button type="button" onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200 focus:outline-none">
                    <i class="ti ti-eye text-base"></i>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-red-500 flex items-center gap-1">
                    <i class="ti ti-alert-circle-filled"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="anim-fade-up-4 space-y-1.5">
            <label for="password_confirmation"
                class="block text-xs font-semibold tracking-widest uppercase text-gray-400 dark:text-gray-500">
                Confirmation
            </label>
            <div class="relative group">
                <span
                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 transition-all duration-200 group-focus-within:text-primary">
                    <i class="ti ti-lock-check text-base"></i>
                </span>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                    required class="w-full pl-10! pr-11"  minlength="8">
                <button type="button" onclick="togglePassword('password_confirmation', this)"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200 focus:outline-none">
                    <i class="ti ti-eye text-base"></i>
                </button>
            </div>
            @error('password_confirmation')
                <p class="text-xs text-red-500 flex items-center gap-1">
                    <i class="ti ti-alert-circle-filled"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Terms --}}
        <div class="anim-fade-up-5 flex items-start gap-3 pt-1">
            <input id="terms" name="terms" type="checkbox" required
                class="mt-0.5 size-4 rounded border-gray-300 text-primary focus:ring-2 dark:border-gray-600 dark:bg-zinc-800">
            <label for="terms" class="text-sm text-gray-500 dark:text-gray-400 cursor-pointer leading-snug">
                J'accepte les
                <a href="#"
                    class="font-semibold text-primary hover:text-primary-dark transition-colors duration-200">conditions
                    d'utilisation</a>
                et la
                <a href="#"
                    class="font-semibold text-primary hover:text-primary-dark transition-colors duration-200">politique de
                    confidentialité</a>
            </label>
        </div>

        {{-- Submit --}}
        <div class="anim-fade-up-5 pt-1">
            <button type="submit"
                class="group relative w-full flex items-center justify-center gap-2 overflow-hidden rounded-xl
                       bg-primary px-4 py-3.5 font-semibold text-white
                       shadow-lg shadow-green-500/20
                       transition-all duration-300
                       hover:bg-primary-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-green-500/25
                       active:translate-y-0 active:shadow-md">
                <i class="ti ti-user-plus text-lg"></i>
                <span>Créer mon compte</span>
            </button>
        </div>

    </form>
@endsection

@section('footer')
    <div class="flex items-center justify-center gap-1.5 text-sm">
        <span class="text-gray-500 dark:text-gray-400">Déjà un compte ?</span>
        <a href="{{ route('login') }}"
            class="font-semibold text-primary hover:text-primary-dark transition-colors duration-200 inline-flex items-center gap-0.5 group">
            Se connecter
            <i class="ti ti-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-0.5"></i>
        </a>
    </div>
@endsection
