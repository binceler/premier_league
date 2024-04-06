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
        Schema::create('team_strengths', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('team_id')->nullable();
            $table->boolean('is_home')->nullable()->default(0);
            $table->enum('strength', array('weak','average','strong'))->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_strengths');
    }
};
