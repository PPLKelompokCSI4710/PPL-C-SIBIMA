<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StudyPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KrsController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyPlan::with(['mahasiswa.user', 'course']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return Inertia::render('Staff/StudyPlans/Index', [
            'studyPlans' => $query->get()->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'student_name' => $plan->mahasiswa->user->name ?? 'Unknown',
                    'student_nim' => $plan->mahasiswa->nim ?? '-',
                    'course_name' => $plan->course->name,
                    'course_code' => $plan->course->code,
                    'credits' => $plan->course->credits,
                    'semester' => $plan->semester,
                    'status' => $plan->status,
                    'created_at' => $plan->created_at->format('d M Y'),
                ];
            }),
            'filters' => $request->only(['status']),
        ]);
    }

    public function approve(StudyPlan $studyPlan)
    {
        $studyPlan->update(['status' => 'approved']);

        return back()->with('success', 'KRS berhasil disetujui.');
    }

    public function reject(StudyPlan $studyPlan)
    {
        $studyPlan->update(['status' => 'rejected']);

        return back()->with('success', 'KRS berhasil ditolak.');
    }
}
