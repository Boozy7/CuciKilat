@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="text-center py-12">
    <h1 class="text-4xl font-bold text-blue-600 mb-3">🧺 CuciKilat</h1>
    <p class="text-gray-500 text-lg mb-8">Layanan laundry cepat, bersih, dan terpercaya!</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md mx-auto">
        <a href="{{ route('orders.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl py-4 px-6 text-center font-semibold shadow transition">
            📦 Pesan Jemput
        </a>
        <a href="{{ route('orders.track.form') }}"
           class="bg-white hover:bg-gray-50 text-blue-600 border-2 border-blue-600 rounded-xl py-4 px-6 text-center font-semibold shadow transition">
            🔍 Lacak Order
        </a>
    </div>
</div>

{{-- Daftar Layanan --}}
<div class="mt-12">
    <h2 class="text-2xl font-bold text-center mb-6">Layanan Kami</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @forelse($services as $service)
            <div class="bg-white rounded-xl shadow p-5 border">
                <h3 class="font-bold text-lg text-blue-600">{{ $service->name }}</h3>
                <p class="text-sm text-gray-500 mb-2">{{ $service->pricing_type->label() }}</p>
                <p class="text-2xl font-bold text-gray-800">
                    Rp {{ number_format($service->base_price, 0, ',', '.') }}
                    <span class="text-sm font-normal text-gray-400">
                        /{{ $service->isPerKg() ? 'kg' : 'item' }}
                    </span>
                </p>
                @if($service->surcharge_percent > 0)
                    <p class="text-xs text-orange-500 mt-1">+{{ $service->surcharge_percent }}% surcharge</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">Estimasi {{ $service->estimated_days }} hari</p>
            </div>
        @empty
            <p class="col-span-3 text-center text-gray-400">Belum ada layanan tersedia.</p>
        @endforelse
    </div>
</div>
@endsection
