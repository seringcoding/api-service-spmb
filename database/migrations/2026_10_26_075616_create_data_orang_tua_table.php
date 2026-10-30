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
        Schema::create('data_orang_tua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->unique()->constrained('pengajuan_pendaftaran')->cascadeOnDelete();
            $kelompok = ['ayah', 'ibu', 'wali'];
            foreach ($kelompok as $orang) {
                $table->string("nama_{$orang}", 150)->nullable();
                $table->string("pekerjaan_{$orang}", 100)->nullable();
                $table->string("penghasilan_{$orang}", 50)->nullable();
                $table->string("status_{$orang}", 50)->nullable();
                $table->unsignedSmallInteger("tahun_lahir_{$orang}")->nullable();
                $table->string("pendidikan_{$orang}", 100)->nullable();
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_orang_tua');
    }
};
