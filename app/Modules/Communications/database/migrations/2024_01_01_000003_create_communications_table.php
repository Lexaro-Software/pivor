<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Type of communication
            $table->enum('type', ['email', 'phone', 'meeting', 'note', 'task'])->default('note');
            $table->enum('direction', ['inbound', 'outbound', 'internal'])->default('internal');

            // Content
            $table->string('subject');
            $table->text('content')->nullable();

            // Relationships
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();

            // For tasks and follow-ups
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // User tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            // Status
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('completed');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('client_id');
            $table->index('contact_id');
            $table->index('created_by');
            $table->index('assigned_to');
            $table->index('due_at');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
