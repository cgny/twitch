<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\API\TwitchAPI;
use Illuminate\Support\Facades\Log;

class Twitch extends AbstractModel
{

    function revokeAccessTokens( $user_id )
    {
        AccessToken::where([ 'user_id' => $user_id ])
            ->update(
                [
                    'oauth_access_tokens.revoked' => 1,
                ]
            );

        RefreshToken::where([ 'user_id' => $user_id ])
            ->join('oauth_access_tokens', 'access_token_id', '=', 'oauth_access_tokens.id')
            ->update(
                [
                    'oauth_refresh_tokens.revoked' => 1,
                ]
            );
    }

    function saveAccessToken( $code, $user_id, $client_id, $name, $token, $scopes, $expires )
    {
        DB::table('oauth_access_tokens')
            ->insert(
                [
                    'id'           => $code,
                    'user_id'      => $user_id,
                    'client_id'    => $client_id,
                    'name'         => $name,
                    'access_token' => $token,
                    'scopes'       => $scopes,
                    'revoked'      => false,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'expires_at'   => date('Y-m-d H:i:s', strtotime("+$expires SECONDS")),
                ]
            );
    }

    function saveRefreshToken( $code, $refresh_token, $expires )
    {
        DB::table('oauth_refresh_tokens')
            ->insert(
                [
                    'id'              => $refresh_token,
                    'access_token_id' => $code,
                    'revoked'         => 0,
                    'expires_at'      => date('Y-m-d H:i:s', strtotime("+$expires SECONDS")),
                ]
            );
    }

    function getTokens($user_id='')
    {
        $where = [ 'oauth_access_tokens.revoked' => 0 ];
        if ($user_id)
        {
            $where['oauth_access_tokens.user_id'] = $user_id;
        }

        return DB::table('oauth_access_tokens')
            ->selectRaw('users.id as user_id, users.twitch_id, oauth_access_tokens.*,
            oauth_refresh_tokens.*,
            oauth_access_tokens.id as access_token_id,
            oauth_refresh_tokens.id as refresh_token_id,
            oauth_access_tokens.revoked as token_revoked,
            oauth_access_tokens.expires_at as token_expires,
             oauth_refresh_tokens.id as refresh_token,
             oauth_refresh_tokens.expires_at as refresh_expires'
            )
            ->where($where)
            ->join('oauth_refresh_tokens', 'oauth_access_tokens.id', '=', 'oauth_refresh_tokens.access_token_id')
            ->join('users', 'users.id', '=', 'oauth_access_tokens.user_id')
            ->orderBy('oauth_access_tokens.created_at', 'desc')
            ->get();

    }

    function checkRefreshToken( $user_id = '' )
    {
        $token_data = $this->getTokens($user_id);

        if ($token_data->count() == 0)
        {
            return -1;
        }
        else
        {
            $ts = new TwitchStreams();
            if ($user_id)
            {
                if (isset($token_data[0]))
                {
                    $user_token      = $token_data[0];
                    $token_expires   = strtotime($user_token->token_expires);
                    $refresh_expires = strtotime($user_token->refresh_expires);
                    if ($token_expires < time())  // access token expired
                    {
                        $this->revokeToken($user_token->access_token, $user_token->refresh_token_id); //revoke with twitch
                        //new acccess token required
                        return -1;
                    }
                    else
                    {
                        $ts->getUserFollows($user_id, $user_token->twitch_id, $user_token->access_token);
                        $ts->getUserStreams($user_id, $user_token->twitch_id, $user_token->access_token);
                        if ($refresh_expires < time()) // refresh token expired
                        {
                            //refresh attempt
                            return $this->autoRunRefreshToken($user_token->access_token, $user_token->refresh_token);
                        }
                        else
                        {
                            //still good
                            return true;
                        }
                    }
                }
            }
            else
            {
                $refreshed = [];
                foreach ($token_data as $token)
                {
                    $token_expires   = strtotime($token->token_expires);
                    $refresh_expires = strtotime($token->refresh_expires);
                    if ($token_expires < time())  // access token expired
                    {
                        $this->revokeToken($token->access_token, $token->refresh_token_id); //revoke with twitch
                        //new acccess token required
                    }
                    else
                    {
                        $ts->getUserFollows($token->user_id, $token->twitch_id, $token->access_token);
                        $ts->getUserStreams($token->user_id, $token->twitch_id, $token->access_token);
                        if ($refresh_expires < time()) // refresh token
                        {
                            //refresh attempt
                            $refreshed[] = $this->autoRunRefreshToken($token->access_token, $token->refresh_token);
                        }
                    }
                }
                return $refreshed;
            }
        }
    }

    function autoRunRefreshToken( $access_token, $refresh_token )
    {
        $parameters = [
            'access_token'  => $access_token,
            'refresh_token' => $refresh_token,
            'grant_type'    => 'refresh_token',
        ];

        $initi_url = $this->initURL('token', 'auth', $parameters, '', true, true);

        $twitchAPI = new TwitchAPI('auth');
        $response  = $twitchAPI->request('POST', $initi_url['URL']);

        if ($twitchAPI->getExpires())
        {
            $exp = date('Y-m-d H:i:s', strtotime("+" . $twitchAPI->getExpires() . " SECONDS"));
            RefreshToken::find($refresh_token)->update(
                [
                    'expires_at' => $exp,
                ]
            );
            return $refresh_token;
        }
        else
        {
            $this->revokeToken($access_token, $refresh_token);
            return -1;
        }

        /**
         * POST https://id.twitch.tv/oauth2/token
         * --data-urlencode
         * ?grant_type=refresh_token
         * &refresh_token=<your refresh token>
         * &client_id=<your client ID>
         * &client_secret=<your client secret>
         */
    }

    function revokeToken( $access_token, $refresh_token )
    {
        AccessToken::where(
            [
                'access_token' => $access_token
            ]
        )
            ->update(
                [
                    'revoked' => 1,
                ]
            );

        RefreshToken::where([
                'id' => $refresh_token ]
        )
            ->update(
                [
                    'revoked' => 1,
                ]
            );

        $parameters = [
            'token' => $access_token
        ];


        $initi_url = $this->initURL('revoke', 'auth', $parameters, $access_token, false, true);

        $twitchAPI = new TwitchAPI('auth');
        $response  = $twitchAPI->request('GET', $initi_url['URL'], $initi_url['client_parameters'], __FUNCTION__);


    }

    function runUpdateFollows()
    {
        $token_data = $this->getTokens();
    }
}
