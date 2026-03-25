@extends('auth.template')

@section('title', 'Mot de passe oublié — ' . config('app.name'))

@section('eyebrow')
    <div class="anim-fade-up flex items-center gap-2 mb-4">
        <div class="h-px w-8 rounded-full bg-primary"></div>
        <span class="text-xs font-semibold uppercase tracking-widest text-primary">Réinitialisation</span>
    </div>
@endsection

@section('header')Mot de passe oublié.@endsection

@section('description', 'Renseignez votre e-mail pour recevoir un lien de réinitialisation.')

@section('content')
    {{-- Succès envoi --}}
    @if (session('status'))
        <div class="anim-fade-up mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800/50 dark:bg-green-900/20">
            <i class="ti ti-circle-check text-lg text-green-600 dark:text-green-400 shrink-0"></i>
            <span class="text-sm text-green-700 dark:text-green-300">{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div class="anim-fade-up-1 space-y-1.5">
            <label for="email" class="block text-xs font-semibold tracking-widest uppercase text-gray-400 dark:text-gray-500">
                Adresse e-mail
            </label>
            <div class="relative group">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 transition-all duration-200 group-focus-within:text-primary">
                    <i class="ti ti-mail text-base"></i>
                </span>
                <input id="email" name="email" type="email" autocomplete="email" required
                       value="{{ old('email') }}"
                       class="w-full pl-10!"
                       placeholder="votre@email.com">
            </div>
            @error('email')
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
                <i class="ti ti-send text-lg"></i>
                <span>Envoyer le lien</span>
            </button>
        </div>
    </form>
@endsection

@section('footer')
    <div class="flex items-center justify-center gap-1.5 text-sm">
        <span class="text-gray-500 dark:text-gray-400">Vous vous souvenez ?</span>
        <a href="{{ route('login') }}"
            class="font-semibold text-primary hover:text-primary-dark transition-colors duration-200 inline-flex items-center gap-0.5 group">
            Se connecter
            <i class="ti ti-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-0.5"></i>
        </a>
    </div>
@endsection
