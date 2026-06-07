<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RiwayatBimbinganController extends Controller
{
    public function index(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();

        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen.');
        }

        $search = $request->query('search');

        $query = JadwalBimbingan::with(['mahasiswa', 'ketersediaanJadwal', 'catatanKonsultasi'])
            ->where('dosen_id', $dosen->id)
            ->where('status', 'completed');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('topik_bimbingan', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        $riwayat = $query->latest('updated_at')->get();

        return Inertia::render('Dosen/RiwayatBimbingan/Index', [
            'riwayatBimbingans' => $riwayat,
            'filters' => [
                'search' => $search ?? '',
            ]
        ]);
    }
}
