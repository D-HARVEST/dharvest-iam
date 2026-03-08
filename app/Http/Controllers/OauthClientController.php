<?php

namespace App\Http\Controllers;

use App\Models\OauthClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\OauthClientRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OauthClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $oauthClients = OauthClient::query()
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('oauth-client.index', compact('oauthClients', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $oauthClients->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $oauthClient = new OauthClient();
        $grantTypes = OauthClient::GRANT_TYPES;

        return view('oauth-client.create', compact('oauthClient', 'grantTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OauthClientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $confidential = !empty($data['confidential']);
        $secret = $confidential ? Str::random(40) : null;

        $client = new OauthClient();
        $client->forceFill([
            'name' => $data['name'],
            'secret' => $secret,
            'provider' => $data['provider'] ?? null,
            'redirect_uris' => array_filter(array_map('trim', explode("\n", $data['redirect_uris'] ?? ''))),
            'grant_types' => $data['grant_types'] ?? [],
            'revoked' => false,
        ])->save();

        // Flash the plain secret so the user can copy it (only visible once)
        return Redirect::route('oauth-clients.show', $client->id)
            ->with('success', 'Client OAuth créé avec succès !')
            ->with('plain_secret', $client->plainSecret);
    }

    /**
     * Display the specified resource.
     */
    public function show(OauthClient $oauthClient): View
    {
        $oauthClient->loadCount('tokens');

        return view('oauth-client.show', compact('oauthClient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OauthClient $oauthClient): View
    {
        $grantTypes = OauthClient::GRANT_TYPES;

        return view('oauth-client.edit', compact('oauthClient', 'grantTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OauthClientRequest $request, OauthClient $oauthClient): RedirectResponse
    {
        $data = $request->validated();

        $oauthClient->forceFill([
            'name' => $data['name'],
            'provider' => $data['provider'] ?? null,
            'redirect_uris' => array_filter(array_map('trim', explode("\n", $data['redirect_uris'] ?? ''))),
            'grant_types' => $data['grant_types'] ?? [],
            'revoked' => (bool) ($data['revoked'] ?? false),
        ])->save();

        return Redirect::route('oauth-clients.index')
            ->with('success', 'Client OAuth mis à jour avec succès !');
    }

    /**
     * Toggle the revoked status of the client.
     */
    public function toggleRevoke(OauthClient $oauthClient): RedirectResponse
    {
        $oauthClient->forceFill(['revoked' => !$oauthClient->revoked])->save();

        $status = $oauthClient->revoked ? 'révoqué' : 'réactivé';

        return Redirect::back()
            ->with('success', "Client OAuth « {$oauthClient->name} » {$status} avec succès !");
    }

    /**
     * Regenerate the client secret.
     */
    public function regenerateSecret(OauthClient $oauthClient): RedirectResponse
    {
        $oauthClient->forceFill(['secret' => Str::random(40)])->save();

        return Redirect::route('oauth-clients.show', $oauthClient->id)
            ->with('success', 'Secret régénéré avec succès !')
            ->with('plain_secret', $oauthClient->plainSecret);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OauthClient $oauthClient): RedirectResponse
    {
        try {
            $oauthClient->tokens()->delete();
            $oauthClient->delete();

            return Redirect::route('oauth-clients.index')
                ->with('success', 'Client OAuth supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer ce client car il est lié à d'autres enregistrements.");
        }
    }
}
