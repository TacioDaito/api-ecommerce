<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function view()
    {
        if (Auth::check()) {
            return response()->json([
                'message' => 'Already logged in',
            ]);
        } else {
            return view('login');
        }
    }
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return redirect()->back()->withErrors($error->validator)->withInput();
        }
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended();
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Invalid credentials',
            ])->withInput();
        }
    }
}
