<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\API\TwitchAPI;
use Illuminate\Support\Facades\DB;

class TwitchStreams extends AbstractModel
{
    use HasFactory;

    private $pages = [];

    protected $table = 'twitch_streams';

    protected $primaryKey = 'ts_id';

    protected $fillable = [
        'ts_user_id',
        'ts_channel_id',
        'ts_channel_name',
        'ts_broadcast_name',
        'ts_broadcast_login',
        'ts_game_id',
        'ts_game_name',
        'ts_number_of_viewers',
        'ts_tags',
        'ts_start_date',
        'created_at',
    ];

    protected $attributes = [
        'ts_user_id'           => '',
        'ts_channel_id'        => '',
        'ts_channel_name'      => '',
        'ts_broadcast_name'    => '',
        'ts_broadcast_login'   => '',
        'ts_game_id'           => '',
        'ts_game_name'         => '',
        'ts_number_of_viewers' => '',
        'ts_tags'              => '',
        'ts_start_date'        => 0,
        'created_at'           => '',
    ];

    function clearChannels()
    {
        self::where('ts_id', '>', 0)->delete();
    }

    function loadStreams( $access_token = '' )
    {
        $this->clearChannels();

        $parameters = [
            'first' => 100,
            'after' => ''
        ];

        $initi_url = $this->initURL('streams', 'api', $parameters, $access_token, false, true);

        $twitchAPI = new TwitchAPI('api');
        $response  = $twitchAPI->request('GET', $initi_url['URL'], $initi_url['client_parameters']);

        $counter = 0;
        if (isset($twitchAPI->getAllParams()['data']))
        {
            foreach ($twitchAPI->getAllParams()['data'] as $stream)
            {
                if (empty($stream->game_id) || $stream->type != 'live')
                {
                    continue;
                }

                $started = new \DateTime($stream->started_at);
                $started->setTimezone(new \DateTimeZone('UTC'));
                $started_date = $started->format('Y-m-d H:i:s');

                $found_stream = TwitchStreams::where('ts_channel_id', $stream->id)->first();
                if (isset($found_stream->ts_id))
                {
                    $found_stream->ts_channel_id        = $stream->id;
                    $found_stream->ts_user_id           = $stream->user_id;
                    $found_stream->ts_channel_name      = $stream->title;
                    $found_stream->ts_broadcast_name    = $stream->user_name;
                    $found_stream->ts_broadcast_login   = $stream->user_login;
                    $found_stream->ts_game_id           = $stream->game_id;
                    $found_stream->ts_game_name         = $stream->game_name;
                    $found_stream->ts_number_of_viewers = $stream->viewer_count;
                    $found_stream->ts_tags              = json_encode($stream->tag_ids);
                    $found_stream->ts_start_date        = $started_date;
                    $found_stream->update();
                }
                else
                {
                    $tstream                       = new TwitchStreams();
                    $tstream->ts_channel_id        = $stream->id;
                    $tstream->ts_user_id           = $stream->user_id;
                    $tstream->ts_channel_name      = $stream->title;
                    $tstream->ts_broadcast_name    = $stream->user_name;
                    $tstream->ts_broadcast_login   = $stream->user_login;
                    $tstream->ts_game_id           = $stream->game_id;
                    $tstream->ts_game_name         = $stream->game_name;
                    $tstream->ts_number_of_viewers = $stream->viewer_count;
                    $tstream->ts_tags              = json_encode($stream->tag_ids);
                    $tstream->ts_start_date        = $started_date;
                    $tstream->save();
                }
                $counter++;
            }
            /*

            */
            $x = 1;
            if (isset($twitchAPI->getAllParams()['pagination']->cursor))
            {
                $this->pages[0] = $twitchAPI->getAllParams()['pagination']->cursor;
                do
                {
                    $parameters['after'] = $this->pages[($x - 1)];

                    $initi_url = $this->initURL('streams', 'api', $parameters, $access_token, false, true);

                    $response = $twitchAPI->request('GET', $initi_url['URL'], $initi_url['client_parameters']);
                    if (isset($twitchAPI->getAllParams()['data']))
                    {
                        $streams = count($twitchAPI->getAllParams()['data']);
                    }
                    else
                    {
                        $streams = 0;
                    }

                    if (isset($response->pagination))
                    {
                        $this->pages[$x] = $response->pagination->cursor;
                        foreach ($twitchAPI->getAllParams()['data'] as $stream)
                        {
                            if (empty($stream->game_id) || $stream->type != 'live')
                            {
                                continue;
                            }

                            $started = new \DateTime($stream->started_at);
                            $started->setTimezone(new \DateTimeZone('UTC'));
                            $started_date = $started->format('Y-m-d H:i:s');

                            $found_stream = TwitchStreams::where('ts_channel_id', $stream->id)->first();
                            if (isset($found_stream->ts_id))
                            {
                                $found_stream->ts_channel_id        = $stream->id;
                                $found_stream->ts_user_id           = $stream->user_id;
                                $found_stream->ts_channel_name      = $stream->title;
                                $found_stream->ts_broadcast_name    = $stream->user_name;
                                $found_stream->ts_broadcast_login   = $stream->user_login;
                                $found_stream->ts_game_id           = $stream->game_id;
                                $found_stream->ts_game_name         = $stream->game_name;
                                $found_stream->ts_number_of_viewers = $stream->viewer_count;
                                $found_stream->ts_tags              = json_encode($stream->tag_ids);
                                $found_stream->ts_start_date        = $started_date;
                                $found_stream->update();
                            }
                            else
                            {
                                $tstream                       = new TwitchStreams();
                                $tstream->ts_channel_id        = $stream->id;
                                $tstream->ts_user_id           = $stream->user_id;
                                $tstream->ts_channel_name      = $stream->title;
                                $tstream->ts_broadcast_name    = $stream->user_name;
                                $tstream->ts_broadcast_login   = $stream->user_login;
                                $tstream->ts_game_id           = $stream->game_id;
                                $tstream->ts_game_name         = $stream->game_name;
                                $tstream->ts_number_of_viewers = $stream->viewer_count;
                                $tstream->ts_tags              = json_encode($stream->tag_ids);
                                $tstream->ts_start_date        = $started_date;
                                $tstream->save();
                            }
                            $counter++;
                        }
                    }
                    $x++;
                } while ($streams > 0 && $counter < 1000);
            }
        }
    }

    /**
     * gets tags from user streams
     *
     * @param $id
     * @param $twitch_id
     * @param $access_token
     */

    function getUserStreams( $id, $twitch_id, $access_token )
    {

        $parameters = [
            'user_id' => $twitch_id
        ];

        $initi_url = $this->initURL('streams', 'api', $parameters, $access_token, false, true);


        $twitchAPI = new TwitchAPI('api');
        $response  = $twitchAPI->request('GET', $initi_url['URL'], $initi_url['client_parameters'], __FUNCTION__);

        $sorted_tags = [];
        $streams     = $tags = [];
        if (isset($twitchAPI->getAllParams()['data']))
        {
            foreach ($twitchAPI->getAllParams()['data'] as $stream)
            {
                $tags[]    = $stream->tag_ids;
                $streams[] = $stream->id;
            }
        }

        AbstractModel::flattenArray($tags, $sorted_tags);
        $sorted_tags = array_unique($sorted_tags);

        User::where([ 'id' => $id ])
            ->update(
                [
                    'twitch_tags'    => $sorted_tags,
                    'twitch_streams' => $streams,
                ]
            );
    }

    function checkUserFollows($game_streams,$follows_flatten,$tags)
    {
        foreach ($game_streams as $game_stream)
        {
            $game_stream->following = "<button class='btn btn-warning' data-id='" . $game_stream->ts_channel_id . "'>No</button>";
            if (in_array($game_stream->ts_channel_id, $follows_flatten))
            {
                $game_stream->following = "<button class='btn btn-success' data-id='" . $game_stream->ts_channel_id . "'>Yes</button>";
            }

            $steam_tags              = (array) json_decode($game_stream->ts_tags);
            $shared                  = array_intersect($steam_tags, $tags);
            $game_stream->share_tags = "<button class='btn btn-warning' data-id='" . $game_stream->ts_channel_id . "'>No</button>";
            if (count($shared) > 0)
            {
                $style                   = (in_array($game_stream->ts_channel_id, $follows_flatten)) ? 'success' : 'primary';
                $game_stream->share_tags = "<button class='btn btn-$style' data-id='" . $game_stream->ts_channel_id . "'>Yes</button>";
            }
        }
        return $game_streams;
    }

    function getUserFollows( $id, $twitch_id, $access_token )
    {

        $parameters = [
            'user_id' => $twitch_id
        ];

        $initi_url = $this->initURL('streams/followed', 'api', $parameters, $access_token, false, true);

        $twitchAPI = new TwitchAPI('api');
        $response  = $twitchAPI->request('GET', $initi_url['URL'], $initi_url['client_parameters'], __FUNCTION__);

        $follows = [];
        if (isset($twitchAPI->getAllParams()['data']))
        {
            foreach ($twitchAPI->getAllParams()['data'] as $follow)
            {
                $follows[$follow->user_id][] = $follow->id;
            }
        }

        User::where([ 'id' => $id ])
            ->update(
                [
                    'twitch_follows' => $follows,
                ]
            );
    }

    function getFollowedStreams( $twitch_follows, $access_token = '', $use_keys = true )
    {
        if ($use_keys == false)
        {
            $users = array_values(array_filter($twitch_follows));
        }
        else
        {
            $users = array_keys($twitch_follows); //get user IDs from follows
        }

        if (count($users) == 0)
        {
            return [];
        }

        $parameters = [
            'user_id' => implode('&user_id=', $users),
        ];

        $initi_url = $this->initURL('streams', 'api', $parameters, $access_token, false, true);

        $twitchAPI = new TwitchAPI();
        $response  = $twitchAPI->request('GET', $initi_url['URL'], $initi_url['client_parameters'], __FUNCTION__);

        return $response->data;

    }

    function getFollows( $access_token = '' )
    {
        $twitch_follows = (array) json_decode(auth()->user()->twitch_follows);
        $follows        = $this->getFollowedStreams($twitch_follows, $access_token);
        $user_tags      = (array) json_decode(auth()->user()->twitch_tags);


        $sorted_streams = [];
        AbstractModel::flattenArray($twitch_follows, $sorted_streams);

        $formatted_follow = [];
        foreach ($follows as $follow)
        {
            if (!in_array($follow->id, $sorted_streams))
            {
                continue;
            }

            $stream_tags = $follow->tag_ids;
            $shared_tags = array_intersect($stream_tags, $user_tags);
            if (count($shared_tags) == 0)
            {
                continue;
            }
            $formatted_follow[$follow->id]['title']       = $follow->title;
            $formatted_follow[$follow->id]['needed_1000'] = ($follow->viewer_count < 1000) ? (1000 - $follow->viewer_count) : 0;
            $formatted_follow[$follow->id]['viewers']     = $follow->viewer_count;
            $formatted_follow[$follow->id]['tags']        = $this->getTagsInfo($shared_tags);
            $formatted_follow[$follow->id]['shared_tags'] = $this->findSharedTags($shared_tags, $formatted_follow[$follow->id]['tags']);
            sleep(2);
        }
        return $formatted_follow;
    }

    function top1000tagsShared( $access_token = '' )
    {
        $formatted_follow   = [];
        $user_tags          = (array) json_decode(auth()->user()->twitch_tags);
        $twitch_shared_tags = $this->getSharedTagsFromStreams($user_tags, 1000);

        $twitch_tags_array = $twitch_shared_tags;

        //AbstractModel::flattenArray($twitch_follows, $sorted_streams);

        $all_tags = [];

        foreach ($twitch_tags_array as $stream)
        {
            $stream_tags = (array) json_decode($stream->ts_tags);
            $shared_tags = array_intersect($stream_tags, $user_tags);
            if (count($shared_tags) == 0)
            {
                continue;
            }
            $formatted_follow[$stream->id]['title']       = $stream->ts_broadcast_name;
            $formatted_follow[$stream->id]['needed_1000'] = ($stream->ts_number_of_viewers < 1000) ? (1000 - $stream->ts_number_of_viewers) : 0;
            $formatted_follow[$stream->id]['viewers']     = $stream->ts_number_of_viewers;
            $formatted_follow[$stream->id]['tags']        = $this->getTagsInfo($shared_tags);
            $formatted_follow[$stream->id]['shared_tags'] = $this->findSharedTags($shared_tags, $formatted_follow[$stream->id]['tags']);
            sleep(2);
        }


        return $formatted_follow;
    }

    function findSharedTags( $user_tags, $stream_tags )
    {
        $shared_tags = [];
        if (count($user_tags) == 0)
        {
            return $shared_tags;
        }
        foreach ($stream_tags->data as $stream_tag)
        {
            if (in_array($stream_tag->tag_id, $user_tags))
            {
                $shared_tags[] = $stream_tag;
            }
        }
        return $shared_tags;
    }

    function getTagsInfo( $tags = [], $access_token = '' )
    {
        if (empty($tags))
        {
            return [];
        }
        if (count($tags) == 0)
        {
            return [];
        }
        $parameters = [
            'tag_id' => implode('&tag_id=', $tags),
        ];

        $initi_url = $this->initURL('tags/streams', 'api', $parameters, $access_token, false, true);

        $twitchAPI = new TwitchAPI();
        return $twitchAPI->request('GET', $initi_url['URL'], $initi_url['client_parameters'], __FUNCTION__);
    }

    function getStreams( $orderBy )
    {
        $s = self::where('');

        if ($orderBy == "views")
        {
            $s->orderBy('ts_number_of_viewers', 'desc');
        }
    }

    function getViewersGroupedByGame( $limit = '' )
    {
        $s = self::selectRaw('ts_tags,ts_user_id,ts_channel_name,ts_channel_id,ts_game_id,ts_game_name, SUM(ts_number_of_viewers) as total_views')
            ->groupBy([ 'ts_user_id', 'ts_channel_name', 'ts_channel_id', 'ts_game_id', 'ts_game_name' ])
            ->orderBy('total_views', 'desc');

        if ($limit)
        {
            $s->limit($limit);
        }

        return $s->get();
    }

    function getSharedTagsFromStreams( $tags, $limit = '' )
    {
        $where_tag = implode("%' OR ts_tags LIKE '%", $tags) . "%'";

        $s = self::selectRaw('ts_tags,ts_user_id,ts_channel_name,ts_channel_id,ts_game_id,ts_game_name, SUM(ts_number_of_viewers) as total_views')
            ->whereRaw("( ts_tags LIKE '%$where_tag ) ")
            ->groupBy([ 'ts_user_id', 'ts_channel_name', 'ts_channel_id', 'ts_game_id', 'ts_game_name' ])
            ->orderBy('total_views', 'desc');

        if ($limit)
        {
            $s->limit($limit);
        }

        return $s->get();
    }

    function getStreamersByStartHour()
    {
        return self::selectRaw('COUNT(*) as total_streams, date_format( ts_start_date, \'%Y-%m-%d %H\' ) as start_date')
            ->groupBy('start_date')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    function getStreamersGroupedByStart()
    {

    }

    function getAVGViews()
    {
        $avg = self::selectRaw('AVG(ts_number_of_viewers) as avg_views')
            ->first();
        if (isset($avg->avg_views))
        {
            return $avg->avg_views;
        }
        return 0;
    }

}
