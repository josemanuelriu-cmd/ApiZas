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
        Schema::create('boardgame_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boardgame_id')->constrained('boardgames')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('types')->onDelete('cascade'); 
            $table->timestamps();
            $table->unique(['boardgame_id','type_id']);           
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boardgame_type');
    }
};
