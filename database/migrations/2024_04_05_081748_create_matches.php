<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('match_games', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('week_id')->nullable();
            $table->integer('home')->nullable()->comment('home team_id');
            $table->integer('away')->nullable()->comment('away team_id');
            $table->integer('home_goal')->default(0);
            $table->integer('away_goal')->default(0);
            $table->boolean('played')->default(0);
            $table->unique(['week_id','away'], 'week_id');
            $table->unique(['week_id','home'], 'week_id_2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_games');
    }
};
