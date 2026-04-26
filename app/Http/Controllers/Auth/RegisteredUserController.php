<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:mahasiswa,dosen,admin',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        if ($request->role === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => 'NIM-'.$user->id.'-'.rand(1000, 9999),
                'nama_lengkap' => $user->name,
                'program_studi' => 'Belum Diatur',
                'angkatan' => date('Y'),
                'status_akademik' => 'aktif',
            ]);
        } elseif ($request->role === 'dosen') {
            Dosen::create([
                'user_id' => $user->id,
                'nidn' => 'NIDN-'.$user->id.'-'.rand(1000, 9999),
                'nama_lengkap' => $user->name,
                'program_studi' => 'Belum Diatur',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect('/admin');
    }
}
