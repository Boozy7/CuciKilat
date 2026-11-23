@extends('layouts.app')

@section('title', 'Lacak Order')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 pb-4 border-b border-[#4A2018]/15">
        <p class="font-mono text-xs uppercase tracking-widest text-[#7A4E45] mb-1">Pelacakan Publik</p>
        <h1 class="font-display text-3xl text-[#4A2018]">Lacak Order</h1>
    </div>

    <form method="GET" action="{{ route('orders.track.form') }}" class="bg-white rounded-lg border border-[#4A2018]/20 p-5 sm:p-6 mb-6">
        <label for="order_code" class="block text-sm font-semibold text-[#4A2018] mb-2">Masukkan Kode Order</label>
        <div class="flex flex-col sm:flex-row gap-2.5">
            <input type="text" name="order_code" id="order_code"
                   value="{{ $orderCode ?? '' }}"
                   placeholder="Contoh: CK-20261121-ABCDEF"
                   required
                   class="flex-1 bg-[#FAF8F4]/60 border border-[#4A2018]/25 rounded-lg px-3.5 py-2.5 text-sm font-mono uppercase text-[#4A2018] placeholder:text-[#7A4E45]/60 min-h-[44px] focus:bg-white focus:border-[#4A2018]">
            <button type="submit"
                    class="cursor-pointer bg-[#4A2018] hover:bg-[#33140E] text-[#FAF8F4] px-6 py-2.5 rounded-lg font-semibold text-sm transition-colors min-h-[44px]">
                Lacak
            </button>
        </div>
    </form>

    @if(isset($orderCode) && $orderCode && !isset($order))
        <div role="alert" class="bg-[#F7E3DE] border border-[#D8A497] text-[#7D2418] rounded-lg p-4 text-sm text-center">
            Kode order <strong class="font-mono">{{ $orderCode }}</strong> tidak ditemukan. Pastikan kode yang dimasukkan sudah benar.
        </div>
    @endif

    @if(isset($order))
        <div class="bg-white rounded-lg border border-[#4A2018]/20 overflow-hidden">
            {{-- Header Card --}}
            <div class="bg-[#4A2018] text-[#FAF8F4] px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-mono opacity-75 uppercase tracking-widest">Kode Order</p>
                    <p class="text-2xl font-mono font-semibold tracking-widest mt-0.5">{{ $order->order_code }}</p>
                </div>
                <span class="inline-flex self-start sm:self-center items-center px-3 py-1 rounded-full text-xs font-semibold {{ $order->status->badgeClasses() }}">
                    {{ $order->status->label() }}
                </span>
            </div>

            <div class="p-6 space-y-6">
                {{-- Status Progress Bar --}}
                <div>
                    <p class="text-xs font-mono uppercase tracking-wider text-[#7A4E45] mb-3">Tahap Pengerjaan</p>
                    @php
                        $allStatuses = \App\Enums\OrderStatus::cases();
                        $currentIndex = array_search($order->status, $allStatuses, true);
                    @endphp
                    <div class="grid grid-cols-5 gap-1.5">
                        @foreach($allStatuses as $i => $st)
                            <div>
                                <div class="h-2 rounded-full {{ $i <= $currentIndex ? 'bg-[#4A2018]' : 'bg-[#4A2018]/15' }}"></div>
                                <p class="text-[10px] leading-tight mt-1.5 {{ $i <= $currentIndex ? 'text-[#4A2018] font-semibold' : 'text-[#7A4E45]' }}">
                                    {{ $st->label() }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Detail Rows --}}
                <div class="border-t border-[#4A2018]/15 pt-4 divide-y divide-[#4A2018]/10 text-sm">
                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <span class="text-[#7A4E45]">Layanan</span>
                        <span class="font-semibold text-[#4A2018]">{{ $order->service->name }}</span>
                    </div>

                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <span class="text-[#7A4E45]">Status Pembayaran</span>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">
                            {{ $order->payment_status->label() }}
                        </span>
                    </div>

                    @if($order->isWeighed())
                        @if($order->service->isPerKg())
                            <div class="py-2.5 flex items-center justify-between gap-4">
                                <span class="text-[#7A4E45]">Berat Tagihan</span>
                                <span class="font-mono font-semibold text-[#4A2018]">{{ $order->billed_weight_kg }} kg</span>
                            </div>
                        @else
                            <div class="py-2.5 flex items-center justify-between gap-4">
                                <span class="text-[#7A4E45]">Jumlah Item</span>
                                <span class="font-mono font-semibold text-[#4A2018]">{{ $order->item_count }} item</span>
                            </div>
                        @endif

                        <div class="py-3 flex items-center justify-between gap-4">
                            <span class="text-[#7A4E45] font-medium">Total Tagihan</span>
                            <span class="font-mono font-semibold text-xl text-[#4A2018]">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-[#7A4E45]">Estimasi Selesai</span>
                            <span class="font-semibold text-[#1F4A68]">{{ $order->estimated_completion_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                    @endif

                    @if($order->delivered_at)
                        <div class="py-2.5 flex items-center justify-between gap-4">
                            <span class="text-[#7A4E45]">Diserahkan pada</span>
                            <span class="font-semibold text-[#4A2018]">{{ $order->delivered_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                    @endif

                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <span class="text-[#7A4E45]">Dibuat pada</span>
                        <span class="text-[#7A4E45]">{{ $order->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</span>
                    </div>
                </div>

                @if(!$order->isWeighed())
                    <div class="bg-[#F7E8B5]/80 border border-[#E5CE82] text-[#6B4C0B] rounded-lg p-3.5 text-sm">
                        Berat, tagihan final, dan estimasi selesai akan tampil setelah cucian dijemput dan ditimbang oleh petugas.
                    </div>
                @endif
            </div>
        </div>

        <p class="text-xs text-[#7A4E45] text-center mt-4">
            Data pribadi (nama, alamat, &amp; nomor kontak) tidak ditampilkan di halaman lacak publik demi keamanan.
        </p>
    @endif
</div>
@endsection
