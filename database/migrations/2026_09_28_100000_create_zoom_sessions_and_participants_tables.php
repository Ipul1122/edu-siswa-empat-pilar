<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zoom_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('pillar', 50)->default('umum');
            $table->text('zoom_link');
            $table->string('meeting_id', 100)->nullable();
            $table->string('passcode', 100)->nullable();
            $table->unsignedInteger('capacity')->default(100);
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('is_active');
            $table->index('start_time');
        });

        Schema::create('zoom_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_session_id')->constrained('zoom_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('joined_at');
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['zoom_session_id', 'user_id']);
            $table->index('joined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_participants');
        Schema::dropIfExists('zoom_sessions');
    }
};
