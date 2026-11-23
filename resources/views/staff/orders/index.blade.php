@extends('layouts.app')

@section('title', 'Daftar Order')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 pb-4 border-b border-[#4A2018]/15">
    <div>
        <p class="font-mono text-xs uppercase tracking-widest text-[#7A4E45] mb-1">Operasional Petugas</p>
        <h1 class="font-display text-3xl text-[#4A2018]">Daftar Order</h1>
    </div>
    <p class="font-mono text-xs text-[#7A4E45]">Total ditampilkan: <strong class="text-[#4A2018]">{{ $orders->count() }}</strong></p>
</div>

{{-- Filter & Search --}}
<form method="GET" action="{{ route('staff.orders.index') }}" class="bg-white rounded-lg border border-[#4A2018]/20 p-4 sm:p-5 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div>
            <label for="search" class="block text-xs font-semibold text-[#4A2018] mb-1.5">Cari (Kode / Nama)</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   placeholder="Cari order..."
                   class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018] min-h-[42px] focus:bg-white focus:border-[#4A2018]">
        </div>
        <div>
            <label for="status" class="block text-xs font-semibold text-[#4A2018] mb-1.5">Status Cucian</label>
            <select name="status" id="status" class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018] min-h-[42px] focus:bg-white focus:border-[#4A2018]">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\OrderStatus::cases() as $st)
                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                        {{ $st->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="payment_status" class="block text-xs font-semibold text-[#4A2018] mb-1.5">Status Pembayaran</label>
            <select name="payment_status" id="payment_status" class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3 py-2 text-sm text-[#4A2018] min-h-[42px] focus:bg-white focus:border-[#4A2018]">
                <option value="">Semua</option>
                @foreach(\App\Enums\PaymentStatus::cases() as $ps)
                    <option value="{{ $ps->value }}" {{ request('payment_status') === $ps->value ? 'selected' : '' }}>
                        {{ $ps->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] px-4 py-2 rounded-lg text-sm font-semibold transition-colors min-h-[42px]">
                Filter
            </button>
            <a href="{{ route('staff.orders.index') }}" class="inline-flex items-center justify-center bg-[#FAF8F4] hover:bg-[#F7E8B5] text-[#4A2018] border border-[#4A2018]/20 px-4 py-2 rounded-lg text-sm font-semibold transition-colors min-h-[42px]">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-lg border border-[#4A2018]/20 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#FAF8F4] border-b border-[#4A2018]/15 text-[#7A4E45] text-xs font-mono uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3.5 text-left">Kode Order</th>
                    <th class="px-4 py-3.5 text-left">Pelanggan</th>
                    <th class="px-4 py-3.5 text-left">Layanan</th>
                    <th class="px-4 py-3.5 text-center">Status</th>
                    <th class="px-4 py-3.5 text-right">Tagihan</th>
                    <th class="px-4 py-3.5 text-center">Pembayaran</th>
                    <th class="px-4 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#4A2018]/10">
                @forelse($orders as $order)
                    <tr class="hover:bg-[#FAF8F4]/70 transition-colors">
                        <td class="px-4 py-3.5 font-mono font-semibold text-[#1F4A68] whitespace-nowrap">
                            <a href="{{ route('staff.orders.show', $order) }}" class="hover:underline">{{ $order->order_code }}</a>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-[#4A2018]">{{ $order->customer_name }}</div>
                            <div class="text-xs font-mono text-[#7A4E45]">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-[#4A2018]">{{ $order->service->name }}</td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->status->badgeClasses() }}">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right font-mono whitespace-nowrap">
                            @if($order->final_amount !== null)
                                <span class="font-semibold text-[#4A2018]">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-[#7A4E45]/60">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">
                                {{ $order->payment_status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            <a href="{{ route('staff.orders.show', $order) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-md bg-[#DCEAF4] hover:bg-[#C7DFF0] text-[#1F4A68] text-xs font-semibold transition-colors">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-[#7A4E45]">Belum ada order yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
