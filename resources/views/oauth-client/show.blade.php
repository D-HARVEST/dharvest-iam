@extends('layouts.app-d')
@section('pageTitle', 'Détails - Client OAuth')
@section('content')
    <div class="active-nav-custom-url" url="{{ route('oauth-clients.index') }}"></div>

    <div class="">
        {{-- Header / Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight theme-title">{{ $oauthClient->name }}</h1>
                <p class="mt-0 text-sm theme-muted-text">Détails et informations du client OAuth.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="{{ route('oauth-clients.index') }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-arrow-left text-base"></span>
                    Retour
                </a>
                <a href="{{ route('oauth-clients.edit', $oauthClient->id) }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="ti ti-edit text-base"></span>
                    Modifier
                </a>
                <form action="{{ route('oauth-clients.destroy', $oauthClient->id) }}" method="POST"
                    onsubmit="return confirmDeletion(event, 'Suppression', 'Voulez-vous vraiment supprimer ce client OAuth ? Tous les tokens associés seront supprimés. Cette action est irréversible.')"
                    class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center gap-2 rounded-md border border-red-200 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/40">
                        <span class="ti ti-trash text-base"></span>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        {{-- Secret Flash (only shown once after creation or regeneration) --}}
        @if (session('plain_secret'))
            <div class="mt-5 rounded-xl border border-amber-300 bg-amber-50 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <i class="ti ti-alert-triangle text-xl text-amber-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-amber-800">Secret du client (affiché une seule fois)</h3>
                        <p class="mt-1 text-xs text-amber-700">Copiez ce secret maintenant. Il ne sera plus jamais affiché
                            en clair.</p>
                        <div class="mt-2 flex items-center gap-2">
                            <code id="plain-secret"
                                class="flex-1 rounded-md bg-white border border-amber-200 px-3 py-2 text-sm font-mono text-gray-900 select-all">{{ session('plain_secret') }}</code>
                            <button type="button"
                                onclick="navigator.clipboard.writeText(document.getElementById('plain-secret').textContent).then(() => this.innerHTML='<i class=\'ti ti-check text-green-600\'></i>')"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300 bg-white hover:bg-gray-50 text-gray-500 transition"
                                title="Copier">
                                <i class="ti ti-copy text-base"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Details Card --}}
        <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm px-6 py-2 mt-5">
            <dl class="divide-y divide-gray-100 dark:divide-gray-800">

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Client ID</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm font-mono text-gray-700">{{ $oauthClient->id }}</dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Nom</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">{{ $oauthClient->name }}</dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Secret</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">
                        @if ($oauthClient->confidential())
                            <div class="flex items-center gap-3">
                                <span class="text-gray-400 italic text-xs">Le secret est hashé et ne peut pas être
                                    affiché.</span>
                                <form action="{{ route('oauth-clients.regenerate-secret', $oauthClient->id) }}"
                                    method="POST"
                                    onsubmit="return confirmDeletion(event, 'Régénérer le secret', 'L\'ancien secret sera invalidé. Tous les systèmes utilisant l\'ancien secret devront être mis à jour.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 rounded-md border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                        <i class="ti ti-refresh text-sm"></i>
                                        Régénérer
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="inline-flex items-center gap-1 text-gray-500 text-xs">
                                <i class="ti ti-lock-open text-sm"></i> Client public (pas de secret)
                            </span>
                        @endif
                    </dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Provider</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">{{ $oauthClient->provider ?? '—' }}</dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Grant Types</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse($oauthClient->grant_types as $gt)
                                <span
                                    class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                                    {{ \App\Models\OauthClient::GRANT_TYPES[$gt] ?? $gt }}
                                </span>
                            @empty
                                <span class="text-gray-400 text-sm">Aucun</span>
                            @endforelse
                        </div>
                    </dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">URIs de redirection</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">
                        @if (!empty($oauthClient->redirect_uris))
                            <ul class="space-y-1">
                                @foreach ($oauthClient->redirect_uris as $uri)
                                    <li class="flex items-center gap-2">
                                        <i class="ti ti-external-link text-xs text-gray-400"></i>
                                        <code class="text-xs bg-gray-50 rounded px-2 py-0.5">{{ $uri }}</code>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Statut</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">
                        <div class="flex items-center gap-3">
                            @if ($oauthClient->revoked)
                                <span
                                    class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                    <i class="ti ti-ban text-sm mr-1"></i> Révoqué
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                    <i class="ti ti-circle-check text-sm mr-1"></i> Actif
                                </span>
                            @endif
                            <form action="{{ route('oauth-clients.toggle-revoke', $oauthClient->id) }}" method="POST"
                                class="inline-flex">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-1 rounded-md border border-gray-300 px-3 py-1 text-xs font-medium {{ $oauthClient->revoked ? 'text-green-700 hover:bg-green-50' : 'text-red-700 hover:bg-red-50' }} transition">
                                    <i
                                        class="ti {{ $oauthClient->revoked ? 'ti-player-play' : 'ti-player-pause' }} text-sm"></i>
                                    {{ $oauthClient->revoked ? 'Réactiver' : 'Révoquer' }}
                                </button>
                            </form>
                        </div>
                    </dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Tokens actifs</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm font-medium">{{ $oauthClient->tokens_count ?? 0 }}</dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Créé le</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">{{ $oauthClient->created_at?->format('d/m/Y à H:i') }}</dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Modifié le</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">{{ $oauthClient->updated_at?->format('d/m/Y à H:i') }}</dd>
                </div>

            </dl>
        </div>
    </div>
@endsection
