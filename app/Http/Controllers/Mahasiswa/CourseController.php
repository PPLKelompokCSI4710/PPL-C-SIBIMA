<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $availableCourses = Course::all()->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code,
                'credits' => $c->credits,
                'semester' => $c->semester,
            ];
        });

        return Inertia::render('Mahasiswa/Courses', [
            'auth' => [
                'user' => $user,
            ],
            'courses' => $availableCourses,
        ]);
    }
}
