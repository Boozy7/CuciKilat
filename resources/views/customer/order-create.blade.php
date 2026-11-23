@extends('layouts.app')

@section('title', 'Pesan Jemput')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 pb-4 border-b border-[#4A2018]/15">
        <p class="font-mono text-xs uppercase tracking-widest text-[#7A4E45] mb-1">Permintaan Baru</p>
        <h1 class="font-display text-3xl text-[#4A2018]">Pesan Jemput Laundry</h1>
        <p class="text-sm text-[#7A4E45] mt-1.5">Isi detail penjemputan di bawah. Berat dan tagihan final akan dihitung setelah cucian ditimbang petugas.</p>
    </div>

    <form method="POST" action="{{ route('orders.store') }}" class="bg-white rounded-lg border border-[#4A2018]/20 p-6 sm:p-8 space-y-5">
        @csrf

        {{-- Pilih Layanan --}}
        <div>
            <label for="service_id" class="block text-sm font-semibold text-[#4A2018] mb-1.5">
                Pilih Layanan <span class="text-[#8F2D1E]">*</span>
            </label>
            <select name="service_id" id="service_id" required
                    class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm text-[#4A2018] min-h-[44px] focus:bg-white focus:border-[#4A2018] @error('service_id') border-[#8F2D1E] @enderror">
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
                <p class="text-[#8F2D1E] text-xs mt-1.5 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama Pelanggan --}}
        <div>
            <label for="customer_name" class="block text-sm font-semibold text-[#4A2018] mb-1.5">
                Nama Lengkap <span class="text-[#8F2D1E]">*</span>
            </label>
            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required maxlength="120"
                   placeholder="Masukkan nama lengkap"
                   class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm text-[#4A2018] placeholder:text-[#7A4E45]/60 min-h-[44px] focus:bg-white focus:border-[#4A2018] @error('customer_name') border-[#8F2D1E] @enderror">
            @error('customer_name')
                <p class="text-[#8F2D1E] text-xs mt-1.5 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor HP --}}
        <div>
            <label for="customer_phone" class="block text-sm font-semibold text-[#4A2018] mb-1.5">
                Nomor Kontak / HP <span class="text-[#8F2D1E]">*</span>
            </label>
            <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required maxlength="30"
                   placeholder="Contoh: 08123456789"
                   class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm font-mono text-[#4A2018] placeholder:font-sans placeholder:text-[#7A4E45]/60 min-h-[44px] focus:bg-white focus:border-[#4A2018] @error('customer_phone') border-[#8F2D1E] @enderror">
            @error('customer_phone')
                <p class="text-[#8F2D1E] text-xs mt-1.5 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Alamat Jemput --}}
        <div>
            <label for="pickup_address" class="block text-sm font-semibold text-[#4A2018] mb-1.5">
                Alamat Jemput <span class="text-[#8F2D1E]">*</span>
            </label>
            <textarea name="pickup_address" id="pickup_address" required maxlength="1000" rows="3"
                      placeholder="Masukkan alamat lengkap beserta patokan untuk penjemputan"
                      class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm text-[#4A2018] placeholder:text-[#7A4E45]/60 focus:bg-white focus:border-[#4A2018] @error('pickup_address') border-[#8F2D1E] @enderror">{{ old('pickup_address') }}</textarea>
            @error('pickup_address')
                <p class="text-[#8F2D1E] text-xs mt-1.5 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] font-semibold py-3.5 px-5 rounded-lg text-sm transition-colors min-h-[48px]">
            Kirim Permintaan Jemput
        </button>
    </form>

    <div class="mt-5 p-4 rounded-lg bg-[#DCEAF4]/70 border border-[#9EC5E0] text-xs text-[#1F4A68] leading-relaxed">
        Setelah order dibuat, Anda akan menerima kode order unik untuk melacak status cucian. Data kontak &amp; alamat tidak pernah ditampilkan di halaman lacak publik.
    </div>
</div>
@endsection
