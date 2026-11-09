<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'identity_type')) {
                $table->string('identity_type', 30)->default('KTP')->after('phone');
            }
            if (! Schema::hasColumn('users', 'identity_number')) {
                $table->string('identity_number', 30)->nullable()->index()->after('identity_type');
            }
            if (! Schema::hasColumn('users', 'identity_card_path')) {
                $table->string('identity_card_path')->nullable()->after('identity_number');
            }
        });

        Schema::table('loans', function (Blueprint $table) {
            if (! Schema::hasColumn('loans', 'identity_verified')) {
                $table->boolean('identity_verified')->default(false)->after('status');
            }
            if (! Schema::hasColumn('loans', 'due_notified_at')) {
                $table->dateTime('due_notified_at')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('loans', 'due_notification_note')) {
                $table->string('due_notification_note', 255)->nullable()->after('due_notified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['identity_verified', 'due_notified_at', 'due_notification_note']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['identity_type', 'identity_number', 'identity_card_path']);
        });
    }
};
