@extends('layouts.app')

@section('title', 'Detail Layanan ' . $service->name)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('staff.services.index') }}" class="text-blue-600 hover:underline text-sm">← Kembali ke Kelola Layanan</a>
    </div>

    <div class="bg-white rounded-xl shadow border p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $service->name }}</h1>
                <p class="text-sm font-mono text-gray-400">{{ $service->code }}</p>
            </div>
            @if($service->is_active)
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
            @else
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">Nonaktif</span>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-xs text-gray-400">Tipe Harga</p>
                <p class="font-semibold">{{ $service->pricing_type->label() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Harga Dasar</p>
                <p class="font-bold text-lg text-blue-600">Rp {{ number_format($service->base_price, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Surcharge</p>
                <p class="font-semibold text-orange-600">+{{ $service->surcharge_percent }}%</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Estimasi Penyelesaian</p>
                <p class="font-semibold">{{ $service->estimated_days }} Hari</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('staff.services.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
