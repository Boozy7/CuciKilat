@extends('layouts.app')

@section('title', 'Beranda')
@section('main_class', 'w-full px-4 sm:px-8 lg:px-12 py-6')

@section('content')
<div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    {{-- MAIN WHITE WORKSPACE SURFACE (Clean, Rounded, Soft Shadow) --}}
    <div class="lg:col-span-8 xl:col-span-9 bg-white rounded-3xl p-6 sm:p-8 lg:p-10 ck-card-shadow">

        {{-- Top Header Row --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 pb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#3B1A14]">
                    CuciKilat Antar-Jemput
                </h1>
                <p class="text-sm text-[#866962] mt-1">
                    Pesan jemput dari rumah, timbang transparan, lacak langsung.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('orders.track.form') }}"
                   aria-label="Lacak Order"
                   title="Lacak Order"
                   class="w-11 h-11 rounded-xl bg-[#FAF7F2] hover:bg-[#E6F1F8] text-[#3B1A14] hover:text-[#1F4E6C] inline-flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                    </svg>
                </a>
                <a href="{{ route('orders.create') }}"
                   class="inline-flex items-center justify-center bg-[#5A271E] hover:bg-[#421B14] text-[#FAF7F2] px-5 py-3 rounded-xl text-sm font-semibold transition-colors min-h-[44px]">
                    Pesan Jemput
                </a>
            </div>
        </div>

        {{-- 3 COLORED SERVICE CARDS (Soft Blue, Mahogany, Soft Yellow) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @forelse($services as $index => $service)
                @php
                    $cardTheme = match($index % 3) {
                        0 => [
                            'bg' => 'bg-[#7FB5D5] hover:bg-[#71AAD0]',
                            'title' => 'text-white',
                            'meta' => 'text-white/80',
                            'barTrack' => 'bg-white/30',
                            'barFill' => 'bg-white w-2/3',
                            'pill' => 'bg-white/20 text-white',
                        ],
                        1 => [
                            'bg' => 'bg-[#5A271E] hover:bg-[#4C2018]',
                            'title' => 'text-[#FAF7F2]',
                            'meta' => 'text-[#FAF7F2]/75',
                            'barTrack' => 'bg-[#FAF7F2]/25',
                            'barFill' => 'bg-[#F3D88B] w-11/12',
                            'pill' => 'bg-[#FAF7F2]/15 text-[#FAF7F2]',
                        ],
                        default => [
                            'bg' => 'bg-[#F3D88B] hover:bg-[#EDCE76]',
                            'title' => 'text-[#3B1A14]',
                            'meta' => 'text-[#6B4D0E]',
                            'barTrack' => 'bg-[#3B1A14]/15',
                            'barFill' => 'bg-[#5A271E] w-1/2',
                            'pill' => 'bg-[#3B1A14]/10 text-[#3B1A14]',
                        ],
                    };
                @endphp
                <a href="{{ route('orders.create') }}"
                   class="group rounded-2xl p-6 flex flex-col justify-between min-h-[188px] transition-colors cursor-pointer {{ $cardTheme['bg'] }}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold {{ $cardTheme['pill'] }}">
                            {{ $service->pricing_type->label() }}
                        </span>
                        <span class="text-xs font-mono {{ $cardTheme['meta'] }}">
                            {{ $service->estimated_days }} hari
                        </span>
                    </div>

                    <div class="my-5">
                        <h2 class="text-xl font-bold tracking-tight {{ $cardTheme['title'] }}">
                            {{ $service->name }}
                        </h2>
                        <p class="font-mono text-2xl font-bold mt-1.5 {{ $cardTheme['title'] }}">
                            Rp {{ number_format($service->base_price, 0, ',', '.') }}<span class="text-xs font-normal opacity-80">/{{ $service->isPerKg() ? 'kg' : 'item' }}</span>
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-[11px] font-medium mb-1.5 {{ $cardTheme['meta'] }}">
                            <span>{{ $service->surcharge_percent > 0 ? '+' . $service->surcharge_percent . '% express' : 'Tarif dasar' }}</span>
                            <span class="group-hover:translate-x-0.5 transition-transform">Pilih &rarr;</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full overflow-hidden {{ $cardTheme['barTrack'] }}">
                            <div class="h-full rounded-full {{ $cardTheme['barFill'] }}"></div>
                        </div>
                    </div>
                </a>
            @empty
                <p class="col-span-3 text-center text-sm text-[#866962] py-8">Belum ada layanan tersedia.</p>
            @endforelse
        </div>

        {{-- LOWER SECTION: Cara Kerja (Vertical Accent Bars) + Quick Track & Aturan --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-7 mt-10">

            {{-- Left: Cara Kerja (Tasks-style white cards with vertical accent bar) --}}
            <div class="md:col-span-6 space-y-3.5">
                <h2 class="text-base font-bold text-[#3B1A14] mb-4">Cara Kerja</h2>

                <div class="bg-white rounded-2xl p-4 pl-5 relative overflow-hidden ck-item-shadow border border-[#3B1A14]/5">
                    <span class="absolute left-0 top-3 bottom-3 w-1 rounded-r-full bg-[#7FB5D5]" aria-hidden="true"></span>
                    <p class="font-semibold text-sm text-[#3B1A14]">1. Pesan Jemput</p>
                    <p class="text-xs text-[#866962] mt-0.5">Isi alamat penjemputan dan dapatkan kode order unik.</p>
                </div>

                <div class="bg-white rounded-2xl p-4 pl-5 relative overflow-hidden ck-item-shadow border border-[#3B1A14]/5">
                    <span class="absolute left-0 top-3 bottom-3 w-1 rounded-r-full bg-[#5A271E]" aria-hidden="true"></span>
                    <p class="font-semibold text-sm text-[#3B1A14]">2. Timbang di Outlet</p>
                    <p class="text-xs text-[#866962] mt-0.5">Petugas mencatat berat aktual, tagihan dihitung otomatis.</p>
                </div>

                <div class="bg-white rounded-2xl p-4 pl-5 relative overflow-hidden ck-item-shadow border border-[#3B1A14]/5">
                    <span class="absolute left-0 top-3 bottom-3 w-1 rounded-r-full bg-[#F3D88B]" aria-hidden="true"></span>
                    <p class="font-semibold text-sm text-[#3B1A14]">3. Lacak &amp; Terima</p>
                    <p class="text-xs text-[#866962] mt-0.5">Pantau progres dengan kode order hingga cucian diantar.</p>
                </div>
            </div>

            {{-- Right: Aturan Singkat + Quick Track Form --}}
            <div class="md:col-span-6 flex flex-col justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-[#3B1A14] mb-4">Ketentuan Kiloan</h2>
                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="bg-[#FAF7F2] rounded-2xl p-4">
                            <p class="font-mono text-xl font-bold text-[#3B1A14]">2 kg</p>
                            <p class="text-xs text-[#866962] mt-1">Minimal tagihan kiloan</p>
                        </div>
                        <div class="bg-[#FAF7F2] rounded-2xl p-4">
                            <p class="font-mono text-xl font-bold text-[#3B1A14]">0,5 kg</p>
                            <p class="text-xs text-[#866962] mt-1">Pembulatan ke atas</p>
                        </div>
                    </div>
                </div>

                {{-- Quick Track Box --}}
                <form method="GET" action="{{ route('orders.track.form') }}" class="bg-[#FAF7F2] rounded-2xl p-5">
                    <label for="quick_order_code" class="block text-sm font-bold text-[#3B1A14] mb-2">
                        Lacak Cepat
                    </label>
                    <div class="flex gap-2">
                        <input type="text"
                               name="order_code"
                               id="quick_order_code"
                               placeholder="Kode order (CK-...)"
                               required
                               class="flex-1 min-w-0 bg-white rounded-xl px-3.5 py-2.5 text-sm font-mono uppercase text-[#3B1A14] placeholder:font-sans placeholder:text-[#866962]/70 border border-[#3B1A14]/10 min-h-[44px]">
                        <button type="submit"
                                class="cursor-pointer bg-[#5A271E] hover:bg-[#421B14] text-[#FAF7F2] px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors min-h-[44px] shrink-0">
                            Cek
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- RIGHT PANEL: Soft Blue Vertical Pinstripe + Alur Status --}}
    <aside class="lg:col-span-4 xl:col-span-3 ck-pinstripe-blue rounded-3xl p-6 sm:p-7">
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-5 sm:p-6 ck-item-shadow">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-base font-bold text-[#3B1A14]">Alur Status</h2>
                <span class="text-xs font-mono text-[#866962]">5 Tahap</span>
            </div>

            <ol class="space-y-5">
                @php
                    $timelineSteps = [
                        ['num' => '01', 'title' => 'Menunggu dijemput', 'desc' => 'Permintaan tercatat', 'bar' => 'bg-[#F3D88B]'],
                        ['num' => '02', 'title' => 'Sudah dijemput', 'desc' => 'Menuju penimbangan', 'bar' => 'bg-[#7FB5D5]'],
                        ['num' => '03', 'title' => 'Sedang diproses', 'desc' => 'Tagihan sudah keluar', 'bar' => 'bg-[#7FB5D5]'],
                        ['num' => '04', 'title' => 'Siap diantar', 'desc' => 'Cucian selesai rapi', 'bar' => 'bg-[#F3D88B]'],
                        ['num' => '05', 'title' => 'Sudah diserahkan', 'desc' => 'Order selesai & terkunci', 'bar' => 'bg-[#5A271E]'],
                    ];
                @endphp

                @foreach($timelineSteps as $step)
                    <li class="flex items-center gap-4">
                        <span class="font-mono text-xs font-semibold text-[#866962] w-6 shrink-0">{{ $step['num'] }}</span>
                        <span class="w-1 h-9 rounded-full shrink-0 {{ $step['bar'] }}" aria-hidden="true"></span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[#3B1A14] leading-snug">{{ $step['title'] }}</p>
                            <p class="text-xs text-[#866962] mt-0.5">{{ $step['desc'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="mt-7 pt-5 border-t border-[#3B1A14]/10">
                <p class="text-xs text-[#866962] leading-relaxed">
                    Privasi terjaga: nama, nomor HP, dan alamat tidak pernah muncul di halaman lacak publik.
                </p>
            </div>
        </div>
    </aside>

</div>
@endsection
