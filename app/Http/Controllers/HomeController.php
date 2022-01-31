<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Twitch;
use App\Models\TwitchStreams;

class HomeController extends Controller
{
    function index( Request $request )
    {
        $access = false;
        $this->validateToken();

        if ($this->getValidatedAccessToken())
        {
            $follows = (array) json_decode(auth()->user()->twitch_follows);
            $tags = (array) json_decode(auth()->user()->twitch_tags);

            $ts              = new TwitchStreams();
            $follows_flatten = [];
            \App\Models\AbstractModel::flattenArray($follows, $follows_flatten);

            $game_streams = $ts->getViewersGroupedByGame(1000);
            $game_streams = $ts->checkUserFollows($game_streams,$follows_flatten);


            $top_100 = $game_streams->slice(0, 99);

            $total_avg_viewers   = $ts->getAVGViews();
            $streamsByHour       = $ts->getStreamersByStartHour();
            $users_followed_data = []; //$ts->getFollows($this->getValidatedAccessToken());
            //sleep(3);
            $users_1000_shared = []; //$ts->top1000tagsShared($this->getValidatedAccessToken());
            $access            = true;
        }
        else
        {
            $streamsByHour     = $game_streams = $top_100 = $users_followed_data = $users_1000_shared = [];
            $total_avg_viewers = 0;
        }

        $data = [
            'total_avg_viewers'  => $total_avg_viewers,
            'top_1000_keys'      => [ 'ts_channel_name', 'ts_game_id', 'ts_game_name', 'following', 'total_views', 'share_tags' ],
            'top_1000_data'      => $game_streams,
            'top_100_keys'       => [ 'ts_channel_name', 'ts_game_id', 'ts_game_name', 'total_views' ],
            'top_100_data'       => $top_100,
            'streams_start_keys' => [ 'total_streams', 'start_date' ],
            'streams_start_data' => $streamsByHour,
            'user_follow_data'   => $users_followed_data,
            'users_1000_shared'  => $users_1000_shared,
            'access'             => $access
        ];

        return view('dashboard', $data);
    }

    function dashboard( Request $request )
    {
        return $this->index($request);
    }
}
