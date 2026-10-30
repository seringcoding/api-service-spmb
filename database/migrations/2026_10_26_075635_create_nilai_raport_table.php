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
        Schema::create('nilai_raport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan_pendaftaran')->cascadeOnDelete();
            $mapel = ['bahasa_indonesia','bahasa_inggris','matematika'];
            foreach ($mapel as $namaMapel) {
                for ($semester = 1; $semester <= 5; $semester++) {
                    $table->decimal("nilai_{$namaMapel}_{$semester}", 5, 2)->nullable();
                }
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_raport');
    }
};
