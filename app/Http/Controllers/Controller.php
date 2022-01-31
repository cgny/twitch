<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Traits\Validation;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Validation;

    private $links, $contoller_access_token;

    function __construct()
    {

    }

    function getLinks()
    {
        $this->validateToken();
        $this->contoller_access_token = $this->getValidatedAccessToken();

        if(isset(Auth::user()->id))
        {
            $id     = Auth::user()->id;
            $twitch = new \App\Models\Twitch();
            $token  = $twitch->checkRefreshToken($id);
            if ($token === true)
            {
                $links[0]['route'] = 'twitch.stats';
                $links[0]['text']  = 'View Stats';
                $links[1]['route'] = 'twitch.streams';
                $links[1]['text']  = 'View Streams';
                $links[2]['route'] = 'twitch.load_streams';
                $links[2]['text']  = 'Load Streams';
            }
            else
            {
                $links[0]['route'] = 'auth.authorize';
                $links[0]['text']  = 'Authorize Twitch';
            }
        }
        else
        {
            $links[0]['route'] = 'auth.authorize';
            $links[0]['text']  = 'Authorize Twitch';
        }
        $this->links = $links;
        return $this->links;
    }

    function getControllerAccessToken()
    {
        return $this->contoller_access_token;
    }
}
