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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20)->unique();
            $table->string('nama_sekolah');
            $table->string('bentuk')->nullable();
            $table->string('jenis')->nullable();
            $table->string('status')->nullable();
            $table->string('jenjang', 20)->index();
            $table->string('kabupaten')->index();
            $table->string('kecamatan')->nullable()->index();
            $table->string('kelurahan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('jalur')->nullable();
            $table->string('pembina')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
