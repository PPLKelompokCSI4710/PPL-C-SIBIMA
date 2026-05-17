<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressController extends Controller
{
    public function index()
    {
        return Inertia::render('Staff/Progress/Index', [
            'students' => Mahasiswa::with('user', 'studentProgress')->get()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'user_id' => $m->user_id,
                    'name' => $m->user->name,
                    'nim' => $m->nim,
                    'ipk' => $m->ipk,
                    'total_sks' => $m->studentProgress->total_sks ?? 0,
                    'passed_courses' => $m->studentProgress->passed_courses ?? 0,
                ];
            }),
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'ipk' => 'required|numeric|min:0|max:4',
            'total_sks' => 'required|integer|min:0',
            'passed_courses' => 'required|integer|min:0',
        ]);

        $mahasiswa->update(['ipk' => $validated['ipk']]);

        StudentProgress::updateOrCreate(
            ['user_id' => $mahasiswa->user_id],
            [
                'total_sks' => $validated['total_sks'],
                'passed_courses' => $validated['passed_courses'],
                'ipk' => $validated['ipk'],
            ]
        );

        return back()->with('success', 'Progress mahasiswa berhasil diperbarui.');
    }
}
