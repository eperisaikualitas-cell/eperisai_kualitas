<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // ✅ SUDAH DIPERBAIKI: Mengarah ke route Perisai yang baru
            return redirect()->route('perisai.index');
        }

        return back()->withErrors(['username' => 'Username atau password salah!'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // ✅ SUDAH DIPERBAIKI: Saat logout, kembali ke halaman login
        return redirect('/login');
    }
}