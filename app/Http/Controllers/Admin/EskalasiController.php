<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eskalasi;
use Inertia\Inertia;

class EskalasiController extends Controller
{
    public function index()
    {
        $eskalasis = Eskalasi::with(['mahasiswa.user', 'mahasiswa.dosens', 'mahasiswa.bimbingans' => function ($q) {
            $q->where('status', 'selesai')->orderBy('waktu_selesai', 'desc');
        }])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Eskalasi/Index', [
            'eskalasis' => $eskalasis,
        ]);
    }
}
