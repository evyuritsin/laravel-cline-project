<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('elements')->nullable(); // walls, floor, ceiling, windows, doors
            $table->timestamps();
            
            $table->index('inspection_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};