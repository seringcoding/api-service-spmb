<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sekolah;

class SekolahController extends Controller
{
    public function kabupaten() {
        $data = Sekolah::query()
            ->whereNotNull('kabupaten')
            ->where('kabupaten', '!=', '')
            ->distinct()
            ->orderBy('kabupaten')
            ->pluck('kabupaten');

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function sekolah(Request $request) {
        $validated = $request->validate([
            'kabupaten' => ['required', 'string', 'max:150'],
            'kecamatan' => ['nullable', 'string', 'max:150'],
            'search' => ['nullable', 'string', 'max:150'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max: 100'],
        ]);

        $query = Sekolah::query()
            ->select([
                'id',
                'npsn',
                'nama_sekolah',
                'bentuk',
                'status',
                'kabupaten',
                'kecamatan',
            ])
            ->where('kabupaten', $validated['kabupaten']);

        if (!empty($validated['kecamatan'])) {
            $query->where('kecamatan', $validated['kecamatan']);
        }

        if (!empty($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($q) use ($search) {
                $q->where('nama_sekolah', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%");
            });
        }

        $sekolah = $query
            ->orderBy('nama_sekolah')
            ->paginate($validated['per_page'] ?? 20);

        return response()->json([
            'success' => true,
            'data' => $sekolah,
        ]);
    }

    public function getSekolahAll() {
        $query = Sekolah::query()
            ->select([
                'id',
                'npsn',
                'nama_sekolah',
                'bentuk',
                'status',
                'kabupaten',
                'kecamatan',
            ]);

        $sekolah = $query
            ->orderBy('nama_sekolah')
            ->paginate($validated['per_page'] ?? 20);

        return response()->json([
            'success' => true,
            'data' => $sekolah,
        ]);
    }
}
