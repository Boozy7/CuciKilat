@extends('layouts.app')

@section('title', 'Laporan Pendapatan Harian')

@section('content')
<div class="mb-6 pb-4 border-b border-[#4A2018]/15">
    <p class="font-mono text-xs uppercase tracking-widest text-[#7A4E45] mb-1">Keuangan &amp; Rekap WIB</p>
    <h1 class="font-display text-3xl text-[#4A2018]">Laporan Pendapatan Harian</h1>
</div>

{{-- Filter Tanggal --}}
<form method="GET" action="{{ route('staff.reports.daily-revenue') }}" class="bg-white rounded-lg border border-[#4A2018]/20 p-4 sm:p-5 mb-6">
    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
        <div class="flex-1">
            <label for="date" class="block text-sm font-semibold text-[#4A2018] mb-1.5">Tanggal (WIB)</label>
            <input type="date" name="date" id="date" value="{{ $date }}"
                   class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2 text-sm font-mono text-[#4A2018] min-h-[44px] focus:bg-white focus:border-[#4A2018]">
        </div>
        <button type="submit" class="cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors min-h-[44px]">
            Tampilkan Laporan
        </button>
    </div>
</form>

{{-- Ringkasan --}}
<div class="grid grid-cols-1 sm:grid-cols-3 border border-[#4A2018]/20 rounded-lg overflow-hidden divide-y sm:divide-y-0 sm:divide-x divide-[#4A2018]/15 bg-white mb-6">
    <div class="p-6">
        <p class="text-xs font-mono text-[#7A4E45] uppercase tracking-wider mb-1">Tanggal</p>
        <p class="font-display text-2xl text-[#4A2018]">{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</p>
        <p class="text-xs font-mono text-[#7A4E45] mt-1">Zona: {{ $timezone }}</p>
    </div>
    <div class="p-6 bg-[#F7E8B5]/50">
        <p class="text-xs font-mono text-[#6B4C0B] uppercase tracking-wider mb-1">Total Pendapatan</p>
        <p class="font-mono text-2xl font-semibold text-[#4A2018]">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-[#6B4C0B] mt-1">Hanya pembayaran terkonfirmasi</p>
    </div>
    <div class="p-6 bg-[#DCEAF4]/55">
        <p class="text-xs font-mono text-[#1F4A68] uppercase tracking-wider mb-1">Jumlah Order Lunas</p>
        <p class="font-mono text-2xl font-semibold text-[#4A2018]">{{ $paidOrdersCount }}</p>
        <p class="text-xs text-[#1F4A68] mt-1">Order terbayar pada tanggal ini</p>
    </div>
</div>

{{-- Tabel Order Lunas --}}
<div class="bg-white rounded-lg border border-[#4A2018]/20 overflow-hidden">
    <div class="px-6 py-4 border-b border-[#4A2018]/15 bg-[#FAF8F4]">
        <h2 class="font-display text-lg text-[#4A2018]">Detail Order Lunas</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#FAF8F4]/60 border-b border-[#4A2018]/10 text-[#7A4E45] text-xs font-mono uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Kode Order</th>
                    <th class="px-4 py-3 text-left">Pelanggan</th>
                    <th class="px-4 py-3 text-left">Layanan</th>
                    <th class="px-4 py-3 text-right">Tagihan</th>
                    <th class="px-4 py-3 text-left">Dibayar pada</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#4A2018]/10">
                @forelse($orders as $order)
                    <tr class="hover:bg-[#FAF8F4]/70 transition-colors">
                        <td class="px-4 py-3.5 font-mono font-semibold text-[#1F4A68] whitespace-nowrap">
                            <a href="{{ route('staff.orders.show', $order) }}" class="hover:underline">{{ $order->order_code }}</a>
                        </td>
                        <td class="px-4 py-3.5 text-[#4A2018] font-medium">{{ $order->customer_name }}</td>
                        <td class="px-4 py-3.5 text-[#4A2018]">{{ $order->service->name }}</td>
                        <td class="px-4 py-3.5 text-right font-mono font-semibold text-[#4A2018] whitespace-nowrap">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-[#7A4E45] whitespace-nowrap">
                            {{ $order->paid_at ? $order->paid_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-[#7A4E45]">Belum ada order yang lunas pada tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
