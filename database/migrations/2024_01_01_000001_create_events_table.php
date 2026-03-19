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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->text('notes')->nullable();
            $table->integer('max_participants')->nullable();
            $table->decimal('cost', 8, 2)->nullable();
            $table->date('event_date');
            $table->time('event_time');
            $table->dateTime('registration_deadline');
            $table->enum('event_type', ['cultural', 'recreational', 'educational', 'sports', 'other']);
            $table->enum('status', ['draft', 'published', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Annulla le migrazioni.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
