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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('package_code', 50)->default('Paket Utama')->after('type');
            $table->unsignedBigInteger('province_id')->nullable()->after('package_code');
            $table->boolean('randomize_questions')->default(true)->after('province_id');
            $table->boolean('randomize_options')->default(true)->after('randomize_questions');

            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->index(['pillar', 'type', 'province_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropIndex(['pillar', 'type', 'province_id']);
            $table->dropColumn(['package_code', 'province_id', 'randomize_questions', 'randomize_options']);
        });
    }
};
