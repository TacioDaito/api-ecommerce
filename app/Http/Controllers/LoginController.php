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
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'message' => 'Validation error',
                'error' => $error->validator->errors(),
            ], 422);
        }
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect($request->input('url_previous'));
        } else {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
    }
}
