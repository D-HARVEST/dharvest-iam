<?php

namespace App\Models;

use Laravel\Passport\Client;

/**
 * Class OauthClient
 *
 * Extends Passport's Client to inherit UUID handling, secret hashing,
 * JSON casts for redirect_uris/grant_types, and all Passport relationships.
 *
 * @property string $id
 * @property string|null $owner_type
 * @property int|null $owner_id
 * @property string $name
 * @property string|null $secret
 * @property string|null $provider
 * @property array $redirect_uris
 * @property array $grant_types
 * @property bool $revoked
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class OauthClient extends Client
{
    protected $perPage = 20;

    /**
     * Available grant types for the UI select.
     */
    public const GRANT_TYPES = [
        'authorization_code' => 'Authorization Code',
        'client_credentials' => 'Client Credentials',
        'password' => 'Password',
        'personal_access' => 'Personal Access',
        'implicit' => 'Implicit',
        'refresh_token' => 'Refresh Token',
        'urn:ietf:params:oauth:grant-type:device_code' => 'Device Code',
    ];

    /**
     * Scope a query to filter by a search term.
     */
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';

        return $query->where(function ($q) use ($like) {
            $q->where('name', 'like', $like)
                ->orWhere('provider', 'like', $like);
        });
    }
    static function getTableName()
    {
        return (new self)->getTable();
    }


}
