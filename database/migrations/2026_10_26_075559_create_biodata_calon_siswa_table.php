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
        Schema::create('biodata_calon_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan_pendaftaran')->cascadeOnDelete();
            $table->string('nama_lengkap', 150);
            $table->string('nomor_kk', 16);
            $table->char('jenis_kelamin', 1);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir', 100);
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('provinsi', 100);
            $table->string('kabupaten_kota', 100);
            $table->string('kecamatan', 100);
            $table->string('kelurahan', 100);
            $table->text('alamat');
            $table->string('nisn', 10)->nullable();
            $table->string('kabupaten_sekolah', 100);
            $table->foreignId('asal_sekolah_id')->nullable()->constrained('sekolah')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodata_calon_siswa');
    }
};
