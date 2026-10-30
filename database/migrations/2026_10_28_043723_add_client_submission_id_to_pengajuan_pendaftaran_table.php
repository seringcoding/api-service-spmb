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
        Schema::table('pengajuan_pendaftaran', function (Blueprint $table) {
            $table->uuid('client_submission_id')
                ->nullable()
                ->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_pendaftaran', function (Blueprint $table) {
            $table->dropUnique(['client_submission_id']);
            $table->dropColumn('client_submission_id');
        });
    }
};
