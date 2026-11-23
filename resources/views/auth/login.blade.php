@extends('layouts.app')

@section('title', 'Login Petugas')

@section('content')
<div class="max-w-sm mx-auto mt-8 sm:mt-12">
    <div class="bg-white rounded-lg border border-[#4A2018]/20 overflow-hidden">
        <div class="bg-[#DCEAF4]/70 px-6 py-6 border-b border-[#4A2018]/15 text-center">
            <p class="font-mono text-[11px] uppercase tracking-widest text-[#1F4A68] mb-1">Portal Operasional</p>
            <h1 class="font-display text-2xl text-[#4A2018]">Login Petugas</h1>
            <p class="text-xs text-[#1F4A68] mt-1">Masuk untuk mengelola order, layanan &amp; laporan</p>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-[#4A2018] mb-1.5">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                       placeholder="admin@cucikilat.local"
                       class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm text-[#4A2018] min-h-[44px] focus:bg-white focus:border-[#4A2018] @error('email') border-[#8F2D1E] @enderror">
                @error('email')
                    <p class="text-[#8F2D1E] text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-[#4A2018] mb-1.5">Password</label>
                <input type="password" name="password" id="password" required
                       placeholder="Masukkan password"
                       class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm text-[#4A2018] min-h-[44px] focus:bg-white focus:border-[#4A2018] @error('password') border-[#8F2D1E] @enderror">
                @error('password')
                    <p class="text-[#8F2D1E] text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] font-semibold py-3 rounded-lg text-sm transition-colors min-h-[44px]">
                Masuk
            </button>
        </form>
    </div>

    <p class="text-xs text-[#7A4E45] text-center mt-4">
        <a href="{{ url('/') }}" class="hover:text-[#4A2018] underline underline-offset-4">Kembali ke Beranda</a>
    </p>
</div>
@endsection
