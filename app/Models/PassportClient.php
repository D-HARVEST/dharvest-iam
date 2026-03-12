<?php
namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passport\Client as BaseClient;

class PassportClient extends BaseClient
{
    /**
     * Détermine si le client doit ignorer l'invite d'autorisation.
     *
     * @return bool
     */
    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        return true;
    }
}
