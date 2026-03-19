<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esegue le migrazioni.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('event_preferences')->nullable()->after('role');
            $table->boolean('email_notifications')->default(true)->after('event_preferences');
        });
    }

    /**
     * Annulla le migrazioni.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['event_preferences', 'email_notifications']);
        });
    }
};
