@extends('layouts.app')

@section('title', 'Detail Layanan ' . $service->name)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('staff.services.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#7A4E45] hover:text-[#4A2018] transition-colors">
            &larr; Kembali ke Kelola Layanan
        </a>
    </div>

    <div class="bg-white rounded-lg border border-[#4A2018]/20 p-6">
        <div class="flex items-start justify-between gap-3 mb-5 pb-4 border-b border-[#4A2018]/15">
            <div>
                <h1 class="font-display text-2xl text-[#4A2018]">{{ $service->name }}</h1>
                <p class="text-sm font-mono text-[#7A4E45]">{{ $service->code }}</p>
            </div>
            @if($service->is_active)
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-[#DCEAF4] text-[#1F4A68] border border-[#9EC5E0]">Aktif</span>
            @else
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-[#F7E3DE] text-[#8F2D1E] border border-[#E3BDB4]">Nonaktif</span>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Tipe Harga</p>
                <p class="font-semibold text-[#4A2018] mt-0.5">{{ $service->pricing_type->label() }}</p>
            </div>
            <div>
                <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Harga Dasar</p>
                <p class="font-mono font-semibold text-lg text-[#4A2018] mt-0.5">Rp {{ number_format($service->base_price, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Surcharge</p>
                <p class="font-mono font-semibold text-[#6B4C0B] mt-0.5">+{{ $service->surcharge_percent }}%</p>
            </div>
            <div>
                <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Estimasi Penyelesaian</p>
                <p class="font-semibold text-[#4A2018] mt-0.5">{{ $service->estimated_days }} Hari</p>
            </div>
        </div>

        <a href="{{ route('staff.services.index') }}"
           class="inline-flex items-center justify-center bg-[#FAF8F4] hover:bg-[#F7E8B5] text-[#4A2018] border border-[#4A2018]/20 px-4 py-2 rounded-lg text-sm font-semibold transition-colors min-h-[42px]">
            Kembali
        </a>
    </div>
</div>
@endsection
