<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrasiSiswaModel extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'nama',
        'nik',
        'kk',
        'jenis_kelamin',
        'tanggal_lahir',
        'nomor_telepon',
        'provinsi',
        'kecamatan',
        'kelurahan',
        'alamat',
        'nisn',
        'asal_sekolah'
    ];

    // Logik relasi antar tabel
}
