@extends('layouts.app')

@section('title', 'Order Berhasil')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-lg border border-[#4A2018]/20 overflow-hidden">
        <div class="bg-[#F7E8B5]/60 px-6 py-7 border-b border-[#4A2018]/15 text-center">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-[#6B4C0B] mb-2">Konfirmasi Order</p>
            <h1 class="font-display text-2xl sm:text-3xl text-[#4A2018] mb-1.5">Permintaan Jemput Berhasil!</h1>
            <p class="text-sm text-[#7A4E45]">Simpan kode order di bawah ini untuk melacak progres cucian Anda.</p>
        </div>

        <div class="p-6 sm:p-8">
            <div class="bg-[#DCEAF4] border border-[#9EC5E0] rounded-lg py-5 px-6 text-center mb-6">
                <p class="text-[11px] font-mono text-[#1F4A68] mb-1.5 font-semibold uppercase tracking-widest">Kode Order Anda</p>
                <p class="text-2xl sm:text-3xl font-mono font-semibold text-[#4A2018] tracking-widest select-all">{{ $order->order_code }}</p>
            </div>

            <div class="bg-[#FAF8F4] border border-[#4A2018]/15 rounded-lg divide-y divide-[#4A2018]/10 text-sm mb-6">
                <div class="px-4 py-3 flex items-center justify-between gap-4">
                    <span class="text-[#7A4E45]">Layanan</span>
                    <span class="font-semibold text-[#4A2018]">{{ $order->service->name }}</span>
                </div>
                <div class="px-4 py-3 flex items-center justify-between gap-4">
                    <span class="text-[#7A4E45]">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->status->badgeClasses() }}">
                        {{ $order->status->label() }}
                    </span>
                </div>
                <div class="px-4 py-3 flex items-center justify-between gap-4">
                    <span class="text-[#7A4E45]">Berat &amp; Tagihan</span>
                    <span class="text-[#7A4E45] italic text-right">Akan muncul setelah dijemput &amp; ditimbang</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('orders.track', $order->order_code) }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] py-3 px-4 rounded-lg font-semibold text-sm transition-colors min-h-[44px]">
                    Lacak Order Ini
                </a>
                <a href="{{ url('/') }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 bg-[#FAF8F4] hover:bg-[#F7E8B5] text-[#4A2018] border border-[#4A2018]/20 py-3 px-4 rounded-lg font-semibold text-sm transition-colors min-h-[44px]">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
