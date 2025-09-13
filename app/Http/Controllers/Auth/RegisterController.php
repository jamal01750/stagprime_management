<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function show()
    {
        $setting = DB::table('settings')->first();

        if (!$setting->registration_enabled) {
            abort(403, 'Registration is disabled.');
        }
        // if (!$setting || !$setting->registration_enabled) {
        //     abort(403, 'Registration is disabled.');
        // }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $setting = DB::table('settings')->first();
        if (!$setting->registration_enabled) {
            abort(403, 'Registration is disabled.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin', // first registered user is admin
        ]);

        return redirect()->route('login')->with('success', 'Registered successfully');
    }
}
