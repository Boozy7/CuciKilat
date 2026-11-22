@extends('layouts.app')

@section('title', 'Laporan Pendapatan Harian')

@section('content')
<h1 class="text-2xl font-bold mb-6">📊 Laporan Pendapatan Harian</h1>

{{-- Filter Tanggal --}}
<form method="GET" action="{{ route('staff.reports.daily-revenue') }}" class="bg-white rounded-xl shadow p-4 border mb-6">
    <div class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="flex-1">
            <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal (WIB)</label>
            <input type="date" name="date" id="date" value="{{ $date }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
            Tampilkan Laporan
        </button>
    </div>
</form>

{{-- Ringkasan --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow border p-5 text-center">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Tanggal</p>
        <p class="text-xl font-bold">{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</p>
        <p class="text-xs text-gray-400">Zona: {{ $timezone }}</p>
    </div>
    <div class="bg-white rounded-xl shadow border p-5 text-center">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Pendapatan</p>
        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow border p-5 text-center">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Jumlah Order Lunas</p>
        <p class="text-2xl font-bold text-blue-600">{{ $paidOrdersCount }}</p>
    </div>
</div>

{{-- Tabel Order Lunas --}}
<div class="bg-white rounded-xl shadow border overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="font-bold">Detail Order Lunas</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Kode Order</th>
                    <th class="px-4 py-3 text-left">Pelanggan</th>
                    <th class="px-4 py-3 text-left">Layanan</th>
                    <th class="px-4 py-3 text-right">Tagihan</th>
                    <th class="px-4 py-3 text-left">Dibayar pada</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold text-blue-600">
                            <a href="{{ route('staff.orders.show', $order) }}" class="hover:underline">{{ $order->order_code }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $order->customer_name }}</td>
                        <td class="px-4 py-3">{{ $order->service->name }}</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-xs">
                            {{ $order->paid_at ? $order->paid_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada order yang lunas pada tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
