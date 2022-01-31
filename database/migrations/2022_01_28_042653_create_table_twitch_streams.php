<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTwitchStreams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitch_streams', function (Blueprint $table) {
            $table->id('ts_id');
            $table->bigInteger('ts_user_id');
            $table->bigInteger('ts_channel_id')->unique();
            $table->text('ts_channel_name');
            $table->text('ts_broadcast_name'); //display name
            $table->string('ts_broadcast_login',200); //username
            $table->bigInteger('ts_game_id');
            $table->text('ts_game_name');
            $table->bigInteger('ts_number_of_viewers')->nullable();
            $table->dateTimeTz('ts_start_date')->comment('UTC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitch_streams');
    }
}
