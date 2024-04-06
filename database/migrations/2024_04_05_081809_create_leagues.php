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
        Schema::create('league', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('team_id')->nullable();
            $table->integer('points')->nullable();
            $table->integer('played')->nullable();
            $table->integer('won')->nullable();
            $table->integer('lose')->nullable();
            $table->integer('draw')->nullable();
            $table->integer('goal_drawn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league');
    }
};
