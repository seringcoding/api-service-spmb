<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataOrangTua extends Model
{
    protected $table = 'data_orang_tua';

    protected $fillable = [
        'pengajuan_id',

        'nama_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'status_ayah',
        'tahun_lahir_ayah',
        'pendidikan_ayah',

        'nama_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'status_ibu',
        'tahun_lahir_ibu',
        'pendidikan_ibu',

        'nama_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'status_wali',
        'tahun_lahir_wali',
        'pendidikan_wali',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            PengajuanPendaftaran::class,
            'pengajuan_id'
        );
    }
}
