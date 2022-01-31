<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 1/30/22
 * Time: 8:00 PM
 */

namespace App\Models;

use App\Models\AbstractModel;
use App\Models\Twitch;
use App\API\TwitchAPI;
use App\Models\User;

class Authentication extends AbstractModel
{
    public $config;

    function __construct( array $attributes = [] )
    {
        parent::__construct($attributes);
        $this->config = config('twitch.auth');
    }

    function validateUserToken( $access_token )
    {
        $parameters = [
        ];

        $initi_url = $this->initURL('validate','auth', $parameters, $access_token, false, true);

        $twitchAPI = new TwitchAPI('auth');
        $response = $twitchAPI->request('GET', $initi_url['URL'], $initi_url['client_parameters'], __FUNCTION__);

        if (isset($twitchAPI->getAllParams()['user_id']))
        {
            $twitch_user_id = $twitchAPI->getAllParams()['user_id'];

            $id             = auth()->user()->id;
            User::where([ 'id' => $id ])
                ->update(
                    [
                        'twitch_id'    => $twitch_user_id,
                        'twitch_login' => $twitchAPI->getAllParams()['login']
                    ]
                );

            $ts = new TwitchStreams();
            $ts->getUserFollows($id, $twitch_user_id, $access_token);
            $ts->getUserStreams($id, $twitch_user_id, $access_token);
        }

    }

    function requestAuthentication( $id, $scope, $code )
    {
        $twitchAPI = new TwitchAPI('auth');
        if ($scope)
        {
            $twitchAPI->setScope($scope);
        }

        $parameters = [
            'redirect_uri' => route('auth.access'),
            'grant_type'   => 'authorization_code',
            'code'         => $code,
        ];

        $initi_url = $this->initURL('token','auth', $parameters, '', true, false);

        $response = $twitchAPI->request('POST', $initi_url['URL'], [],__FUNCTION__);

        $twitch = new Twitch();

        $twitch->saveAccessToken(
            $code,
            $id,
            $this->config['client_id'],
            'twitch_access',
            $twitchAPI->getAccessToken(),
            $twitchAPI->getScope(),
            $twitchAPI->getExpires()
        );

        $twitch->saveRefreshToken(
            $code,
            $twitchAPI->getRefreshToken(),
            $twitchAPI->getExpires()
        );

        $this->validateUserToken(
            $twitchAPI->getAccessToken()
        );
    }

}
