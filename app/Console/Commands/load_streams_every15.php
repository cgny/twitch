<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TwitchStreams;

class load_streams_every15 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:load_streams_every15';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stream data every 15 min';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $twitch = new TwitchStreams();
        $twitch->loadStreams();
    }
}
