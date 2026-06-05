<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        // Fetch study plans
        $studyPlans = $mahasiswa ? $mahasiswa->studyPlans()->with('course')->get() : collect([]);

        // Map study plans for easier frontend usage
        $mappedStudyPlans = $studyPlans->map(function ($plan) {
            return [
                'id' => $plan->id,
                'courseId' => $plan->course_id,
                'courseName' => $plan->course->name,
                'courseCode' => $plan->course->code,
                'credits' => $plan->course->credits,
                'semester' => $plan->semester,
                'status' => ucfirst($plan->status),
            ];
        });

        $availableCourses = Course::all()->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code,
                'credits' => $c->credits,
            ];
        });

        $studentProgress = StudentProgress::where('user_id', $user->id)->first();

        return Inertia::render('Mahasiswa/Dashboard', [
            'auth' => [
                'user' => $user,
                'mahasiswa' => $mahasiswa,
                'progress' => $studentProgress,
            ],
            'studyPlans' => $mappedStudyPlans,
            'availableCourses' => $availableCourses,
        ]);
    }

    public function calendar(Request $request)
    {
        return Inertia::render('Mahasiswa/AcademicCalendar', [
            'auth' => [
                'user' => $request->user(),
            ],
        ]);
    }
}
