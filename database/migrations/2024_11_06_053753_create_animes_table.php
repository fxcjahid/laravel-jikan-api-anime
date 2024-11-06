<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('mal_id')->unique();
            $table->json('titles');
            $table->json('slugs');
            $table->text('synopsis')->nullable();
            $table->string('type')->nullable();
            $table->integer('episodes')->nullable();
            $table->decimal('score', 3, 2)->nullable();
            $table->integer('rank')->nullable();
            $table->integer('popularity')->nullable();
            $table->string('status')->nullable();
            $table->date('aired_from')->nullable();
            $table->date('aired_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('animes');
    }
};
