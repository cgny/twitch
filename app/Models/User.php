<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Models\AccessToken;
use App\Traits\Validation;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Validation;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'twitch_id',
        'twitch_login',
        'twitch_follows',
        'twitch_streams',
        'twitch_tags',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function hasAccessToken()
    {
        return $this->hasMany(AccessToken::class, 'user_id', 'id')
            ->where('revoked','=', 0)
            ->selectRaw('oauth_access_tokens.*,oauth_access_tokens.access_token as latest_token');
    }
}
