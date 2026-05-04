<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        return Inertia::render('Staff/Courses/Index', [
            'courses' => Course::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:courses,code',
            'name' => 'required|string',
            'credits' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        Course::create($validated);

        return back()->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:courses,code,'.$course->id,
            'name' => 'required|string',
            'credits' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        $course->update($validated);

        return back()->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return back()->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
