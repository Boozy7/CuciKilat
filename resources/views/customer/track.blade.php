@extends('layouts.app')

@section('title', 'Lacak Order')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">🔍 Lacak Order</h1>

    <form method="GET" action="{{ route('orders.track.form') }}" class="bg-white rounded-xl shadow p-6 border mb-6">
        <label for="order_code" class="block text-sm font-semibold text-gray-700 mb-2">Masukkan Kode Order</label>
        <div class="flex gap-2">
            <input type="text" name="order_code" id="order_code"
                   value="{{ $orderCode ?? '' }}"
                   placeholder="Contoh: CK-20261121-ABCDEF"
                   required
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold text-sm transition">
                Lacak
            </button>
        </div>
    </form>

    @if(isset($orderCode) && $orderCode && !isset($order))
        <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 text-sm text-center">
            ❌ Kode order <strong class="font-mono">{{ $orderCode }}</strong> tidak ditemukan. Pastikan kode yang dimasukkan benar.
        </div>
    @endif

    @if(isset($order))
        <div class="bg-white rounded-xl shadow border overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-4">
                <p class="text-xs opacity-70 uppercase tracking-wider">Kode Order</p>
                <p class="text-2xl font-mono font-bold tracking-widest">{{ $order->order_code }}</p>
            </div>

            <div class="p-6 space-y-4">
                {{-- Status Badge --}}
                <div>
                    <p class="text-xs text-gray-400 mb-1">Status Cucian</p>
                    @php
                        $statusColor = match($order->status->value) {
                            'waiting_pickup' => 'bg-yellow-100 text-yellow-700',
                            'picked_up' => 'bg-blue-100 text-blue-700',
                            'processing' => 'bg-indigo-100 text-indigo-700',
                            'ready_for_delivery' => 'bg-green-100 text-green-700',
                            'delivered' => 'bg-gray-100 text-gray-700',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusColor }}">
                        {{ $order->status->label() }}
                    </span>
                </div>

                {{-- Status Progress --}}
                <div>
                    <p class="text-xs text-gray-400 mb-2">Progres</p>
                    @php
                        $allStatuses = \App\Enums\OrderStatus::cases();
                        $currentIndex = array_search($order->status, $allStatuses);
                    @endphp
                    <div class="flex items-center gap-1">
                        @foreach($allStatuses as $i => $st)
                            <div class="flex-1 h-2 rounded-full {{ $i <= $currentIndex ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                        @foreach($allStatuses as $st)
                            <span>{{ $st->label() }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Layanan --}}
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Layanan:</span>
                    <span class="font-semibold">{{ $order->service->name }}</span>
                </div>

                {{-- Status Pembayaran --}}
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Status Pembayaran:</span>
                    @php
                        $payColor = match($order->payment_status->value) {
                            'unpaid' => 'text-red-600',
                            'pending' => 'text-yellow-600',
                            'paid' => 'text-green-600',
                            default => 'text-gray-600',
                        };
                    @endphp
                    <span class="font-semibold {{ $payColor }}">{{ $order->payment_status->label() }}</span>
                </div>

                {{-- Tagihan & Estimasi --}}
                @if($order->isWeighed())
                    @if($order->service->isPerKg())
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Berat Tercatat:</span>
                            <span class="font-semibold">{{ $order->billed_weight_kg }} kg</span>
                        </div>
                    @else
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Jumlah Item:</span>
                            <span class="font-semibold">{{ $order->item_count }} item</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Tagihan:</span>
                        <span class="font-bold text-lg text-blue-600">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Estimasi Selesai:</span>
                        <span class="font-semibold">{{ $order->estimated_completion_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</span>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg p-3 text-sm">
                        ⏳ Berat dan tagihan akan muncul setelah cucian dijemput dan ditimbang oleh petugas.
                    </div>
                @endif

                @if($order->delivered_at)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Diserahkan pada:</span>
                        <span class="font-semibold">{{ $order->delivered_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</span>
                    </div>
                @endif

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Dibuat pada:</span>
                    <span>{{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</span>
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 text-center mt-4">
            Data pribadi (alamat & nomor HP) tidak ditampilkan demi keamanan.
        </p>
    @endif
</div>
@endsection
