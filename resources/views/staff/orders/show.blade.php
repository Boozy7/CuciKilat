@extends('layouts.app')

@section('title', 'Detail Order ' . $order->order_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.orders.index') }}" class="text-blue-600 hover:underline text-sm">← Kembali ke Daftar Order</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Kolom Kiri: Info Order --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Header --}}
        <div class="bg-white rounded-xl shadow border p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Kode Order</p>
                    <p class="text-2xl font-mono font-bold text-blue-600">{{ $order->order_code }}</p>
                </div>
                <div class="flex gap-2 flex-wrap">
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
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                        {{ $order->status->label() }}
                    </span>
                    @php
                        $payBg = match($order->payment_status->value) {
                            'unpaid' => 'bg-red-100 text-red-700',
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'paid' => 'bg-green-100 text-green-700',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $payBg }}">
                        {{ $order->payment_status->label() }}
                    </span>
                </div>
            </div>

            {{-- Info Pelanggan (petugas bisa lihat) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400">Nama Pelanggan</p>
                    <p class="font-semibold">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Nomor HP</p>
                    <p class="font-semibold">{{ $order->customer_phone }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs text-gray-400">Alamat Jemput</p>
                    <p class="font-semibold">{{ $order->pickup_address }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Layanan</p>
                    <p class="font-semibold">{{ $order->service->name }} ({{ $order->service->pricing_type->label() }})</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Dibuat pada</p>
                    <p class="font-semibold">{{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>

        {{-- Penimbangan & Tagihan --}}
        <div class="bg-white rounded-xl shadow border p-6">
            <h2 class="text-lg font-bold mb-4">⚖️ Penimbangan & Tagihan</h2>

            @if($order->isWeighed())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    @if($order->service->isPerKg())
                        <div>
                            <p class="text-xs text-gray-400">Berat Aktual</p>
                            <p class="font-semibold">{{ number_format($order->actual_weight_grams) }} gram</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Berat Tercatat</p>
                            <p class="font-semibold">{{ $order->billed_weight_kg }} kg</p>
                        </div>
                    @else
                        <div>
                            <p class="text-xs text-gray-400">Jumlah Item</p>
                            <p class="font-semibold">{{ $order->item_count }} item</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400">Harga Satuan</p>
                        <p class="font-semibold">Rp {{ number_format($order->applied_unit_price, 0, ',', '.') }}</p>
                    </div>
                    @if($order->applied_surcharge_percent > 0)
                        <div>
                            <p class="text-xs text-gray-400">Surcharge</p>
                            <p class="font-semibold text-orange-600">+{{ $order->applied_surcharge_percent }}%</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400">Total Tagihan</p>
                        <p class="font-bold text-xl text-blue-600">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Estimasi Selesai</p>
                        <p class="font-semibold">{{ $order->estimated_completion_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
            @elseif($order->status->value === 'picked_up' && !$order->isDelivered())
                {{-- Form Penimbangan --}}
                <form method="POST" action="{{ route('staff.orders.weigh', $order) }}" class="space-y-4">
                    @csrf
                    @if($order->service->isPerKg())
                        <div>
                            <label for="actual_weight_grams" class="block text-sm font-semibold text-gray-700 mb-1">
                                Berat Aktual (gram) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="actual_weight_grams" id="actual_weight_grams"
                                   min="1" max="500000" required
                                   value="{{ old('actual_weight_grams') }}"
                                   placeholder="Contoh: 2500 (= 2,5 kg)"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('actual_weight_grams') border-red-500 @enderror">
                            <p class="text-xs text-gray-400 mt-1">Berat minimal tercatat 2 kg, dibulatkan ke atas per 0,5 kg.</p>
                            @error('actual_weight_grams')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <div>
                            <label for="item_count" class="block text-sm font-semibold text-gray-700 mb-1">
                                Jumlah Item <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="item_count" id="item_count"
                                   min="1" max="1000" required
                                   value="{{ old('item_count') }}"
                                   placeholder="Masukkan jumlah item"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('item_count') border-red-500 @enderror">
                            @error('item_count')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
                        💾 Simpan Penimbangan
                    </button>
                </form>
            @else
                <p class="text-gray-400 text-sm">
                    @if($order->status->value === 'waiting_pickup')
                        ⏳ Order harus dijemput dulu (status "Sudah dijemput") sebelum bisa ditimbang.
                    @else
                        ⏳ Belum ditimbang.
                    @endif
                </p>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Aksi --}}
    <div class="space-y-6">
        {{-- Update Status --}}
        <div class="bg-white rounded-xl shadow border p-6">
            <h2 class="text-lg font-bold mb-4">🔄 Update Status</h2>

            @if($order->isDelivered())
                <div class="bg-gray-50 border border-gray-200 text-gray-500 rounded-lg p-3 text-sm text-center">
                    🔒 Order sudah diserahkan. Tidak bisa diubah lagi.
                </div>
            @else
                @php
                    $nextStatus = $order->status->next();
                @endphp

                @if($nextStatus)
                    @if($nextStatus === \App\Enums\OrderStatus::Processing && !$order->isWeighed())
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg p-3 text-sm mb-3">
                            ⚠️ Penimbangan wajib diselesaikan sebelum order bisa masuk tahap "Sedang diproses".
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.orders.status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $nextStatus->value }}">

                        <p class="text-sm text-gray-500 mb-3">
                            Status berikutnya: <strong class="text-gray-700">{{ $nextStatus->label() }}</strong>
                        </p>

                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ ($nextStatus === \App\Enums\OrderStatus::Processing && !$order->isWeighed()) ? 'disabled' : '' }}>
                            Ubah ke "{{ $nextStatus->label() }}"
                        </button>

                        @error('status')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                @endif
            @endif
        </div>

        {{-- Update Pembayaran --}}
        <div class="bg-white rounded-xl shadow border p-6">
            <h2 class="text-lg font-bold mb-4">💳 Status Pembayaran</h2>

            @if($order->isDelivered())
                <div class="bg-gray-50 border border-gray-200 text-gray-500 rounded-lg p-3 text-sm text-center">
                    🔒 Order terkunci.
                </div>
            @elseif(!$order->isWeighed())
                <p class="text-sm text-gray-400">⏳ Tagihan belum dihitung. Pembayaran baru bisa dicatat setelah penimbangan.</p>
            @else
                <form method="POST" action="{{ route('staff.orders.payment', $order) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="payment_status" class="block text-sm font-semibold text-gray-700 mb-1">Ubah Status Pembayaran</label>
                        <select name="payment_status" id="payment_status"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @foreach(\App\Enums\PaymentStatus::cases() as $ps)
                                <option value="{{ $ps->value }}" {{ $order->payment_status === $ps ? 'selected' : '' }}>
                                    {{ $ps->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-semibold transition">
                        Simpan Pembayaran
                    </button>

                    @error('payment_status')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </form>
            @endif
        </div>

        {{-- Delivered info --}}
        @if($order->delivered_at)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                ✅ Diserahkan pada: <strong>{{ $order->delivered_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</strong>
            </div>
        @endif
    </div>
</div>
@endsection
