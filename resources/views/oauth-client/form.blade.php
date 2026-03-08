<div class="flex flex-col gap-y-4">

    {{-- Nom du client --}}
    <div>
        <x-input-label for="name" :value="__('Nom du client')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $oauthClient?->name)"
            placeholder="Ex : Mon Application Web" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    {{-- Provider (optionnel) --}}
    <div>
        <x-input-label for="provider" :value="__('Provider (optionnel)')" />
        <select id="provider" name="provider"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">— Aucun (défaut) —</option>
            <option value="users" @selected(old('provider', $oauthClient?->provider) === 'users')>users</option>
        </select>
        <p class="mt-1 text-xs text-gray-500">Provider d'authentification utilisé pour la validation des credentials.
        </p>
        <x-input-error class="mt-2" :messages="$errors->get('provider')" />
    </div>

    {{-- Grant Types --}}
    <div>
        <x-input-label :value="__('Grant Types')" />
        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach ($grantTypes as $value => $label)
                <label
                    class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm cursor-pointer hover:bg-gray-50 transition">
                    <input type="checkbox" name="grant_types[]" value="{{ $value }}"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        @checked(in_array($value, old('grant_types', $oauthClient?->grant_types ?? []))) />
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('grant_types')" />
        <x-input-error class="mt-1" :messages="$errors->get('grant_types.*')" />
    </div>

    {{-- URIs de Redirection --}}
    <div>
        <x-input-label for="redirect_uris" :value="__('URIs de redirection')" />
        <textarea id="redirect_uris" name="redirect_uris" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
            placeholder="https://example.com/callback&#10;https://example.com/auth/redirect">{{ old('redirect_uris', is_array($oauthClient?->redirect_uris) ? implode("\n", $oauthClient->redirect_uris) : $oauthClient?->redirect_uris) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">Une URI par ligne. Obligatoire pour les grants Authorization Code et
            Implicit.</p>
        <x-input-error class="mt-2" :messages="$errors->get('redirect_uris')" />
    </div>

    {{-- Confidential (création uniquement) --}}
    @if (!$oauthClient?->exists)
        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="confidential" value="0" />
                <input type="checkbox" name="confidential" value="1"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-5 w-5"
                    @checked(old('confidential', true)) />
                <div>
                    <span class="text-sm font-medium text-gray-700">Client confidentiel</span>
                    <p class="text-xs text-gray-500">Un secret sera généré automatiquement. Décochez pour un client
                        public (SPA, mobile).</p>
                </div>
            </label>
        </div>
    @endif

    {{-- Révoqué (édition uniquement) --}}
    @if ($oauthClient?->exists)
        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="revoked" value="0" />
                <input type="checkbox" name="revoked" value="1"
                    class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500 h-5 w-5"
                    @checked(old('revoked', $oauthClient?->revoked)) />
                <div>
                    <span class="text-sm font-medium text-gray-700">Révoqué</span>
                    <p class="text-xs text-gray-500">Si coché, ce client ne pourra plus émettre de tokens.</p>
                </div>
            </label>
        </div>
    @endif

</div>
