<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Twitch;
use App\Models\TwitchStreams;

class HomeController extends Controller
{
    function index()
    {

        $this->validateToken();
        if($this->getValidatedAccessToken())
        {

            $follows = (array) json_decode(auth()->user()->twitch_follows);

            $ts = new TwitchStreams();

            $game_streams = $ts->getViewersGroupedByGame(1000);
            foreach ($game_streams as $game_stream)
            {
                $game_stream->following = "<button class='btn btn-warning' data-id='" . $game_stream->ts_channel_id . "'>No</button>";
                if (in_array($game_stream->ts_channel_id, $follows))
                {
                    $game_stream->following = "<button class='btn btn-success' data-id='" . $game_stream->ts_channel_id . "'>Yes</button>";
                }
            }
            $top_100 = $game_streams->slice(0, 99);

            $total_avg_viewers   = $ts->getAVGViews();
            $streamsByHour       = $ts->getStreamersByStartHour();
            $users_followed_data = $ts->formatFollows($this->getValidatedAccessToken());
        }
        else
        {
            $streamsByHour = $game_streams = $top_100 = $users_followed_data = [];
            $total_avg_viewers = 0;
        }

        $data = [
            'total_avg_viewers'  => $total_avg_viewers,
            'top_1000_keys'      => [ 'ts_channel_name', 'ts_game_id', 'ts_game_name', 'following', 'total_views' ],
            'top_1000_data'      => $game_streams,
            'top_100_keys'       => [ 'ts_game_id', 'ts_game_name', 'total_views' ],
            'top_100_data'       => $top_100,
            'streams_start_keys' => [ 'total_streams', 'start_date' ],
            'streams_start_data' => $streamsByHour,
            'user_follow_data'   => $users_followed_data
        ];

        return view('dashboard', $data);
    }
}
