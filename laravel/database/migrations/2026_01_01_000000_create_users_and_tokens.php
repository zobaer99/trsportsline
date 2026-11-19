<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email', 191)->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        } elseif (!Schema::hasIndex('users', ['email'])) {
            Schema::table('users', function (Blueprint $table) { $table->string('email', 191)->change(); $table->unique('email'); });
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('tokenable_type', 191);
                $table->unsignedBigInteger('tokenable_id');
                $table->index(['tokenable_type', 'tokenable_id'], 'personal_access_tokens_tokenable_index');
                $table->text('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });
        } else {
            $hasTokenableIndex = Schema::hasIndex('personal_access_tokens', ['tokenable_type', 'tokenable_id']);
            $hasTokenIndex = Schema::hasIndex('personal_access_tokens', ['token']);
            $hasExpiryIndex = Schema::hasIndex('personal_access_tokens', ['expires_at']);
            Schema::table('personal_access_tokens', function (Blueprint $table) use ($hasTokenableIndex, $hasTokenIndex, $hasExpiryIndex) {
                $table->string('tokenable_type', 191)->change();
                if (!$hasTokenableIndex) $table->index(['tokenable_type', 'tokenable_id'], 'personal_access_tokens_tokenable_index');
                if (!$hasTokenIndex) $table->unique('token');
                if (!$hasExpiryIndex) $table->index('expires_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        // A Laravel users table may predate this app's migration history. Keep it on rollback.
    }
};
