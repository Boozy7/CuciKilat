@extends('layouts.app')

@section('title', 'Login Petugas')

@section('content')
<div class="max-w-sm mx-auto mt-12">
    <div class="bg-white rounded-xl shadow p-8 border">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">🔐 Login Petugas</h1>
            <p class="text-sm text-gray-400 mt-1">Masuk untuk mengelola order & layanan</p>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                       placeholder="admin@cucikilat.local"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       placeholder="Masukkan password"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                Masuk
            </button>
        </form>
    </div>

    <p class="text-xs text-gray-400 text-center mt-4">
        <a href="{{ url('/') }}" class="hover:underline">← Kembali ke Beranda</a>
    </p>
</div>
@endsection
