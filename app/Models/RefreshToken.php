<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends AbstractModel
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'oauth_refresh_tokens';

    protected $fillable = [
        'id',
        'access_token_id',
        'revoked',
        'expires_at',
    ];

    protected $attributes = [
        'id'              => '',
        'access_token_id' => '',
        'revoked'         => 0,
        'expires_at'      => '',
    ];


}
