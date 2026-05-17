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
        $user = $request->user();
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
        ]);

        StudentProgress::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'ipk' => $request->ipk,
                'total_sks' => $request->total_sks,
                'passed_courses' => $request->passed_courses ?? 0,
            ]
        );

        // Update Mahasiswa IPK as well
        if ($request->user()->mahasiswa) {
            $request->user()->mahasiswa->update(['ipk' => $request->ipk]);
        }

        return back();
    }
}
