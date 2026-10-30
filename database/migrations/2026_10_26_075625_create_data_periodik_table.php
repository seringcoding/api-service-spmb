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
        Schema::create('data_periodik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan_pendaftaran')->cascadeOnDelete();
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('jarak_tempuh', 8, 2)->nullable();
            $table->unsignedInteger('waktu_tempuh')->nullable();
            $table->unsignedSmallInteger('jumlah_saudara_kandung')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_periodik');
    }
};
