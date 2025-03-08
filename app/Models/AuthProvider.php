<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthProvider extends Model
{
    protected $fillable = ['name', 'provider_class', 'provider_config', 'enabled', 'uuid'];
    protected $casts = [
        'provider_config' => 'object',
        'enabled' => 'boolean'
    ];
}
