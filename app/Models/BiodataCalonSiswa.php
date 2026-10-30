<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiodataCalonSiswa extends Model
{
    protected $table = 'biodata_calon_siswa';

    protected $fillable = [
        'pengajuan_id',
        'nama_lengkap',
        'nik',
        'nomor_kk',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'nomor_telepon',
        'provinsi',
        'kabupaten_kota',
        'kecamatan',
        'kelurahan',
        'alamat',
        'nisn',
        'kabupaten_sekolah',
        'asal_sekolah_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            PengajuanPendaftaran::class,
            'pengajuan_id'
        );
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(
            Sekolah::class,
            'asal_sekolah_id'
        );
    }
}
