<?php

namespace App\Services;

use App\Models\PengajuanPendaftaran;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class RegistrasiServices
{
    // Proses Registrasi
    public function insertPengajuan(array $data): PengajuanPendaftaran {

        $transaksi = DB::transaction(function() use ($data) {
            // Retry untuk submission ID yang sama tidak membuat data baru.
            $existing = PengajuanPendaftaran::where(
                'client_submission_id',
                $data['client_submission_id']
            )->first();

            if ($existing) {
                return $existing->load([
                    'biodata',
                    'orangTua',
                    'periodik',
                    'nilaiRaport',
                ]);
            }

            $biodata = $data['biodata'];
            $sekolah = $biodata['asalSekolah'] ?? null;

            // Buat pengajuan
            $pengajuan = PengajuanPendaftaran::create([
                'client_submission_id' => $data['client_submission_id'],
                'nomor_tracking' => $this->buatNomorTracking(),
                'nik' => $biodata['nik'],
                'status' => 'PENDING_VERIFICATION',
            ]);

            // Simpan biodata
            $pengajuan->biodata()->create([
                'nama_lengkap' => $data['biodata']['namaLengkap'],
                'nik' => $data['biodata']['nik'],
                'nomor_kk' => $data['biodata']['kk'],
                'jenis_kelamin' => $data['biodata']['jenisKelamin'],
                'tempat_lahir' => $data['biodata']['tempatLahir'],
                'tanggal_lahir' => $data['biodata']['tanggalLahir'],
                'nomor_telepon' => $data['biodata']['nomorTelepon'],
                'provinsi' => $data['biodata']['provinsi'],
                'kabupaten_kota' => $data['biodata']['kabupatenKota'],
                'kecamatan' => $data['biodata']['kecamatan'],
                'kelurahan' => $data['biodata']['kelurahan'],
                'alamat' => $data['biodata']['alamat'],
                'nisn' => $data['biodata']['nisn'],
                'kabupaten_sekolah' => $data['biodata']['kabupatenSekolah'],
                'asal_sekolah_id' => $data['biodata']['asalSekolah']['id'] ?? null,
            ]);

            // Simpan data orang tua
            $pengajuan->orangTua()->create([
                "nama_ayah" => $data['data_orang_tua']['nama_ayah'],
                "nik_ayah" => $data['data_orang_tua']['nik_ayah'],
                "pekerjaan_ayah" => $data['data_orang_tua']['pekerjaan_ayah'],
                "pendidikan_ayah" => $data['data_orang_tua']['pendidikan_ayah'],
                "penghasilan_ayah" => $data['data_orang_tua']['penghasilan_ayah'],
                "status_ayah" => $data['data_orang_tua']['status_ayah'],
                "tahun_lahir_ayah" => $data['data_orang_tua']['tahun_lahir_ayah'],
                "nomor_telepon_ayah" => $data['data_orang_tua']['nomor_telepon_ayah'],
                "nama_ibu" => $data['data_orang_tua']['nama_ibu'],
                "nik_ibu" => $data['data_orang_tua']['nik_ibu'],
                "pekerjaan_ibu" => $data['data_orang_tua']['pekerjaan_ibu'],
                "pendidikan_ibu" => $data['data_orang_tua']['pendidikan_ibu'],
                "penghasilan_ibu" => $data['data_orang_tua']['penghasilan_ibu'],
                "status_ibu" => $data['data_orang_tua']['status_ibu'],
                "tahun_lahir_ibu" => $data['data_orang_tua']['tahun_lahir_ibu'],
                "nomor_telepon_ibu" => $data['data_orang_tua']['nomor_telepon_ibu'],
            ]);

            // Simpan data periodik
            $pengajuan->periodik()->create([
                "tinggi_badan" => $data['data_periodik']['tinggi_badan'],
                "berat_badan" => $data['data_periodik']['berat_badan'],
                "jarak_tempuh" => $data['data_periodik']['jarak_tempuh'],
                "waktu_tempuh" => $data['data_periodik']['waktu_tempuh'],
                "jumlah_saudara_kandung" => $data['data_periodik']['jumlah_saudara_kandung']
            ]);

            // Simpan data nilai raport
            $pengajuan->nilaiRaport()->create([
                "nilai_bahasa_indonesia_1" => $data['nilai_raport']['semester_1']['bahasa_indonesia'],
                "nilai_matematika_1" => $data['nilai_raport']['semester_1']['matematika'],
                "nilai_bahasa_inggris_1" => $data['nilai_raport']['semester_1']['bahasa_inggris'],
                "nilai_bahasa_indonesia_2" => $data['nilai_raport']['semester_2']['bahasa_indonesia'],
                "nilai_matematika_2" => $data['nilai_raport']['semester_2']['matematika'],
                "nilai_bahasa_inggris_2" => $data['nilai_raport']['semester_2']['bahasa_inggris'],
                "nilai_bahasa_indonesia_3" => $data['nilai_raport']['semester_3']['bahasa_indonesia'],
                "nilai_matematika_3" => $data['nilai_raport']['semester_3']['matematika'],
                "nilai_bahasa_inggris_3" => $data['nilai_raport']['semester_3']['bahasa_inggris'],
                "nilai_bahasa_indonesia_4" => $data['nilai_raport']['semester_4']['bahasa_indonesia'],
                "nilai_matematika_4" => $data['nilai_raport']['semester_4']['matematika'],
                "nilai_bahasa_inggris_4" => $data['nilai_raport']['semester_4']['bahasa_inggris'],
                "nilai_bahasa_indonesia_5" => $data['nilai_raport']['semester_5']['bahasa_indonesia'],
                "nilai_matematika_5" => $data['nilai_raport']['semester_5']['matematika'],
                "nilai_bahasa_inggris_5" => $data['nilai_raport']['semester_5']['bahasa_inggris']
            ]);

            return $pengajuan->load([
                'biodata', 'orangTua', 'periodik', 'nilaiRaport'
            ]);
        });

        return $transaksi;
    }

    private function buatNomorTracking(): string
    {
        do {
            $nomor = 'TRK-' . strtoupper(Str::random(12));
        } while (
            PengajuanPendaftaran::where(
                'nomor_tracking',
                $nomor
            )->exists()
        );

        return $nomor;
    }
}
