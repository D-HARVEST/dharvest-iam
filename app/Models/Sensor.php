<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Sensor extends Model
{
    use HasApiTokens; // Indispensable pour générer des tokens

    protected $fillable = [
        'chip_id',
        'last_token_generated_at'
    ];
}
