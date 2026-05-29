<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'telegram_id')) {
                $table->bigInteger('telegram_id')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'tier')) {
                $table->enum('tier', ['free', 'starter', 'pro', 'premium'])->default('free')->after('password');
            }
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('tier');
            }
            if (!Schema::hasColumn('users', 'metadata')) {
                $table->json('metadata')->nullable()->after('avatar_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'telegram_id')) {
                $table->dropColumn('telegram_id');
            }
            if (Schema::hasColumn('users', 'tier')) {
                $table->dropColumn('tier');
            }
            if (Schema::hasColumn('users', 'avatar_url')) {
                $table->dropColumn('avatar_url');
            }
            if (Schema::hasColumn('users', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};