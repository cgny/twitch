<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwitchFieldsToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('twitch_id')->nullable()->after('remember_token');
            $table->string('twitch_login',80)->nullable()->after('twitch_id');
            $table->longText('twitch_follows')->nullable()->default('[]')->comment('JSON IDS')->after('twitch_login');
            $table->longText('twitch_streams')->nullable()->default('[]')->comment('JSON IDS')->after('twitch_follows');
            $table->longText('twitch_tags')->nullable()->default('[]')->comment('JSON IDS')->after('twitch_streams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('twitch_id');
            $table->dropColumn('twitch_login');
            $table->dropColumn('twitch_follows');
            $table->dropColumn('twitch_streams');
            $table->dropColumn('twitch_tags');
        });
    }
}
