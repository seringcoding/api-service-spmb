<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanPendaftaran extends Model
{
    protected $table = 'pengajuan_pendaftaran';

    protected $fillable = [
        'nomor_tracking',
        'nik',
        'status',
        'catatan_penolakan',
        'bagian_perlu_perbaikan',
        'verified_by',
        'verified_at'
    ];

    protected function cast(): array {
        return [
            'bagian_perlu_perbaikan' => 'array',
            'verified_at' => 'datetime'
        ];
    }

    public function biodata(): HasOne {
        return $this->hasOne(BiodataCalonSiswa::class, 'pengajuan_id');
    }

    public function orangTua(): HasOne {
        return $this->hasOne(DataOrangTua::class, 'pengajuan_id');
    }

    public function periodik(): HasOne {
        return $this->hasOne(DataPeriodik::class, 'pengajuan_id');
    }

    public function nilaiRaport(): HasOne {
        return $this->hasOne(NilaiRaport::class, 'pengajuan_id');
    }

    public function berkas(): HasMany {
        return $this->hasMany(BerkasPendaftaran::class, 'pengajuan_id');
    }

    public function verifier(): BelongsTo {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
