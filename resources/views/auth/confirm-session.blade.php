@extends('auth.template')

@section('title', 'Session active — ' . config('app.name'))

@section('eyebrow')
    <div class="anim-fade-up flex items-center gap-2 mb-4">
        <div class="h-px w-8 rounded-full bg-primary"></div>
        <span class="text-xs font-semibold uppercase tracking-widest text-primary">Session active</span>
    </div>
@endsection

@section('header', 'Déjà connecté')

@section('description', 'Une session est déjà ouverte. Souhaitez-vous continuer ou utiliser un autre compte ?')

@section('content')

    {{-- Compte actif --}}
    <div class="anim-fade-up flex items-center gap-4 rounded-2xl theme-muted p-4 mb-5">
        <div class="size-10 rounded-full bg-primary/15 flex items-center justify-center shrink-0">
            <i class="ti ti-user text-primary text-lg"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold theme-title truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs theme-body mt-0.5 truncate">{{ Auth::user()->email }}</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium px-2.5 py-1 shrink-0">
            <span class="size-1.5 rounded-full bg-green-500 inline-block"></span>
            Actif
        </span>
    </div>

    {{-- Barre de progression --}}
    <div class="anim-fade-up-1 mb-5">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-400 dark:text-gray-500">Redirection automatique dans</span>
            <span id="countdown" class="text-xs font-semibold text-primary tabular-nums">3s</span>
        </div>
        <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-white/[0.07] overflow-hidden">
            <div id="progress-bar" class="h-full rounded-full bg-primary" style="width: 100%"></div>
        </div>
    </div>

    {{-- Continuer (auto-soumis) --}}
    <form id="continue-form" method="POST" action="{{ route('oauth.confirm-session.continue') }}">
        @csrf
        <button type="submit"
            class="anim-fade-up-2 w-full flex items-center justify-center gap-2 rounded-xl
                   bg-primary px-4 py-3 text-sm font-semibold text-white
                   shadow-lg shadow-green-500/20
                   transition-all duration-300
                   hover:bg-primary-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-green-500/25
                   active:translate-y-0 active:shadow-md">
            <i class="ti ti-arrow-right text-base"></i>
            Continuer avec ce compte
        </button>
    </form>

    {{-- Autre compte --}}
    <form method="POST" action="{{ route('oauth.confirm-session.switch') }}" class="mt-3">
        @csrf
        <button type="submit"
            class="anim-fade-up-3 w-full flex items-center justify-center gap-2 rounded-xl
                   border border-gray-200 dark:border-white/[0.1]
                   bg-white dark:bg-white/[0.04] px-4 py-3 text-sm font-semibold theme-body
                   hover:bg-gray-50 dark:hover:bg-white/[0.08] transition-all duration-200
                   hover:-translate-y-0.5 active:translate-y-0">
            <i class="ti ti-user-plus text-base"></i>
            Se connecter avec un autre compte
        </button>
    </form>

    <script>
        (function () {
            const DURATION = 3000;
            const bar = document.getElementById('progress-bar');
            const countdown = document.getElementById('countdown');
            const startTime = Date.now();

            // Démarrer l'animation de la barre
            requestAnimationFrame(function () {
                bar.style.transition = 'width ' + DURATION + 'ms linear';
                bar.style.width = '0%';
            });

            // Mettre à jour le compteur
            const interval = setInterval(function () {
                const elapsed = Date.now() - startTime;
                const remaining = Math.ceil((DURATION - elapsed) / 1000);
                countdown.textContent = Math.max(remaining, 0) + 's';
                if (elapsed >= DURATION) clearInterval(interval);
            }, 200);

            // Soumettre automatiquement
            setTimeout(function () {
                document.getElementById('continue-form').submit();
            }, DURATION);
        })();
    </script>

@endsection
