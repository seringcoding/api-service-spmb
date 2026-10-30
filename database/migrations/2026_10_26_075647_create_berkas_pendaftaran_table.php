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
        Schema::create('berkas_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_pendaftaran')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis_berkas', 30);
            $table->string('path_file');
            $table->string('nama_file_asli')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            $table->string('status_verifikasi', 30)->default('PENDING');
            $table->text('catatan_verifikasi')->nullable();            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique(['pengajuan_id', 'jenis_berkas'],'berkas_pengajuan_jenis_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_pendaftaran');
    }
};
