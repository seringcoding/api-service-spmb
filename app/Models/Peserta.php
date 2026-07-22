<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = "registrasi";

    protected $fillable = [
        'user_id', 'nomor_pendaftaran', 'nama_siswa', 'tempat_lahir', 'nisn', 'nik', 'asal_sekolah', 'jurusan_pertama', 'jurusan_kedua', 'nis', 'status'
    ];

}
