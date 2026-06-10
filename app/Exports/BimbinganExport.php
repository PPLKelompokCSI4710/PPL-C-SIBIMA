<?php

namespace App\Exports;

use App\Models\JadwalBimbingan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BimbinganExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection|JadwalBimbingan[]
     */
    protected $bimbingans;

    /**
     * @var \App\Models\Mahasiswa
     */
    protected $mahasiswa;

    public function __construct($bimbingans, $mahasiswa)
    {
        $this->bimbingans = $bimbingans;
        $this->mahasiswa = $mahasiswa;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        $rows = [];
        foreach ($this->bimbingans as $index => $b) {
            $rows[] = [
                $index + 1,
                optional($b->ketersediaanJadwal)->tanggal ?? '',
                optional($b->dosen)->nama_lengkap ?? '',
                $b->topik_bimbingan ?? '',
                $b->status ?? '',
                optional($b->ketersediaanJadwal)->lokasi ?? '',
                $b->created_at->format('d-m-Y'),
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Jadwal',
            'Nama Dosen',
            'Topik Bimbingan',
            'Status',
            'Lokasi',
            'Dibuat Pada',
        ];
    }
}
?>
