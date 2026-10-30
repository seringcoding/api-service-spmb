<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'bentuk',
        'jenis',
        'status',
        'jenjang',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'alamat',
        'jalur',
        'pembina',
    ];
}
