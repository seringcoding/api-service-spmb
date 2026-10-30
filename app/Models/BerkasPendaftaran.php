<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BerkasPendaftaran extends Model
{
    protected $table = 'berkas_pendaftaran';

    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'jenis_berkas',
        'path_file',
        'nama_file_asli',
        'mime_type',
        'ukuran_file',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'ukuran_file' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            PengajuanPendaftaran::class,
            'pengajuan_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
