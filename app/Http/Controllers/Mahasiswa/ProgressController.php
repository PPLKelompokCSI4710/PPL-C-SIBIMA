<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('mahasiswa');
        $progress = StudentProgress::where('user_id', $user->id)->first();

        return Inertia::render('Mahasiswa/ProgressStudi', [
            'auth' => [
                'user' => $user,
                'progress' => $progress,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'ipk' => 'required|numeric|min:0|max:4',
            'total_sks' => 'required|integer|min:0',
            'passed_courses' => 'nullable|integer|min:0',
            'target_ipk' => 'nullable|numeric|min:0|max:4|gte:ipk',
            'target_sks' => 'nullable|integer|min:0|gte:total_sks',
            'target_semester' => 'nullable|integer|min:1|max:14',
            'tak' => 'nullable|integer|min:0|max:120',
            'nim' => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('mahasiswa', 'nim')->ignore($request->user()->mahasiswa?->id),
            ],
            'semester' => 'required|integer|min:1|max:14',
            'program_studi' => 'required|string|max:100',
        ], [
            'target_ipk.gte' => 'Target IPK tidak boleh lebih rendah dari IPK saat ini.',
            'target_sks.gte' => 'Target SKS tidak boleh lebih rendah dari total SKS saat ini.',
            'tak.min' => 'Poin TAK tidak boleh bernilai negatif.',
            'tak.max' => 'Poin TAK tidak boleh melebihi 120.',
            'nim.unique' => 'NIM sudah terdaftar untuk mahasiswa lain.',
        ]);

        StudentProgress::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'ipk' => $request->ipk,
                'total_sks' => $request->total_sks,
                'passed_courses' => $request->passed_courses ?? 0,
                'target_ipk' => $request->target_ipk,
                'target_sks' => $request->target_sks,
                'target_semester' => $request->target_semester,
                'tak' => $request->tak ?? 0,
            ]
        );

        // Update Mahasiswa IPK, NIM, Semester, and Program Studi
        if ($request->user()->mahasiswa) {
            $request->user()->mahasiswa->update([
                'ipk' => $request->ipk,
                'nim' => $request->nim,
                'semester' => $request->semester,
                'program_studi' => $request->program_studi,
            ]);
        }

        return back();
    }
}
