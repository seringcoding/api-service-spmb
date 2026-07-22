<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PesertaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/cari-peserta', [PesertaController::class, 'cari_peserta']);
Route::get('/wawancara/{user_id}', [PesertaController::class, 'detailPeserta']);
