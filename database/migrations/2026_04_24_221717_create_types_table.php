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
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['abstracto', 'ameritrash', 'cartas', 'clásico', 'colocación de trabajadores', 'construcción de mazos', 'cooperativo', 'dados', 'escape room', 'estrategia', 'eurogame', 'familiar', 'filler', 'infantil', 'investigacion', 'mayorias', 'narrativo', 'party', 'roles ocultos', 'wargame']);
            $table->mediumText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};
