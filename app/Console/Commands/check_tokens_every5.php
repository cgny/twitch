<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Twitch;
use Illuminate\Support\Facades\Log;

class check_tokens_every5 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:check_tokens_every5';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks access and refresh tokens every 5 minutes, to revoke or refresh';

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
        $twitch = new Twitch();
        $tokens = $twitch->checkRefreshToken();
        return $tokens;
    }
}
