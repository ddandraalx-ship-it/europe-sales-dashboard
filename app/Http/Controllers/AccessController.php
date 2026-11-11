<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AccessController extends Controller
{
    public function login()
    {
        if (session('admin_authenticated')) {
            return redirect()->route('access');
        }

        return view('login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'destination' => ['required', 'in:gateway,etl'],
        ]);

        if ($credentials['username'] !== 'admin' || $credentials['password'] !== 'admin') {
            throw ValidationException::withMessages([
                'username' => 'Username atau password tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        if ($credentials['destination'] === 'etl') {
            return redirect()->route('etl');
        }

        return redirect()->intended(route('access'));
    }

    public function access()
    {
        return view('access');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
