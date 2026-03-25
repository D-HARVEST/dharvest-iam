@extends('auth.template')

@section('title', 'Confirmer le mot de passe — ' . config('app.name'))

@section('eyebrow')
    <div class="anim-fade-up flex items-center gap-2 mb-4">
        <div class="h-px w-8 rounded-full bg-primary"></div>
        <span class="text-xs font-semibold uppercase tracking-widest text-primary">Sécurité</span>
    </div>
@endsection

@section('header')Confirmez votre identité.@endsection

@section('description', 'Cette zone est protégée. Veuillez confirmer votre mot de passe pour continuer.')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        {{-- Icône --}}
        <div class="anim-fade-up flex justify-center mb-2">
            <div class="size-14 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                <i class="ti ti-shield-lock text-2xl text-amber-500"></i>
            </div>
        </div>

        {{-- Mot de passe --}}
        <div class="anim-fade-up-1 space-y-1.5">
            <label for="password" class="block text-xs font-semibold tracking-widest uppercase text-gray-400 dark:text-gray-500">
                Mot de passe
            </label>
            <div class="relative group">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 transition-all duration-200 group-focus-within:text-primary">
                    <i class="ti ti-lock text-base"></i>
                </span>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       class="w-full pl-10! pr-11"
                       placeholder="Votre mot de passe actuel">
                <button type="button" onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200 focus:outline-none">
                    <i class="ti ti-eye text-base"></i>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-red-500 flex items-center gap-1 mt-1">
                    <i class="ti ti-alert-circle-filled"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="anim-fade-up-2 pt-1">
            <button type="submit"
                class="group relative w-full flex items-center justify-center gap-2 overflow-hidden rounded-xl
                       bg-primary px-4 py-3.5 font-semibold text-white
                       shadow-lg shadow-green-500/20
                       transition-all duration-300
                       hover:bg-primary-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-green-500/25
                       active:translate-y-0 active:shadow-md">
                <i class="ti ti-shield-check text-lg"></i>
                <span>Confirmer et continuer</span>
            </button>
        </div>
    </form>
@endsection
