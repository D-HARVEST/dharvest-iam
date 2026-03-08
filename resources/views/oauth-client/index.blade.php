@extends('layouts.app')
@section('pageTitle', 'Clients OAuth')

@section('content')

    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $oauthClients */
        $isPaginator = is_object($oauthClients) && method_exists($oauthClients, 'firstItem');
        $firstItem = $isPaginator ? $oauthClients->firstItem() ?? 1 : 1;
        $totalItems = $isPaginator
            ? $oauthClients->total()
            : ($oauthClients instanceof \Illuminate\Support\Collection
                ? $oauthClients->count()
                : 0);
        $q = request('q');
    @endphp

    <div>
        <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">Clients OAuth</h1>
        <p class="mb-2 text-sm theme-muted-text">Gérez les clients OAuth Passport de la plateforme.</p>
    </div>

    {{-- Search / Filters --}}
    <div class="theme-surface backdrop-blur-sm border border-gray-200 rounded-xl p-4 shadow-sm mb-4">
        <form method="GET" action="{{ route('oauth-clients.index') }}" class="flex gap-3">
            <div class="w-[300px] relative">
                <label for="q" class="sr-only">Recherche</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}"
                    placeholder="Rechercher par nom..."
                    class="w-full rounded-lg border-gray-200 bg-white/70 focus:bg-white px-4 py-2.5 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" />
                @if (request('q'))
                    <button type="button" onclick="document.getElementById('q').value=''; this.closest('form').submit();"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        title="Effacer">
                        <span class="ti ti-x"></span>
                    </button>
                @endif
            </div>
            <div class="gap-2">
                @if (request('q'))
                    <a href="{{ route('oauth-clients.index') }}"
                        class="inline-flex items-center rounded-md px-3 py-2.5 text-sm font-medium bg-slate-200 hover:bg-slate-300 text-gray-800">
                        Réinitialiser
                    </a>
                @endif
            </div>
            <div class="flex-1"></div>
            <a href="{{ route('oauth-clients.create') }}"
                class="inline-flex items-center gap-2 rounded-md px-4 py-1 text-sm font-medium text-black bg-secondary focus:outline-none focus:ring-2">
                <span class="ti ti-plus text-base"></span>
                Nouveau client
            </a>
        </form>
    </div>

    <div class="">
        {{-- Table --}}
        <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-gray-200 text-[11px] uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="py-3 pl-4 pr-3 text-left font-medium">#</th>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Nom</th>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Grant Types</th>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Confidentiel</th>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Statut</th>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Créé le</th>
                            <th class="py-3 px-3 text-right font-medium w-px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($oauthClients as $oauthClient)
                            <tr class="group hover:bg-indigo-50/40 transition-colors">
                                <td class="whitespace-nowrap py-3 pl-4 pr-3 font-semibold text-gray-700">
                                    {{ $firstItem + $loop->index }}</td>

                                {{-- Nom --}}
                                <td class="whitespace-nowrap px-3 py-3 text-sm">
                                    <div class="font-medium text-gray-900">{{ $oauthClient->name }}</div>
                                    @if ($oauthClient->provider)
                                        <div class="text-xs text-gray-400">Provider: {{ $oauthClient->provider }}</div>
                                    @endif
                                </td>

                                {{-- Grant Types (badges) --}}
                                <td class="px-3 py-3 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($oauthClient->grant_types as $gt)
                                            <span
                                                class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700">
                                                {{ \App\Models\OauthClient::GRANT_TYPES[$gt] ?? $gt }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- Confidentiel --}}
                                <td class="whitespace-nowrap px-3 py-3 text-sm">
                                    @if ($oauthClient->confidential())
                                        <span class="inline-flex items-center gap-1 text-green-700 text-xs font-medium">
                                            <i class="ti ti-lock text-sm"></i> Oui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-gray-400 text-xs font-medium">
                                            <i class="ti ti-lock-open text-sm"></i> Non
                                        </span>
                                    @endif
                                </td>

                                {{-- Statut --}}
                                <td class="whitespace-nowrap px-3 py-3 text-sm">
                                    @if ($oauthClient->revoked)
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">
                                            <i class="ti ti-ban text-sm mr-1"></i> Révoqué
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">
                                            <i class="ti ti-circle-check text-sm mr-1"></i> Actif
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                    {{ $oauthClient->created_at?->format('d/m/Y H:i') }}
                                </td>

                                {{-- Actions --}}
                                <td class="whitespace-nowrap px-3 py-3 text-right">
                                    <div
                                        class="flex items-center justify-end gap-1 opacity-70 group-hover:opacity-100 transition">
                                        <a href="{{ route('oauth-clients.show', $oauthClient->id) }}" title="Voir"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                            <span class="ti ti-eye text-[17px]"></span>
                                        </a>
                                        <a href="{{ route('oauth-clients.edit', $oauthClient->id) }}" title="Modifier"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition">
                                            <span class="ti ti-edit text-[17px]"></span>
                                        </a>

                                        {{-- Toggle Révocation --}}
                                        <form action="{{ route('oauth-clients.toggle-revoke', $oauthClient->id) }}"
                                            method="POST" class="inline-flex">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                title="{{ $oauthClient->revoked ? 'Réactiver' : 'Révoquer' }}"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 {{ $oauthClient->revoked ? 'hover:text-green-600 hover:bg-green-50' : 'hover:text-orange-600 hover:bg-orange-50' }} transition">
                                                <span
                                                    class="ti {{ $oauthClient->revoked ? 'ti-player-play' : 'ti-player-pause' }} text-[17px]"></span>
                                            </button>
                                        </form>

                                        <form action="{{ route('oauth-clients.destroy', $oauthClient->id) }}"
                                            method="POST" class="inline-flex"
                                            onsubmit="return confirmDeletion(event, 'Suppression', 'Voulez-vous vraiment supprimer ce client OAuth ? Tous les tokens associés seront aussi supprimés. Cette action est irréversible.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-red-600 hover:bg-red-50 transition">
                                                <span class="ti ti-trash text-[17px]"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100" class="py-12 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="ti ti-database-off text-2xl text-gray-300"></span>
                                        <p class="font-medium">{{ __('No records found') }}@if ($q)
                                                {{ __('for') }} « {{ $q }} »
                                            @endif.</p>
                                        <div class="flex gap-2 mt-2">
                                            @if ($q)
                                                <a href="{{ route('oauth-clients.index') }}"
                                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800">{{ __('Clear search') }}</a>
                                            @endif
                                            <a href="{{ route('oauth-clients.create') }}"
                                                class="text-xs font-medium text-gray-600 hover:text-gray-900">Créer un
                                                premier client</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                class="px-4 py-4 border-t border-gray-100 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-xs text-gray-500 order-2 sm:order-1">
                    @if ($totalItems > 0)
                        @if ($isPaginator)
                            {{ __('Showing') }} {{ $oauthClients->firstItem() }} - {{ $oauthClients->lastItem() }}
                            {{ __('of') }} {{ $oauthClients->total() }}
                        @else
                            {{ __('Total') }} : {{ $totalItems }}
                        @endif
                    @else
                        {{ __('No entries to display') }}
                    @endif
                </div>
                <div class="order-1 sm:order-2">
                    @if ($isPaginator)
                        {!! $oauthClients->withQueryString()->links() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
