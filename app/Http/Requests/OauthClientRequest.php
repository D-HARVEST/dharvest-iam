<?php

namespace App\Http\Requests;

use App\Models\OauthClient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OauthClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'redirect_uris' => 'nullable|string',
            'grant_types' => 'required|array|min:1',
            'grant_types.*' => ['required', 'string', Rule::in(array_keys(OauthClient::GRANT_TYPES))],
        ];

        // Only on create
        if ($this->isMethod('POST')) {
            $rules['confidential'] = 'nullable|boolean';
        }

        // Only on update
        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            $rules['revoked'] = 'nullable|boolean';
        }

        return $rules;
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom du client',
            'redirect_uris' => 'URIs de redirection',
            'grant_types' => 'types de grant',
            'grant_types.*' => 'type de grant',
            'provider' => 'provider',
        ];
    }
}
