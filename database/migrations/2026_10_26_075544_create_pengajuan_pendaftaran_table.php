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
        Schema::create('pengajuan_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tracking', 60)->unique();
            $table->string('nik', 16)->unique();
            $table->string('status', 30)->default('PENDING_VERIFICATION');
            $table->text('catatan_penolakan')->nullable();
            $table->json('bagian_perlu_perbaikan')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pendaftaran');
    }
};
