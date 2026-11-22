@extends('layouts.app')

@section('title', 'Daftar Order')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold">📋 Daftar Order</h1>
</div>

{{-- Filter & Search --}}
<form method="GET" action="{{ route('staff.orders.index') }}" class="bg-white rounded-xl shadow p-4 border mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Cari (Kode/Nama)</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari order..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status Cucian</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\OrderStatus::cases() as $st)
                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                        {{ $st->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status Pembayaran</label>
            <select name="payment_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach(\App\Enums\PaymentStatus::cases() as $ps)
                    <option value="{{ $ps->value }}" {{ request('payment_status') === $ps->value ? 'selected' : '' }}>
                        {{ $ps->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Filter</button>
            <a href="{{ route('staff.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold transition">Reset</a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl shadow border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Kode Order</th>
                    <th class="px-4 py-3 text-left">Pelanggan</th>
                    <th class="px-4 py-3 text-left">Layanan</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Tagihan</th>
                    <th class="px-4 py-3 text-center">Pembayaran</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono font-semibold text-blue-600">
                            <a href="{{ route('staff.orders.show', $order) }}" class="hover:underline">{{ $order->order_code }}</a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-400">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $order->service->name }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusColor = match($order->status->value) {
                                    'waiting_pickup' => 'bg-yellow-100 text-yellow-700',
                                    'picked_up' => 'bg-blue-100 text-blue-700',
                                    'processing' => 'bg-indigo-100 text-indigo-700',
                                    'ready_for_delivery' => 'bg-green-100 text-green-700',
                                    'delivered' => 'bg-gray-200 text-gray-600',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">{{ $order->status->label() }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($order->final_amount !== null)
                                Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $payColor = match($order->payment_status->value) {
                                    'unpaid' => 'text-red-600',
                                    'pending' => 'text-yellow-600',
                                    'paid' => 'text-green-600',
                                    default => 'text-gray-600',
                                };
                            @endphp
                            <span class="text-xs font-semibold {{ $payColor }}">{{ $order->payment_status->label() }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('staff.orders.show', $order) }}" class="text-blue-600 hover:underline text-xs font-semibold">Detail →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
