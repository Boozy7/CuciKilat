<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): JsonResponse|View
    {
        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Silakan login sebagai petugas untuk mengakses halaman ini.',
            ]);
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): JsonResponse|RedirectResponse
    {
        if (! Auth::attempt($request->validated())) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak sesuai.'],
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login berhasil.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return redirect()->route('staff.orders.index')->with('success', 'Login berhasil. Selamat datang, '.$user->name.'!');
    }

    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logout berhasil.',
            ]);
        }

        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }
}
