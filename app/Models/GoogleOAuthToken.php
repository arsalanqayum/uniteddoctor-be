<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleOAuthToken extends Model
{
    protected $table = 'google_oauth_tokens';

    protected $fillable = [
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $dates = [
        'token_expires_at',
    ];
}