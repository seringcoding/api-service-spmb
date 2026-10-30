<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('sekolah:import {file=datasets/data-smp.csv}')]
#[Description('Import master sekolah SMP dan MTs dari CSV')]
class ImportSekolahCsv extends Command
{
    public function handle(): int
    {
        $path = storage_path('app/' . $this->argument('file'));

        if (!is_file($path) || !is_readable($path)) {
            $this->error("File tidak ditemukan atau tidak dapat dibaca: {$path}");
            return self::FAILURE;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error('Gagal membuka file CSV.');
            return self::FAILURE;
        }

        // Deteksi delimiter koma atau titik koma.
        $firstLine = fgets($handle);

        if ($firstLine === false) {
            fclose($handle);
            $this->error('File CSV kosong.');
            return self::FAILURE;
        }

        $delimiter = substr_count($firstLine, ';')
            > substr_count($firstLine, ',') ? ';' : ',';

        rewind($handle);

        $headers = fgetcsv($handle, 0, $delimiter);

        if (!$headers) {
            fclose($handle);
            $this->error('Header CSV tidak ditemukan.');
            return self::FAILURE;
        }

        // Hapus BOM UTF-8 dan rapikan header.
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

        $headers = array_map(
            fn ($header) => strtolower(trim($header)),
            $headers
        );

        // Sesuaikan header CSV dengan nama kolom yang digunakan aplikasi.
        $headerMap = [
            'nama' => 'nama_sekolah',
        ];

        $headers = array_map(
            fn ($header) => $headerMap[$header] ?? $header,
            $headers
        );

        $required = [
            'npsn',
            'nama_sekolah',
            'bentuk',
            'jenjang',
            'kabupaten',
        ];

        foreach ($required as $column) {
            if (!in_array($column, $headers, true)) {
                fclose($handle);
                $this->error("Kolom wajib '{$column}' tidak ditemukan.");
                $this->line('Kolom CSV: ' . implode(', ', $headers));
                return self::FAILURE;
            }
        }

        $index = array_flip($headers);

        $batch = [];
        $batchSize = 500;

        $processed = 0;
        $skipped = 0;
        $invalid = 0;
        $line = 1;

        $allowedBentuk = ['SMP', 'MTS'];

        $flush = function () use (&$batch): void {
            if (empty($batch)) {
                return;
            }

            DB::table('sekolah')->upsert(
                $batch,
                ['npsn'],
                [
                    'nama_sekolah',
                    'bentuk',
                    'jenis',
                    'status',
                    'jenjang',
                    'kabupaten',
                    'kecamatan',
                    'kelurahan',
                    'alamat',
                    'jalur',
                    'pembina',
                    'updated_at',
                ]
            );

            $batch = [];
        };

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $line++;

                if ($row === [null] || $row === []) {
                    continue;
                }

                // Abaikan baris dengan jumlah kolom yang tidak sesuai.
                if (count($row) !== count($headers)) {
                    $invalid++;
                    continue;
                }

                $record = [];

                foreach ($index as $column => $position) {
                    $record[$column] = trim(
                        (string) ($row[$position] ?? '')
                    );
                }

                $npsn = $record['npsn'] ?? '';
                $nama = $record['nama_sekolah'] ?? '';
                $bentuk = strtoupper($record['bentuk'] ?? '');
                $kabupaten = $record['kabupaten'] ?? '';

                // Filter berdasarkan Bentuk, bukan Jenjang.
                if (!in_array($bentuk, $allowedBentuk, true)) {
                    $skipped++;
                    continue;
                }

                // NPSN, nama sekolah, dan kabupaten wajib tersedia.
                if ($npsn === '' || $nama === '' || $kabupaten === '') {
                    $invalid++;
                    continue;
                }

                $now = now();

                $batch[] = [
                    'npsn' => $npsn,
                    'nama_sekolah' => $nama,
                    'bentuk' => $bentuk,
                    'jenis' => $record['jenis'] ?? null,
                    'status' => $record['status'] ?? null,
                    'jenjang' => $record['jenjang'] ?? null,
                    'kabupaten' => $kabupaten,
                    'kecamatan' => $record['kecamatan'] ?? null,
                    'kelurahan' => $record['kelurahan'] ?? null,
                    'alamat' => $record['alamat'] ?? null,
                    'jalur' => $record['jalur'] ?? null,
                    'pembina' => $record['pembina'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $processed++;

                if (count($batch) >= $batchSize) {
                    $flush();
                    $this->line("Memproses baris CSV: {$line}");
                }
            }

            $flush();
        } catch (\Throwable $e) {
            report($e);

            $this->error(
                'Import gagal. Periksa log Laravel untuk detailnya.'
            );

            fclose($handle);

            return self::FAILURE;
        }

        fclose($handle);

        $this->newLine();
        $this->info('Import selesai.');

        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Data valid diproses', $processed],
                ['Baris dilewati karena bentuk', $skipped],
                ['Baris tidak valid', $invalid],
            ]
        );

        return self::SUCCESS;
    }
}
