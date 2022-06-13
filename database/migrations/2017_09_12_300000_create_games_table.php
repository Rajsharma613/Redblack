<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->string('code', 16)->unique();
            $table->string('start_code', 16);
            $table->string('name');
            $table->string('engine');
            $table->string('engine_version')->default('1.0');
            $table->integer('steam_app_id')->unsigned()->nullable();
            $table->string('steam_app_set_config')->nullable();
            $table->string('remote_repository')->nullable();
            $table->string('local_repository')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
