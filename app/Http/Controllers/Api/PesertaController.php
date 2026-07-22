<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // 👈 Wajib diimport untuk menggunakan Validator::make
use App\Models\Peserta;
use Exception; // 👈 Wajib diimport agar catch(Exception) berfungsi

class PesertaController extends Controller
{
    // Cek peserta
    public function cari_peserta(Request $request)
    {
        // 1. Gunakan Validator::make agar bisa menggunakan method ->fails()
        $valid = Validator::make($request->all(), [
            'nomor_pendaftaran' => 'required|numeric'
        ], [
            'nomor_pendaftaran.required' => 'Nomor pendaftaran wajib diisi.',
            'nomor_pendaftaran.numeric'  => 'Nomor pendaftaran harus berupa angka.'
        ]);

        // Sesuai dengan logika Anda, cek jika validasi gagal
        if ($valid->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validasi failed',
                'errors' => $valid->errors()
            ], 422);
        }

        try {
            // 2. Ambil nilai input dari request dan simpan ke variabel
            $nomor_pendaftaran = $request->input('nomor_pendaftaran');

            // 3. Cari data ke database
            $peserta = Peserta::where('nomor_pendaftaran', $nomor_pendaftaran)->first();
            
            // 4. Jika peserta tidak ditemukan
            if (!$peserta) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Nomor pendaftaran tidak ditemukan dalam sistem.'
                ], 404); // Berikan status code 404
            }

            // 5. Jika sukses ditemukan
            return response()->json([
                'status' => 'success',
                'message' => 'Get Peserta Success',
                'data' => [
                    'user_id'           => $peserta->user_id,
                    'nomor_pendaftaran' => $peserta->nomor_pendaftaran,
                    'nis'               => $peserta->nis, 
                    'nisn'              => $peserta->nisn, 
                    'nik'               => $peserta->nik, 
                    'nama_siswa'        => $peserta->nama_siswa, 
                    'tempat_lahir'      => $peserta->tempat_lahir, 
                    'asal_sekolah'      => $peserta->asal_sekolah, 
                    'jurusan_pertama'   => $peserta->jurusan_pertama, 
                    'jurusan_kedua'     => $peserta->jurusan_kedua, 
                    'status'            => $peserta->status,
                ]
            ], 200);

        } catch(Exception $e) {
            // Menangkap error jika ada masalah query atau database down
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], 500);
        }
    }

    public function detailPeserta($id)
    {
        $peserta = Peserta::where('user_id', $id)->first();
        if (!$peserta) {
            return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $peserta], 200);
    }
}