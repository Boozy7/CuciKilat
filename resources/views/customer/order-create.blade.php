@extends('layouts.app')

@section('title', 'Pesan Jemput')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">📦 Pesan Jemput Laundry</h1>

    <form method="POST" action="{{ route('orders.store') }}" class="bg-white rounded-xl shadow p-6 space-y-5 border">
        @csrf

        {{-- Pilih Layanan --}}
        <div>
            <label for="service_id" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Layanan <span class="text-red-500">*</span></label>
            <select name="service_id" id="service_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('service_id') border-red-500 @enderror">
                <option value="">— Pilih layanan —</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->name }} — Rp {{ number_format($service->base_price, 0, ',', '.') }}/{{ $service->isPerKg() ? 'kg' : 'item' }}
                        @if($service->surcharge_percent > 0) (+{{ $service->surcharge_percent }}%) @endif
                        (est. {{ $service->estimated_days }} hari)
                    </option>
                @endforeach
            </select>
            @error('service_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama Pelanggan --}}
        <div>
            <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required maxlength="120"
                   placeholder="Masukkan nama lengkap"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customer_name') border-red-500 @enderror">
            @error('customer_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor HP --}}
        <div>
            <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-1">Nomor HP <span class="text-red-500">*</span></label>
            <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required maxlength="30"
                   placeholder="Contoh: 08123456789"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customer_phone') border-red-500 @enderror">
            @error('customer_phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Alamat Jemput --}}
        <div>
            <label for="pickup_address" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Jemput <span class="text-red-500">*</span></label>
            <textarea name="pickup_address" id="pickup_address" required maxlength="1000" rows="3"
                      placeholder="Masukkan alamat lengkap untuk penjemputan"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pickup_address') border-red-500 @enderror">{{ old('pickup_address') }}</textarea>
            @error('pickup_address')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
            Kirim Permintaan Jemput
        </button>
    </form>

    <p class="text-xs text-gray-400 text-center mt-4">
        Setelah order berhasil dibuat, Anda akan mendapatkan kode order untuk melacak status cucian Anda.
    </p>
</div>
@endsection
