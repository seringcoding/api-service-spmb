<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RegistrasiServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegistrasiController extends Controller
{
    public function store(Request $request, RegistrasiServices $registrasiServices): JsonResponse {
        // dd($request->all);
        $validator = Validator::make($request->all(), [
            'client_submission_id' => [
                'required',
                'uuid',
            ],

            'biodata' => ['required', 'array'],
            'biodata.namaLengkap' => ['required', 'string', 'max:150'],
            'biodata.nik' => ['required', 'digits:16'],
            'biodata.kk' => ['required', 'digits:16'],
            'biodata.jenisKelamin' => ['required', Rule::in(['L', 'P'])],
            'biodata.tanggalLahir' => ['required', 'date'],
            'biodata.tempatLahir' => ['required', 'string', 'max:100'],
            'biodata.nomorTelepon' => ['nullable', 'string', 'max:20'],
            'biodata.provinsi' => ['required', 'string', 'max:100'],
            'biodata.kabupatenKota' => ['required', 'string', 'max:100'],
            'biodata.kecamatan' => ['required', 'string', 'max:100'],
            'biodata.kelurahan' => ['required', 'string', 'max:100'],
            'biodata.alamat' => ['required', 'string'],
            'biodata.nisn' => ['nullable', 'digits:10'],
            'biodata.kabupatenSekolah' => ['required', 'string', 'max:100'],
            'biodata.asalSekolah' => ['nullable', 'array'],
            'biodata.asalSekolah.id' => [
                'nullable',
                'integer',
                Rule::exists('sekolah', 'id'),
            ],

            'data_orang_tua' => ['required', 'array'],
            'data_periodik' => ['required', 'array'],
            'nilai_raport' => ['required', 'array'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $sekolah = $request->input('biodata.asalSekolah');

            if (
                is_array($sekolah) &&
                !empty($sekolah) &&
                empty($sekolah['id'])
            ) {
                $validator->errors()->add(
                    'biodata.asalSekolah.id',
                    'Pilihan sekolah tidak memiliki ID yang valid.'
                );
            }
        });

        $validated = $validator->validate();

        // Pastikan NIK belum digunakan oleh pengajuan lain.
        // Retry dengan ID yang sama diperbolehkan.
        $existing = \App\Models\PengajuanPendaftaran::where(
            'client_submission_id',
            $validated['client_submission_id']
        )->first();

        if (!$existing && \App\Models\PengajuanPendaftaran::where('nik', $validated['biodata']['nik'] )->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'NIK sudah terdaftar dalam pengajuan.',
            ], 409);
        }

        $pengajuan = $registrasiServices->insertPengajuan($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil diterima.',
            'data' => [
                'nomor_tracking' => $pengajuan->nomor_tracking,
                'status' => $pengajuan->status,
            ],
        ], 201);

        // return response()->json([
        //     'method' => $request->method(),
        //     'url' => $request->fullUrl(),
        //     'content_type' => $request->header('Content-Type'),
        //     'is_json' => $request->isJson(),
        //     'all_data' => $request->all(),
        //     'raw_body' => $request->getContent(),
        // ]);
    }
}
