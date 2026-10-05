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
        Schema::table('users', function (Blueprint $table) {
            $table->string('pic_name')->nullable()->after('school_name');
            $table->string('whatsapp', 30)->nullable()->after('pic_name');
            $table->boolean('is_troubled')->default(false)->after('whatsapp');
            $table->text('trouble_notes')->nullable()->after('is_troubled');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->boolean('is_retest')->default(false)->after('violations_count');
            $table->string('retest_reason')->nullable()->after('is_retest');
            $table->foreignId('retest_granted_by')->nullable()->constrained('users')->nullOnDelete()->after('retest_reason');
        });

        Schema::table('zoom_participants', function (Blueprint $table) {
            $table->string('status', 30)->default('connected')->after('notes'); // connected, disconnected, trouble, left
            $table->string('device_info')->nullable()->after('status');
            $table->dateTime('last_ping_at')->nullable()->after('device_info');
            $table->dateTime('left_at')->nullable()->after('last_ping_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_participants', function (Blueprint $table) {
            $table->dropColumn(['status', 'device_info', 'last_ping_at', 'left_at']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropForeign(['retest_granted_by']);
            $table->dropColumn(['is_retest', 'retest_reason', 'retest_granted_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pic_name', 'whatsapp', 'is_troubled', 'trouble_notes']);
        });
    }
};
