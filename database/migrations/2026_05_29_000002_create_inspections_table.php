<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('type', ['move_in', 'move_out'])->default('move_in');
            $table->enum('status', ['draft', 'completed', 'sent'])->default('draft');
            $table->date('inspection_date');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'type']);
            $table->index('inspection_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};