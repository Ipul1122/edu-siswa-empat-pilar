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
            $table->foreignId('province_id')->nullable()->after('dapil')->constrained('provinces')->nullOnDelete();
            $table->foreignId('regency_id')->nullable()->after('province_id')->constrained('regencies')->nullOnDelete();

            $table->index(['province_id', 'regency_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['province_id', 'regency_id']);
            $table->dropForeign(['province_id']);
            $table->dropForeign(['regency_id']);
            $table->dropColumn(['province_id', 'regency_id']);
        });
    }
};
