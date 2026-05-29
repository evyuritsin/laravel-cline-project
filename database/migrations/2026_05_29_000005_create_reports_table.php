<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->onDelete('cascade');
            $table->string('pdf_path');
            $table->string('pdf_url')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->json('sent_to')->nullable(); // email addresses, telegram IDs
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamps();
            
            $table->index('inspection_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};