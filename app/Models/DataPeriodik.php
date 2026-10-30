<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPeriodik extends Model
{
    protected $table = 'data_periodik';

    protected $fillable = [
        'pengajuan_id',
        'berat_badan',
        'jarak_tempuh',
        'waktu_tempuh',
        'jumlah_saudara_kandung',
    ];

    protected function casts(): array
    {
        return [
            'berat_badan' => 'decimal:2',
            'jarak_tempuh' => 'decimal:2',
            'waktu_tempuh' => 'integer',
            'jumlah_saudara_kandung' => 'integer',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            PengajuanPendaftaran::class,
            'pengajuan_id'
        );
    }
}
