<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Twitch;
use App\API\TwitchAPI;
use App\Models\Authentication;

class OAuthController extends Controller
{

    function authorizeAccess()
    {

        $id     = auth()->user()->id;
        $twitch = new Twitch();
        $twitch->revokeAccessTokens($id); //clear all tokens

        $scopes[] = 'channel:read:subscriptions';
        $scopes[] = 'channel:read:polls';
        $scopes[] = 'channel:manage:broadcast';
        $scopes[] = 'analytics:read:extensions';
        $scopes[] = 'user:read:email';
        $scopes[] = 'user:read:follows';

        $parameters = [
            'redirect_uri'  => route('auth.access'),
            'response_type' => 'code',
        ];

        $twitchAPI = new TwitchAPI('auth');
        $url       = $twitchAPI->buildUrl(
            true,
            'authorize',
            '',
            $parameters,
            $scopes
        );

        return redirect($url);
    }

    function access( Request $request )
    {
        $code  = $request->input('code');
        $scope = $request->input('scope');
        $id    = auth()->user()->id;

        $auth = new Authentication();
        $auth->requestAuthentication($id,$scope,$code);

        return redirect(route('dashboard'));
    }
}
