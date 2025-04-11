<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index () {
        return view('auth.login');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($validated)) {
            $request->session()->regenerate();
            $user = Auth::user();
    
            // Logout jika status tidak aktif
            if (!$user->status) {
                Auth::logout();
                return response()->json([
                    'message' => 'Akun tidak aktif.',
                ], 403);
            }
    
            // Redirect khusus jika punya id_employee
            if ($user->id_employee) {
                return response()->json([
                    'redirect' => route('employee.show', $user->id_employee),
                ]);
            }
    
            // Redirect biasa
            return response()->json([
                'redirect' => url('/'),
            ]);
        }
    
        return response()->json([
            'message' => 'Email atau password salah.',
        ], 401);
    }
    

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    
}
