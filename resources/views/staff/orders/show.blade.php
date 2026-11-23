@extends('layouts.app')

@section('title', 'Detail Order ' . $order->order_code)

@section('content')
<div class="mb-5">
    <a href="{{ route('staff.orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#7A4E45] hover:text-[#4A2018] transition-colors">
        &larr; Kembali ke Daftar Order
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Kolom Kiri: Info Order & Penimbangan --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Header & Info Pelanggan --}}
        <div class="bg-white rounded-lg border border-[#4A2018]/20 overflow-hidden">
            <div class="bg-[#4A2018] text-[#FAF8F4] px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-mono opacity-75 uppercase tracking-widest">Kode Order</p>
                    <p class="text-2xl font-mono font-semibold tracking-wider mt-0.5">{{ $order->order_code }}</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $order->status->badgeClasses() }}">
                        {{ $order->status->label() }}
                    </span>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">
                        {{ $order->payment_status->label() }}
                    </span>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Nama Pelanggan</p>
                    <p class="font-semibold text-[#4A2018] mt-0.5">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Nomor HP</p>
                    <p class="font-mono font-semibold text-[#4A2018] mt-0.5">{{ $order->customer_phone }}</p>
                </div>
                <div class="sm:col-span-2 pt-2 border-t border-[#4A2018]/10">
                    <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Alamat Jemput</p>
                    <p class="font-medium text-[#4A2018] mt-0.5 leading-relaxed">{{ $order->pickup_address }}</p>
                </div>
                <div class="pt-2 border-t border-[#4A2018]/10">
                    <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Layanan</p>
                    <p class="font-semibold text-[#4A2018] mt-0.5">{{ $order->service->name }} ({{ $order->service->pricing_type->label() }})</p>
                </div>
                <div class="pt-2 border-t border-[#4A2018]/10">
                    <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Dibuat pada</p>
                    <p class="font-medium text-[#4A2018] mt-0.5">{{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>

        {{-- Penimbangan & Tagihan --}}
        <div class="bg-white rounded-lg border border-[#4A2018]/20 p-6">
            <h2 class="font-display text-xl text-[#4A2018] mb-4 pb-3 border-b border-[#4A2018]/15">Penimbangan &amp; Tagihan</h2>

            @if($order->isWeighed())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 text-sm">
                    @if($order->service->isPerKg())
                        <div>
                            <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Berat Aktual</p>
                            <p class="font-mono font-semibold text-[#4A2018] mt-1">{{ number_format($order->actual_weight_grams) }} gram</p>
                        </div>
                        <div>
                            <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Berat Tagihan</p>
                            <p class="font-mono font-semibold text-[#4A2018] mt-1">{{ $order->billed_weight_kg }} kg</p>
                        </div>
                    @else
                        <div>
                            <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Jumlah Item</p>
                            <p class="font-mono font-semibold text-[#4A2018] mt-1">{{ $order->item_count }} item</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Tarif Dipakai</p>
                        <p class="font-mono font-semibold text-[#4A2018] mt-1">Rp {{ number_format($order->applied_unit_price, 0, ',', '.') }}</p>
                    </div>
                    @if($order->applied_surcharge_percent > 0)
                        <div>
                            <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45]">Tambahan Express</p>
                            <p class="font-mono font-semibold text-[#6B4C0B] mt-1">+{{ $order->applied_surcharge_percent }}%</p>
                        </div>
                    @endif
                    <div class="bg-[#DCEAF4]/70 border border-[#9EC5E0] rounded-lg p-3">
                        <p class="text-[11px] font-mono uppercase tracking-wider text-[#1F4A68]">Total Tagihan</p>
                        <p class="font-mono font-semibold text-xl text-[#4A2018] mt-0.5">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-[#FAF8F4] border border-[#4A2018]/15 rounded-lg p-3">
                        <p class="text-[11px] font-mono uppercase tracking-wider text-[#7A4E45]">Estimasi Selesai</p>
                        <p class="font-semibold text-[#4A2018] mt-0.5">{{ $order->estimated_completion_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
            @elseif($order->status->value === 'picked_up' && !$order->isDelivered())
                <form method="POST" action="{{ route('staff.orders.weigh', $order) }}" class="space-y-4">
                    @csrf
                    @if($order->service->isPerKg())
                        <div>
                            <label for="actual_weight_grams" class="block text-sm font-semibold text-[#4A2018] mb-1.5">
                                Berat Aktual (gram) <span class="text-[#8F2D1E]">*</span>
                            </label>
                            <input type="number" name="actual_weight_grams" id="actual_weight_grams"
                                   min="1" max="500000" required
                                   value="{{ old('actual_weight_grams') }}"
                                   placeholder="Contoh: 2510 (= 2,51 kg)"
                                   class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm font-mono text-[#4A2018] min-h-[44px] focus:bg-white focus:border-[#4A2018] @error('actual_weight_grams') border-[#8F2D1E] @enderror">
                            <p class="text-xs text-[#7A4E45] mt-1.5">Berat tagihan minimal 2 kg dan dibulatkan ke atas per 0,5 kg otomatis di server.</p>
                            @error('actual_weight_grams')
                                <p class="text-[#8F2D1E] text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <div>
                            <label for="item_count" class="block text-sm font-semibold text-[#4A2018] mb-1.5">
                                Jumlah Item <span class="text-[#8F2D1E]">*</span>
                            </label>
                            <input type="number" name="item_count" id="item_count"
                                   min="1" max="1000" required
                                   value="{{ old('item_count') }}"
                                   placeholder="Masukkan jumlah item"
                                   class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm font-mono text-[#4A2018] min-h-[44px] focus:bg-white focus:border-[#4A2018] @error('item_count') border-[#8F2D1E] @enderror">
                            @error('item_count')
                                <p class="text-[#8F2D1E] text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    <button type="submit" class="cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors min-h-[44px]">
                        Simpan Penimbangan &amp; Hitung Tagihan
                    </button>
                </form>
            @else
                <div class="bg-[#F7E8B5]/70 border border-[#E5CE82] text-[#6B4C0B] rounded-lg p-4 text-sm">
                    @if($order->status->value === 'waiting_pickup')
                        Order harus ditandai "Sudah dijemput" terlebih dahulu sebelum bisa ditimbang.
                    @else
                        Belum ditimbang.
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Aksi Operasional --}}
    <div class="space-y-6">
        {{-- Update Status --}}
        <div class="bg-white rounded-lg border border-[#4A2018]/20 p-6">
            <h2 class="font-display text-lg text-[#4A2018] mb-4 pb-2.5 border-b border-[#4A2018]/15">Update Status</h2>

            @if($order->isDelivered())
                <div class="bg-[#FAF8F4] border border-[#4A2018]/20 text-[#7A4E45] rounded-lg p-3.5 text-sm text-center font-medium">
                    Order sudah diserahkan. Tidak bisa diubah lagi.
                </div>
            @else
                @php
                    $nextStatus = $order->status->next();
                @endphp

                @if($nextStatus)
                    @if($nextStatus === \App\Enums\OrderStatus::Processing && !$order->isWeighed())
                        <div class="bg-[#F7E8B5] border border-[#E5CE82] text-[#6B4C0B] rounded-lg p-3.5 text-xs leading-relaxed mb-4">
                            Penimbangan wajib diselesaikan sebelum order bisa masuk tahap "{{ $nextStatus->label() }}".
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.orders.status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $nextStatus->value }}">

                        <p class="text-sm text-[#7A4E45] mb-3.5">
                            Status berikutnya: <strong class="text-[#4A2018]">{{ $nextStatus->label() }}</strong>
                        </p>

                        <button type="submit"
                                class="w-full cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] py-2.5 px-4 rounded-lg text-sm font-semibold transition-colors min-h-[44px] disabled:opacity-45 disabled:cursor-not-allowed"
                                {{ ($nextStatus === \App\Enums\OrderStatus::Processing && !$order->isWeighed()) ? 'disabled' : '' }}>
                            Ubah ke "{{ $nextStatus->label() }}"
                        </button>

                        @error('status')
                            <p class="text-[#8F2D1E] text-xs mt-2 font-medium">{{ $message }}</p>
                        @enderror
                    </form>
                @endif
            @endif
        </div>

        {{-- Update Pembayaran --}}
        <div class="bg-white rounded-lg border border-[#4A2018]/20 p-6">
            <h2 class="font-display text-lg text-[#4A2018] mb-4 pb-2.5 border-b border-[#4A2018]/15">Status Pembayaran</h2>

            @if($order->isDelivered())
                <div class="bg-[#FAF8F4] border border-[#4A2018]/20 text-[#7A4E45] rounded-lg p-3.5 text-sm text-center font-medium">
                    Order terkunci setelah diserahkan.
                </div>
            @elseif(!$order->isWeighed())
                <p class="text-sm text-[#7A4E45]">Tagihan belum dihitung. Pembayaran baru bisa dicatat setelah penimbangan selesai.</p>
            @else
                <form method="POST" action="{{ route('staff.orders.payment', $order) }}" class="space-y-3.5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="payment_status" class="block text-sm font-semibold text-[#4A2018] mb-1.5">Ubah Status Pembayaran</label>
                        <select name="payment_status" id="payment_status"
                                class="w-full bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3 py-2.5 text-sm text-[#4A2018] min-h-[44px] focus:bg-white focus:border-[#4A2018]">
                            @foreach(\App\Enums\PaymentStatus::cases() as $ps)
                                <option value="{{ $ps->value }}" {{ $order->payment_status === $ps ? 'selected' : '' }}>
                                    {{ $ps->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full cursor-pointer bg-[#DCEAF4] hover:bg-[#C7DFF0] text-[#1F4A68] border border-[#9EC5E0] py-2.5 px-4 rounded-lg text-sm font-semibold transition-colors min-h-[44px]">
                        Simpan Pembayaran
                    </button>

                    @error('payment_status')
                        <p class="text-[#8F2D1E] text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </form>
            @endif
        </div>

        @if($order->delivered_at)
            <div class="bg-[#DCEAF4] border border-[#9EC5E0] rounded-lg p-4 text-sm text-[#1F4A68]">
                Diserahkan pada: <strong>{{ $order->delivered_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</strong>
            </div>
        @endif
    </div>
</div>
@endsection
