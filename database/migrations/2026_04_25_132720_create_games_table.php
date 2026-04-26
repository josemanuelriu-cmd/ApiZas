<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zassession_id')->constrained('zassessions')->onDelete('cascade');
            $table->foreignId('boardgame_id')->constrained('boardgames')->onDelete('cascade');
            $table->foreignId('host_user_id')->constrained('users');
            $table->integer('max_players')->nullable();
            $table->time('start_time');
            $table->enum('status', ['open','limited','playing','finished'])->default('open');
            $table->boolean('necesary_know_how');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
