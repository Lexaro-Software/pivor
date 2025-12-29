<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->foreignId('email_message_id')->nullable()->after('contact_id')
                ->constrained('email_messages')->nullOnDelete();
            $table->string('email_from')->nullable()->after('content');
            $table->json('email_to')->nullable()->after('email_from');
            $table->json('email_cc')->nullable()->after('email_to');
            $table->text('email_body_html')->nullable()->after('email_cc');
        });
    }

    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->dropForeign(['email_message_id']);
            $table->dropColumn([
                'email_message_id',
                'email_from',
                'email_to',
                'email_cc',
                'email_body_html',
            ]);
        });
    }
};
