<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_email_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Provider info
            $table->string('provider'); // gmail, outlook
            $table->string('email_address');
            $table->string('display_name')->nullable();

            // OAuth tokens (encrypted at model level)
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();

            // Sync tracking
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->string('sync_token')->nullable(); // Gmail history ID / Microsoft deltaLink
            $table->json('sync_errors')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'provider']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_email_accounts');
    }
};
