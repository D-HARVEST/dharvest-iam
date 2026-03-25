@extends('auth.template')

@section('title', 'Autorisation — ' . config('app.name'))

@section('eyebrow')
    <div class="anim-fade-up flex items-center gap-2 mb-4">
        <div class="h-px w-8 rounded-full bg-primary"></div>
        <span class="text-xs font-semibold uppercase tracking-widest text-primary">Autorisation OAuth</span>
    </div>
@endsection

@section('header'){{ $client->name }}@endsection

@section('description', 'Cette application demande l\'accès à votre compte D-HARVEST.')

@section('content')

    {{-- Identité client --}}
    <div class="anim-fade-up flex items-center gap-4 rounded-2xl theme-muted p-4 mb-2">
        <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
            <i class="ti ti-app-window text-xl text-primary"></i>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-semibold theme-title truncate">{{ $client->name }}</p>
            <p class="text-xs theme-body mt-0.5">souhaite accéder à votre compte</p>
        </div>
    </div>

    {{-- Scopes demandés --}}
    @if (count($scopes) > 0)
        <div class="anim-fade-up-1 mt-4 rounded-xl border border-gray-100 dark:border-white/[0.06] overflow-hidden">
            <div class="px-4 py-2.5 bg-gray-50 dark:bg-white/[0.03] border-b border-gray-100 dark:border-white/[0.06]">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                    Permissions demandées
                </p>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                @foreach ($scopes as $scope)
                    <li class="flex items-center gap-3 px-4 py-3">
                        <i class="ti ti-circle-check text-base text-green-500 shrink-0"></i>
                        <span class="text-sm theme-body">{{ $scope->description }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="anim-fade-up-1 mt-3 flex items-center gap-2.5 rounded-xl bg-blue-50 dark:bg-blue-900/15 border border-blue-100 dark:border-blue-800/30 px-4 py-3">
            <i class="ti ti-info-circle text-base text-blue-500 shrink-0"></i>
            <p class="text-xs text-blue-700 dark:text-blue-300">Accès de base uniquement (aucune permission étendue).</p>
        </div>
    @endif

    {{-- Avertissement --}}
    <p class="anim-fade-up-2 mt-4 text-xs text-gray-400 dark:text-gray-500 text-center leading-relaxed">
        En autorisant, <strong class="text-gray-600 dark:text-gray-300">{{ $client->name }}</strong> pourra
        accéder aux informations ci-dessus jusqu'à révocation.
    </p>

    {{-- Actions --}}
    <div class="anim-fade-up-3 mt-5 flex gap-3">

        {{-- Refuser --}}
        <form method="POST" action="{{ route('passport.authorizations.deny') }}" class="flex-1">
            @csrf
            @method('DELETE')
            <input type="hidden" name="state"      value="{{ $request->state }}">
            <input type="hidden" name="client_id"  value="{{ $client->getKey() }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-white/[0.1]
                       bg-white dark:bg-white/[0.04] px-4 py-3 text-sm font-semibold theme-body
                       hover:bg-gray-50 dark:hover:bg-white/[0.08] transition-all duration-200
                       hover:-translate-y-0.5 active:translate-y-0">
                <i class="ti ti-x text-base"></i>
                Refuser
            </button>
        </form>

        {{-- Autoriser --}}
        <form method="POST" action="{{ route('passport.authorizations.approve') }}" class="flex-1">
            @csrf
            <input type="hidden" name="state"      value="{{ $request->state }}">
            <input type="hidden" name="client_id"  value="{{ $client->getKey() }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 rounded-xl
                       bg-primary px-4 py-3 text-sm font-semibold text-white
                       shadow-lg shadow-green-500/20
                       transition-all duration-300
                       hover:bg-primary-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-green-500/25
                       active:translate-y-0 active:shadow-md">
                <i class="ti ti-check text-base"></i>
                Autoriser
            </button>
        </form>

    </div>

@endsection

@section('footer')
    <p class="text-xs text-gray-400 dark:text-gray-500 text-center">
        Connecté en tant que <strong class="text-gray-600 dark:text-gray-300">{{ Auth::user()->email }}</strong>
        · <a href="{{ route('logout') }}"
             onclick="event.preventDefault(); document.getElementById('logout-form-oauth').submit();"
             class="text-primary hover:text-primary-dark transition-colors duration-200">Se déconnecter</a>
    </p>
    <form id="logout-form-oauth" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
@endsection
