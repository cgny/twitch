<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends AbstractModel
{
    use HasFactory;

    protected $table = 'oauth_access_tokens';

    protected $fillable = [
        'id',
        'user_id',
        'client_id',
        'name',
        'scopes',
        'access_token',
        'revoked',
        'created_at',
        'expires_at',
    ];

    protected $attributes = [
        'id'           => '',
        'user_id'      => '',
        'client_id'    => '',
        'name'         => '',
        'scopes'       => '',
        'access_token' => '',
        'revoked'      => 0,
        'created_at'   => '',
        'expires_at'   => '',
    ];
}
