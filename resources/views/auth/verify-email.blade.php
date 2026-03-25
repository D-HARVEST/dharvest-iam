@extends('auth.template')

@section('title', 'Vérifiez votre e-mail — ' . config('app.name'))

@section('eyebrow')
    <div class="anim-fade-up flex items-center gap-2 mb-4">
        <div class="h-px w-8 rounded-full bg-primary"></div>
        <span class="text-xs font-semibold uppercase tracking-widest text-primary">Vérification</span>
    </div>
@endsection

@section('header')Vérifiez votre e-mail.@endsection

@section('content')
    {{-- Icône illustrative --}}
    <div class="anim-fade-up flex justify-center mb-6">
        <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center">
            <i class="ti ti-mail-check text-3xl text-primary"></i>
        </div>
    </div>

    {{-- Message --}}
    <div class="anim-fade-up-1 space-y-3 text-center mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            Un lien de vérification a été envoyé à votre adresse e-mail. <span
                style="font-weight: bold">({{ Auth::user()->email }})</span><br>
            Consultez votre boîte de réception et cliquez sur le lien pour activer votre compte.
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500">
            Pensez à vérifier vos spams si vous ne trouvez pas l'e-mail.
        </p>
    </div>

    {{-- Succès renvoi --}}
    @if (session('status') === 'verification-link-sent')
        <div
            class="anim-fade-up mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800/50 dark:bg-green-900/20">
            <i class="ti ti-circle-check text-lg text-green-600 dark:text-green-400 shrink-0"></i>
            <span class="text-sm text-green-700 dark:text-green-300">
                Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
            </span>
        </div>
    @endif

    {{-- Renvoyer --}}
    <div class="anim-fade-up-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="group relative w-full flex items-center justify-center gap-2 overflow-hidden rounded-xl
                       bg-primary px-4 py-3.5 font-semibold text-white
                       shadow-lg shadow-green-500/20
                       transition-all duration-300
                       hover:bg-primary-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-green-500/25
                       active:translate-y-0 active:shadow-md">
                <i class="ti ti-mail-forward text-lg"></i>
                <span>Renvoyer l'e-mail de vérification</span>
            </button>
        </form>
    </div>
@endsection

@section('footer')
    <form method="POST" action="{{ route('logout') }}" class="flex items-center justify-center">
        @csrf
        <button type="submit"
            class="text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200 inline-flex items-center gap-1.5 group">
            <i class="ti ti-logout text-sm"></i>
            Se déconnecter
        </button>
    </form>
@endsection
