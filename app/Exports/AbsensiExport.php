<?php

namespace App\Exports;

use App\Models\Absensi;
use Illuminate\Database\Eloquent\Builder; // Menggunakan Builder untuk type-hinting
use Maatwebsite\Excel\Concerns\FromQuery; // Menggunakan FromQuery untuk efisiensi memori
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
    * Menggunakan FromQuery lebih efisien untuk dataset besar karena
    * tidak memuat semua data ke memori sekaligus.
    *
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return $this->query;
    }

    /**
     * Mendefinisikan header untuk kolom-kolom di file Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Absen',
            'Nama Anggota',
            'Email',
            'Devisi',
            'Judul Kegiatan',
            'Waktu Absen',
            'Status',
            'Keterangan',
        ];
    }

    /**
     * Memetakan setiap baris data ke kolom yang sesuai di Excel.
     *
     * @param Absensi $absensi
     * @return array
     */
    public function map($absensi): array
    {
        // PERBAIKAN: Menggunakan null-safe operator (?->) untuk mencegah error jika relasi kosong.
        return [
            $absensi->id,
            $absensi->user?->name ?? 'User Dihapus',
            $absensi->user?->email ?? 'N/A',
            $absensi->user?->devisi?->nama_devisi ?? 'Umum',
            $absensi->kegiatan?->judul ?? 'Kegiatan Dihapus',
            $absensi->waktu_absen->format('Y-m-d H:i:s'),
            ucfirst($absensi->status),
            $absensi->keterangan,
        ];
    }
}