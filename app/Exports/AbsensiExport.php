<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Mengambil data dari query yang sudah difilter di controller
        return $this->query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Mendefinisikan header untuk kolom Excel
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
     * @var Absensi $absensi
     */
    public function map($absensi): array
    {
        // Memetakan setiap baris data ke kolom yang sesuai
        return [
            $absensi->id,
            $absensi->user->name ?? 'N/A',
            $absensi->user->email ?? 'N/A',
            $absensi->user->devisi->nama_devisi ?? 'Umum',
            $absensi->kegiatan->judul ?? 'N/A',
            $absensi->waktu_absen->format('Y-m-d H:i:s'),
            ucfirst($absensi->status),
            $absensi->keterangan,
        ];
    }
}