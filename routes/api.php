<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistrasiController;
use App\Http\Controllers\Api\SekolahController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

// Request Registrasi dan Login
Route::post('/registrasi', [AuthController::class, 'register']);
Route::post('/login', [AuthController:: class, 'login']);

// Get Verifikasi Email
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');

// Resend Email Verification
Route::post('/email/resend', [AuthController::class, 'resendVerification']);

// Request kirim email reset password
Route::post('/request-password', [AuthController::class, 'forgotPassword']);

// Request buat password baru
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Endpoint login dan email terverifikasi
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Test endpoint
    Route::post('/spmb/pendaftaran', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Selamat! Anda berhasil mengakses formulir pendaftaran SPMB.'
        ]);
    });

    // Endpoint melihat profil user aktif
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Get data sekolah jenjang SMP & MTS
Route::prefix('sekolah')->group(function () {
    Route::get('/kabupaten', [SekolahController::class, 'kabupaten']);
    Route::get('/', [SekolahController::class, 'sekolah']);
    // Route::get('/all', [SekolahController::class, 'getSekolah']);
});

// Wilayah API Route
Route::get('/wilayah/{level}/{kode?}', function($level, $kode = null) {
    $endpoints = [
        'provinsi' => 'provinces.json',
        'kabupaten' => 'regencies',
        'kecamatan' => 'districts',
        'kelurahan' => 'villages',
    ];

    abort_unless(isset($endpoints[$level]), 404);

    if ($level === 'provinsi') {
        $path = $endpoints[$level];
    } else {
        abort_unless(
            is_string($kode) &&
            preg_match('/^[0-9.]+$/', $kode),
            400
        );

        $path = $endpoints[$level] . '/' . $kode . '.json';
    }

    $response = Http::timeout(10)
        ->get("https://wilayah.id/api/{$path}");

    if (!$response->successful()) {
        return response()->json([
            'message' => 'Gagal mengambil data wilayah'
        ], 502);
    }

    return response()->json($response->json());
});

// API Route POST data registrasi biodata, orang tua, data periodik, nilai raport dan upload berkas
Route::post('/pendaftaran', [RegistrasiController::class, 'store']);
