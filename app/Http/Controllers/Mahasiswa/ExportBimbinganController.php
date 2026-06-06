<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\JadwalBimbingan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportBimbinganController extends Controller
{
    /**
     * Export the guidance history of the logged‑in student as a PDF.
     */
    public function exportPdf(Request $request)
    {
        // 1. Get the logged‑in student
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        // 2. Optional filters
        $status = $request->query('status');
        $search = $request->query('search');

        // 3. Build the query for bimbingan records
        $query = JadwalBimbingan::select('jadwal_bimbingans.*')
            ->join('ketersediaan_jadwals', 'jadwal_bimbingans.ketersediaan_jadwal_id', '=', 'ketersediaan_jadwals.id')
            ->with(['dosen', 'mahasiswa', 'ketersediaanJadwal'])
            ->where('jadwal_bimbingans.mahasiswa_id', $mahasiswa->id);

        if ($status && $status !== 'all') {
            $query->where('jadwal_bimbingans.status', $status);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ketersediaan_jadwals.topik', 'like', "%{$search}%")
                  ->orWhere('ketersediaan_jadwals.judul_ta', 'like', "%{$search}%")
                  ->orWhereHas('dosen', function ($dq) use ($search) {
                      $dq->where('nama', 'like', "%{$search}%");
                  });
            });
        }
        $bimbingan = $query->orderBy('jadwal_bimbingans.created_at', 'desc')->get();

        // 4. Prepare data for the Blade view
        $data = [
            'mahasiswa' => $mahasiswa,
            'jadwalBimbingans' => $bimbingan,
            'filters'   => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
            ],
            'exportAt' => now()->locale('id')->translatedFormat('d F Y H:i') . ' WIB',
        ];

        // 5. Render PDF and download
        $pdf = Pdf::loadView('mahasiswa.bimbingan.export-pdf', $data);
        $cleanName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $mahasiswa->nama_lengkap);
        $fileName = 'Laporan_Bimbingan_' . $mahasiswa->nim . '_' . $cleanName . '.pdf';
        return $pdf->download($fileName);
    }
}
