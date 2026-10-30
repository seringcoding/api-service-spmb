<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiRaport extends Model
{
    protected $table = 'nilai_raport';

    protected $fillable = [
        'pengajuan_id',

        'nilai_bahasa_indonesia_1',
        'nilai_bahasa_inggris_1',
        'nilai_matematika_1',

        'nilai_bahasa_indonesia_2',
        'nilai_bahasa_inggris_2',
        'nilai_matematika_2',

        'nilai_bahasa_indonesia_3',
        'nilai_bahasa_inggris_3',
        'nilai_matematika_3',

        'nilai_bahasa_indonesia_4',
        'nilai_bahasa_inggris_4',
        'nilai_matematika_4',

        'nilai_bahasa_indonesia_5',
        'nilai_bahasa_inggris_5',
        'nilai_matematika_5',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            PengajuanPendaftaran::class,
            'pengajuan_id'
        );
    }
}
