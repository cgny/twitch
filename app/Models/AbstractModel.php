<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 1/30/22
 * Time: 5:34 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Validation;
use App\API\TwitchAPI;

abstract class AbstractModel extends Model
{
    use Validation, HasFactory;

    function initURL( $uri, $type = 'api', $parameters = [], $access_token = '', $secret = false, $base_url = true)
    {
        $this->validateToken();

        $twitchAPI = new TwitchAPI($type);
        $url       = $twitchAPI->buildURL(
            $base_url,
            $uri,
            $secret,
            $parameters
        );

        $access_token = ($access_token) ? $access_token : $this->getValidatedAccessToken();

        $client_parameters = [];
        if($access_token)
        {
            $client_parameters = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'Client-Id'     => getenv('TWITCH_CLIENT'),
                ]
            ];
        }

        return [
            'URL'               => $url,
            'client_parameters' => $client_parameters,
        ];

    }

    static function filterKeys($key)
    {
        return ucfirst( str_ireplace('_',' ', str_ireplace('ts_','',$key) ) );
    }

    static function flattenArray($from_array,&$to_array)
    {
        array_walk_recursive($from_array, function ( $v ) use ( &$to_array )
        {
            $to_array[] = $v;
        }
        );
    }

}
