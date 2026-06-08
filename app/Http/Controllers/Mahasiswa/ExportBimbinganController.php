<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\JadwalBimbingan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BimbinganExport;

class ExportBimbinganController extends Controller
{
    /**
     * Export the guidance history of the logged‑in student as a PDF.
     */
    public function exportPdf(Request $request)
    {
        // 1. Ambil data mahasiswa yang sedang login
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        // 2. Ambil query filter opsional dari request (untuk fleksibilitas ekspor sesuai filter)
        $status = $request->query('status');
        $search = $request->query('search');
        $format = $request->query('format', 'pdf'); // default pdf

        // 3. Query data bimbingan milik mahasiswa
        $query = JadwalBimbingan::select('jadwal_bimbingans.*')
            ->join('ketersediaan_jadwals', 'jadwal_bimbingans.ketersediaan_jadwal_id', '=', 'ketersediaan_jadwals.id')
            ->with(['dosen', 'mahasiswa', 'ketersediaanJadwal'])
            ->where('jadwal_bimbingans.mahasiswa_id', $mahasiswa->id);

        // Filter berdasarkan status jika dipilih
        if ($status && $status !== 'all') {
            $query->where('jadwal_bimbingans.status', $status);
        }

        // Filter berdasarkan kata kunci pencarian (topik, judul TA, nama dosen)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jadwal_bimbingans.topik_bimbingan', 'like', "%{$search}%")
                    ->orWhere('jadwal_bimbingans.judul_ta', 'like', "%{$search}%")
                    ->orWhereHas('dosen', function ($q2) use ($search) {
                        $q2->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        // Urutkan berdasarkan tanggal ketersediaan jadwal terbaru (descending)
        $jadwalBimbingans = $query->orderBy('ketersediaan_jadwals.tanggal', 'desc')->get();

        // 4. Siapkan data untuk dikirim ke template PDF Blade atau Excel
        $data = [
            'mahasiswa' => $mahasiswa,
            'jadwalBimbingans' => $jadwalBimbingans,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
            ],
            'exportAt' => now()->locale('id')->translatedFormat('d F Y H:i') . ' WIB',
        ];

        // 5. Nama file
        $cleanName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $mahasiswa->nama_lengkap);
        $fileBase = 'Laporan_Bimbingan_' . $mahasiswa->nim . '_' . $cleanName;

        // 6. Export berdasarkan format
        if (strtolower($format) === 'excel') {
            // Use Laravel Excel export
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BimbinganExport($jadwalBimbingans, $mahasiswa), $fileBase . '.xlsx');
        }

        // Default PDF export
        $pdf = Pdf::loadView('mahasiswa.bimbingan.export-pdf', $data);
        $fileName = $fileBase . '.pdf';
        return $pdf->download($fileName);
    }
}
