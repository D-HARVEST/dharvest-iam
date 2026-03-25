<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.26.0/dist/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .auth-dots {
            background-image: radial-gradient(rgba(0, 201, 81, 0.14) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
        }

        @keyframes drift {

            0%,
            100% {
                transform: translateY(0px) translateX(0px);
            }

            33% {
                transform: translateY(-22px) translateX(9px);
            }

            66% {
                transform: translateY(12px) translateX(-6px);
            }
        }

        @keyframes orbit {
            from {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        @keyframes fade-up {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim-drift {
            animation: drift 12s ease-in-out infinite;
        }

        .anim-drift-slow {
            animation: drift 18s ease-in-out infinite 3s;
        }

        .anim-orbit {
            animation: orbit 45s linear infinite;
        }

        .anim-orbit-r {
            animation: orbit 30s linear infinite reverse;
        }

        .anim-fade-up {
            animation: fade-up .55s cubic-bezier(.22, 1, .36, 1) both;
        }

        .anim-fade-up-1 {
            animation: fade-up .55s cubic-bezier(.22, 1, .36, 1) .1s both;
        }

        .anim-fade-up-2 {
            animation: fade-up .55s cubic-bezier(.22, 1, .36, 1) .2s both;
        }

        .anim-fade-up-3 {
            animation: fade-up .55s cubic-bezier(.22, 1, .36, 1) .3s both;
        }

        .anim-fade-up-4 {
            animation: fade-up .55s cubic-bezier(.22, 1, .36, 1) .4s both;
        }

        .anim-fade-up-5 {
            animation: fade-up .55s cubic-bezier(.22, 1, .36, 1) .5s both;
        }
    </style>
</head>

<body class="overflow-x-hidden bg-white dark:bg-zinc-950">
    <div class="min-h-screen flex">

        {{-- ══════════════════════════════════════════
         LEFT — Brand / Decorative Panel
         TODO: remplacer l'aside ci-dessous par une image
    ══════════════════════════════════════════ --}}

        {{--
        ╔═══════════════════════════════════════════════════════════════╗
        ║  VERSION DÉGRADÉ VERT — décommenter pour revenir en arrière  ║
        ╚═══════════════════════════════════════════════════════════════╝

        <aside class="hidden lg:flex w-5/12 xl:w-[42%] relative overflow-hidden flex-col flex-shrink-0 rounded-3xl m-4"
            style="background: linear-gradient(150deg, #1a9244 0%, #24ad52 45%, #28be5c 100%);">

            <div class="auth-dots absolute inset-0"
                style="background-image:radial-gradient(rgba(255,255,255,.12) 1.5px,transparent 1.5px);"></div>

            <div class="anim-drift absolute pointer-events-none"
                style="width:580px;height:580px;top:-130px;right:-200px;
                    background:radial-gradient(circle,rgba(255,255,255,.12) 0%,transparent 65%);border-radius:50%;"></div>
            <div class="anim-drift-slow absolute pointer-events-none"
                style="width:420px;height:420px;bottom:-100px;left:-140px;
                    background:radial-gradient(circle,rgba(0,0,0,.15) 0%,transparent 65%);border-radius:50%;"></div>

            <div class="anim-orbit absolute pointer-events-none"
                style="top:50%;left:50%;width:700px;height:700px;margin-left:-350px;margin-top:-350px;
                    border:1px solid rgba(255,255,255,.10);border-radius:50%;"></div>
            <div class="anim-orbit-r absolute pointer-events-none"
                style="top:50%;left:50%;width:520px;height:520px;margin-left:-260px;margin-top:-260px;
                    border:1px solid rgba(255,255,255,.14);border-radius:50%;"></div>
            <div class="absolute pointer-events-none"
                style="top:50%;left:50%;width:340px;height:340px;margin-left:-170px;margin-top:-170px;
                    border:1px solid rgba(255,255,255,.08);border-radius:50%;"></div>

            <div class="relative z-10 flex flex-col h-full p-10 xl:p-14 justify-between">
                <div class="anim-drift-slow">
                    <div class="h-px w-10 mb-6" style="background:rgba(255,255,255,.5);"></div>
                    <h2 class="text-white text-4xl xl:text-[2.75rem] font-bold leading-[1.18] tracking-tight">
                        Gérez vos<br>accès avec<br>
                        <span style="color:#b8ffd4;">précision.</span>
                    </h2>
                    <p class="text-white/75 text-sm mt-5 leading-relaxed max-w-xs">
                        Une plateforme centralisée pour gérer<br>vos identités en toute sécurité.
                    </p>
                </div>
                <div>
                    <div class="anim-drift flex items-center gap-3 rounded-2xl px-4 py-3.5"
                        style="background:rgba(0,0,0,.15);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px);animation-duration:13s;">
                        <div class="size-9 rounded-xl flex-shrink-0 flex items-center justify-center"
                            style="background:rgba(255,255,255,.15);">
                            <i class="ti ti-shield-check text-sm text-white"></i>
                        </div>
                        <div>
                            <p class="text-white text-xs font-semibold">Sécurité maximale</p>
                            <p class="text-white/60 text-xs">Authentification 2FA · Chiffrement AES-256</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        --}}

        {{-- NOUVELLE VERSION — décoration multicolore --}}
        <aside
            class="hidden lg:flex w-5/12 xl:w-[42%] relative overflow-hidden flex-col shrink-0 rounded-3xl m-4 bg-gray-100 dark:bg-zinc-900">

            {{-- Blobs colorés --}}
            {{-- Vert — haut droite --}}
            <div class="absolute pointer-events-none"
                style="width:480px;height:480px;top:-110px;right:-130px;
                        background:radial-gradient(circle,rgba(0,201,81,.28) 0%,transparent 62%);border-radius:50%;">
            </div>
            {{-- Bleu ciel — milieu gauche --}}
            <div class="absolute pointer-events-none anim-drift"
                style="width:360px;height:360px;top:22%;left:-90px;
                        background:radial-gradient(circle,rgba(56,189,248,.22) 0%,transparent 62%);border-radius:50%;animation-duration:15s;">
            </div>
            {{-- Violet — bas droite --}}
            <div class="absolute pointer-events-none anim-drift-slow"
                style="width:380px;height:380px;bottom:-90px;right:-90px;
                        background:radial-gradient(circle,rgba(167,139,250,.22) 0%,transparent 62%);border-radius:50%;">
            </div>
            {{-- Orange — bas gauche --}}
            <div class="absolute pointer-events-none anim-drift"
                style="width:280px;height:280px;bottom:18%;left:-55px;
                        background:radial-gradient(circle,rgba(251,146,60,.20) 0%,transparent 62%);border-radius:50%;animation-duration:19s;animation-delay:3s;">
            </div>

            {{-- Grille de points --}}
            <div class="absolute inset-0"
                style="background-image:radial-gradient(rgba(0,0,0,.07) 1px,transparent 1px);background-size:26px 26px;">
            </div>

            {{-- Anneaux décoratifs --}}
            <div class="anim-orbit absolute pointer-events-none"
                style="top:50%;left:50%;width:520px;height:520px;margin-left:-260px;margin-top:-260px;
                        border:1px solid rgba(0,201,81,.12);border-radius:50%;">
            </div>
            <div class="anim-orbit-r absolute pointer-events-none"
                style="top:50%;left:50%;width:360px;height:360px;margin-left:-180px;margin-top:-180px;
                        border:1px solid rgba(56,189,248,.14);border-radius:50%;">
            </div>

            {{-- Contenu --}}
            <div class="relative z-10 flex flex-col h-full p-10 xl:p-14 justify-between">

                {{-- Logo --}}
                <div class=""></div>
                {{-- <a href="/" class="flex items-center gap-2.5">
                    <img src="{{ asset('logo-dh.svg') }}" class="h-7 dark:brightness-0 dark:invert"
                        alt="{{ config('app.name') }}">
                    <span
                        class="font-semibold text-base tracking-wide text-gray-800 dark:text-white">{{ config('app.name') }}</span>
                </a> --}}

                {{-- Tagline --}}
                <div>
                    <div class="h-px w-10 mb-6 bg-gray-300 dark:bg-white/30"></div>
                    <h2
                        class="text-gray-900 dark:text-white text-4xl xl:text-[2.75rem] font-bold leading-[1.18] tracking-tight">
                        Gérez vos<br>accès avec<br>
                        <span style="color:#00C951;">précision.</span>
                    </h2>
                    <p class="text-gray-500 dark:text-white/55 text-sm mt-5 leading-relaxed max-w-xs">
                        Une plateforme centralisée pour gérer<br>vos identités en toute sécurité.
                    </p>
                </div>

                {{-- Chip sécurité --}}
                <div class="anim-drift flex items-center gap-3 rounded-2xl px-4 py-3.5
                            bg-white/70 dark:bg-black/25 backdrop-blur-sm
                            border border-white dark:border-white/10"
                    style="animation-duration:13s;">
                    <div
                        class="size-9 rounded-xl shrink-0 flex items-center justify-center bg-green-100 dark:bg-white/10">
                        <i class="ti ti-shield-check text-sm text-green-600 dark:text-white"></i>
                    </div>
                    <div>
                        <p class="text-gray-800 dark:text-white text-xs font-semibold">Sécurité maximale</p>
                        <p class="text-gray-400 dark:text-white/50 text-xs">Authentification 2FA · Chiffrement AES-256
                        </p>
                    </div>
                </div>

            </div>
        </aside>

        {{-- ══════════════════════════════════════════
         RIGHT — Form Panel
    ══════════════════════════════════════════ --}}
        <main class="flex-1 flex flex-col bg-white dark:bg-zinc-950">

            {{-- Top bar --}}
            <header
                class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-white/[0.06] lg:border-0 lg:py-5 lg:px-8">
                {{-- Mobile logo only --}}
                <a href="/" class="flex items-center gap-2 font-semibold lg:hidden">
                    <img src="{{ asset('logo-dh.svg') }}" style="max-height:22px;" alt="">
                    <span class="theme-title text-sm">{{ config('app.name') }}</span>
                </a>
                <div class="hidden lg:block"></div>
                {{-- Theme toggle --}}
                <button type="button" onclick="window.__toggleDarkMode()"
                    class="flex size-9 items-center justify-center rounded-full bg-gray-100 text-gray-500 transition-all hover:scale-105 hover:bg-gray-200 dark:bg-white/[0.07] dark:text-gray-400 dark:hover:bg-white/[0.12] focus:outline-none">
                    <span class="hidden dark:block"><i class="ti ti-sun text-base"></i></span>
                    <span class="block dark:hidden"><i class="ti ti-moon text-base"></i></span>
                </button>
            </header>

            {{-- Scrollable form area --}}
            <div class="flex-1 flex items-center justify-center px-6 py-10 lg:py-12">
                <div class="w-full max-w-sm">

                    {{-- Flash messages --}}
                    @if (session('success'))
                        <div
                            class="anim-fade-up mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800/50 dark:bg-green-900/20">
                            <i class="ti ti-circle-check text-lg text-green-600 dark:text-green-400"></i>
                            <span class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="anim-fade-up mb-6 flex items-center gap-3 rounded-xl theme-danger p-4">
                            <i class="ti ti-alert-circle text-lg text-red-600"></i>
                            <span class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="anim-fade-up mb-6 rounded-xl theme-danger p-4">
                            <div class="flex items-center gap-2 text-sm font-semibold text-red-700 dark:text-red-400">
                                <i class="ti ti-alert-triangle"></i> Erreur de validation
                            </div>
                            <ul class="mt-2 space-y-1 text-xs text-red-600 dark:text-red-400">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center gap-1.5"><i class="ti ti-point-filled text-xs"></i>
                                        {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Eyebrow slot --}}
                    @hasSection('eyebrow')
                        @yield('eyebrow')
                    @endif

                    {{-- Heading --}}
                    <div class="anim-fade-up mb-4">
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white leading-tight">
                            @yield('header')
                        </h1>
                        @hasSection('description')
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                @yield('description')
                            </p>
                        @endif
                    </div>

                    {{-- Form content --}}
                    @yield('content')

                    {{-- Footer --}}
                    @hasSection('footer')
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-white/[0.06]">
                            @yield('footer')
                        </div>
                    @endif

                </div>
            </div>
        </main>

    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ti-eye', 'ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.replace('ti-eye-off', 'ti-eye');
            }
        }
    </script>
</body>

</html>
