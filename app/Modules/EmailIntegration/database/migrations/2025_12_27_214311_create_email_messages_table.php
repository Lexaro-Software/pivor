<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_email_account_id')->constrained()->cascadeOnDelete();

            // External references
            $table->string('external_id'); // Gmail message ID or Microsoft message ID
            $table->string('thread_id')->nullable();

            // Link to CRM communication (created after import)
            $table->foreignId('communication_id')->nullable()->constrained()->nullOnDelete();

            // Email metadata
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->json('to_emails');
            $table->json('cc_emails')->nullable();
            $table->string('subject')->nullable();
            $table->text('body_html')->nullable();
            $table->text('body_text')->nullable();
            $table->json('attachments')->nullable();

            // Direction and status
            $table->string('direction'); // inbound, outbound
            $table->boolean('is_read')->default(false);
            $table->timestamp('email_date');

            // Contact matching
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->unique(['user_email_account_id', 'external_id']);
            $table->index('contact_id');
            $table->index('client_id');
            $table->index('email_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_messages');
    }
};
