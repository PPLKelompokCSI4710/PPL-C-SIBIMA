<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudyPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudyPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $studyPlans = $mahasiswa ? $mahasiswa->studyPlans()->with('course')->get() : collect();

        $mappedStudyPlans = $studyPlans->map(function ($plan) {
            return [
                'id' => $plan->id,
                'courseId' => $plan->course_id,
                'courseName' => $plan->course->name,
                'courseCode' => $plan->course->code,
                'credits' => $plan->course->credits,
                'semester' => $plan->semester,
                'status' => strtolower($plan->status),
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

        return Inertia::render('Mahasiswa/StudyPlans', [
            'auth' => [
                'user' => $user,
                'mahasiswa' => $mahasiswa,
            ],
            'studyPlans' => $mappedStudyPlans,
            'availableCourses' => $availableCourses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        $mahasiswa = $request->user()->mahasiswa;

        if (! $mahasiswa) {
            return back()->withErrors(['message' => 'Profil mahasiswa tidak ditemukan.']);
        }

        // Check if course already added
        $exists = StudyPlan::where('mahasiswa_id', $mahasiswa->id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['course_id' => 'Mata kuliah sudah ada di KRS Anda.']);
        }

        StudyPlan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'course_id' => $request->course_id,
            'semester' => $request->semester,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Mata kuliah berhasil ditambahkan ke KRS.');
    }

    public function update(Request $request, StudyPlan $studyPlan)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        $mahasiswa = $request->user()->mahasiswa;

        if ($studyPlan->mahasiswa_id !== $mahasiswa?->id) {
            abort(403);
        }

        // Check if updating to a course that already exists in another study plan
        if ($studyPlan->course_id !== $request->course_id) {
            $exists = StudyPlan::where('mahasiswa_id', $mahasiswa->id)
                ->where('course_id', $request->course_id)
                ->exists();

            if ($exists) {
                return back()->withErrors(['course_id' => 'Mata kuliah sudah ada di KRS Anda.']);
            }
        }

        $studyPlan->update([
            'course_id' => $request->course_id,
            'semester' => $request->semester,
        ]);

        return back()->with('success', 'Rencana studi berhasil diperbarui.');
    }

    public function destroy(StudyPlan $studyPlan, Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        if ($studyPlan->mahasiswa_id !== $mahasiswa?->id) {
            abort(403);
        }

        $studyPlan->delete();

        return back()->with('success', 'Mata kuliah berhasil dihapus dari KRS.');
    }
}
