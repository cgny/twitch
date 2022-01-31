<?php

namespace App\Http\Controllers;

use App\API\TwitchAPI;
use App\Models\Twitch;
use App\Models\TwitchStreams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TwitchController extends Controller
{

    function streams()
    {
        $id = auth()->user()->id;
        $twitch = new TwitchStreams();

        echo "<pre>";

        $follows = $twitch->formatFollows( $this->getValidatedAccessToken() );

    }

}
