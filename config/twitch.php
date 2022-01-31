<?php

return [

    'auth' => [
        'base_url'      => 'https://id.twitch.tv/oauth2/',
        'client_id'     => getenv('TWITCH_CLIENT'),
        'client_secret' => getenv('TWITCH_SECRET'),
        'redirect_uri'  => '',
    ],
    'api'  => [
        'base_url'      => 'https://api.twitch.tv/helix/',
        'client_id'     => getenv('TWITCH_CLIENT'),
        'client_secret' => getenv('TWITCH_SECRET'),
        'redirect_uri'  => '',
    ]
];
