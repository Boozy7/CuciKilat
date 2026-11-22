@extends('layouts.app')

@section('title', 'Order Berhasil')

@section('content')
<div class="max-w-lg mx-auto text-center">
    <div class="bg-white rounded-xl shadow p-8 border">
        <div class="text-5xl mb-4">✅</div>
        <h1 class="text-2xl font-bold text-green-600 mb-2">Permintaan Jemput Berhasil!</h1>
        <p class="text-gray-500 mb-6">Simpan kode order di bawah ini untuk melacak status cucian Anda.</p>

        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl py-4 px-6 mb-6">
            <p class="text-xs text-blue-500 mb-1 font-semibold uppercase tracking-wider">Kode Order Anda</p>
            <p class="text-3xl font-mono font-bold text-blue-700 tracking-widest">{{ $order->order_code }}</p>
        </div>

        <div class="text-left bg-gray-50 rounded-lg p-4 text-sm space-y-2 mb-6">
            <div class="flex justify-between">
                <span class="text-gray-500">Layanan:</span>
                <span class="font-semibold">{{ $order->service->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status:</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                    {{ $order->status->label() }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Berat & Tagihan:</span>
                <span class="text-gray-400 italic">Akan muncul setelah dijemput & ditimbang</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('orders.track', $order->order_code) }}"
               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold text-sm transition text-center">
                🔍 Lacak Order Ini
            </a>
            <a href="{{ url('/') }}"
               class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold text-sm transition text-center">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
