<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 1/28/22
 * Time: 6:33 PM
 */

namespace App\API;

use App\Models\Twitch;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class TwitchAPI{

    private $config = [], $client, $access_token, $refresh_token, $scope, $expires_in = false, $all_params = [], $client_parameters;

    function __construct($type = 'auth')
    {
        $this->config = config('twitch.'.$type);
    }

    function buildURL($base_url = true, $uri = 'authorize', $secret = false, $parameters = [], $scopes = [])
    {
        if($base_url)
        {
            $build_url = $this->config['base_url'];
        }
        else
        {
            $build_url = '';
        }
        $client_id = $this->config['client_id'];

        $build_url .= $uri; //endpoint
        $build_url .= "?client_id=" . $client_id;

        if($secret)
        {
            $build_url .= "&client_secret=" . $this->config['client_secret'];
        }

        foreach ($parameters as $p_name => $parameter)
        {
            $build_url .= "&$p_name=" . $parameter;
        }

        if($scopes)
        {
            $build_url .= "&scope=" . implode(' ',$scopes);
        }
        return $build_url;
    }

    private function client()
    {
        $this->client =  new Client(array( 'base_uri' => $this->config['base_url'] ));
    }

    private function handleResponse( $response )
    {
        $sc = $response->getStatusCode();
        $body = $this->parseJSON($response->getBody());
        //print_r( $body );
        if (isset($body->access_token))
        {
            $this->access_token = $body->access_token;
        }
        if (isset($body->refresh_token))
        {
            $this->refresh_token = $body->refresh_token;
        }
        if (isset($body->scope))
        {
            $this->scope = $body->scope;
        }
        if (isset($body->expires_in))
        {
            $this->expires_in = $body->expires_in;
        }

        foreach ($body as $name => $property)
        {
            $this->all_params[$name] = $property;
        }

        $return = $msg = false;
        $ctstat = $response->getHeader("X-Ct-Status");

        switch ($sc)
        {
            case 200:
            case 201:
                $return = true;
                break;
            case 400:
            case 500:
                $msg    = urldecode(implode(", ", $ctstat));
                //throw new \Exception("X-Ct-Status: " . $msg);
                break;
        }

        if($return)
        {
            return $body;
        }

        if (!empty($ctstat))
        {
            throw new \Exception("Code : ". $sc . "X-Ct-Status: " . $msg);
        }
    }

    function request( $method, $path, $params = array() , $function = '')
    {
        $this->client();
        //print_r([ $method, $path, $params, '__FUNCTION__' => $function ]);
        $response = $this->client->request($method, $path, $params);
        return $this->handleResponse($response);
    }

    function parseJSON( $json, $key = false )
    {
        $decoded = json_decode($json);
        if ($key)
        {
            return $decoded[$key];
        }
        return $decoded;
    }

    function getAccessToken()
    {
        return $this->access_token;
    }

    function getRefreshToken()
    {
        return $this->refresh_token;
    }

    function setScope($scope)
    {
        $this->scope = $scope;
    }

    function getScope()
    {
        return json_encode($this->scope);
    }

    function getExpires()
    {
        return $this->expires_in;
    }

    function getAllParams()
    {
        return $this->all_params;
    }

}
