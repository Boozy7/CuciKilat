<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CuciKilat') — CuciKilat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                            display: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                            mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
                        }
                    }
                }
            }
        </script>
    @endif
    <style>
        .ck-pinstripe-blue {
            background-image: repeating-linear-gradient(
                to right,
                #DCEAF4 0px,
                #DCEAF4 10px,
                #EBF3F9 10px,
                #EBF3F9 18px,
                rgba(74, 32, 24, 0.08) 18px,
                rgba(74, 32, 24, 0.08) 19px
            );
        }
        .ck-card-shadow { box-shadow: 0 8px 30px rgba(90, 39, 30, 0.05); }
        .ck-item-shadow { box-shadow: 0 4px 16px rgba(90, 39, 30, 0.04); }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid #5A271E;
            outline-offset: 2px;
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-[#FAF7F2] text-[#3B1A14] font-sans antialiased flex flex-col justify-between selection:bg-[#F3D88B] selection:text-[#3B1A14]">

    {{-- Clean Modern Navbar --}}
    <header class="sticky top-0 z-50 bg-[#FAF7F2]/90 backdrop-blur-md">
        <nav class="w-full px-5 sm:px-8 lg:px-12 h-16 flex items-center justify-between gap-4" aria-label="Navigasi utama">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-[#3B1A14] font-bold text-lg tracking-tight cursor-pointer">
                <span class="inline-flex items-end gap-0.5 h-4" aria-hidden="true">
                    <span class="w-1 h-2.5 rounded-full bg-[#7FB5D5]"></span>
                    <span class="w-1 h-4 rounded-full bg-[#5A271E]"></span>
                    <span class="w-1 h-3 rounded-full bg-[#F3D88B]"></span>
                </span>
                <span>CuciKilat</span>
            </a>

            <div class="flex items-center gap-1 sm:gap-2 text-sm font-medium overflow-x-auto">
                @auth
                    <span class="hidden md:inline-flex items-center px-3 py-1 text-xs text-[#866962]">
                        {{ Auth::user()->name }}
                    </span>
                    <a href="{{ route('staff.orders.index') }}"
                       class="px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('staff.orders.*') ? 'bg-white text-[#3B1A14] font-semibold ck-item-shadow' : 'text-[#866962] hover:text-[#3B1A14]' }}">
                        Order
                    </a>
                    <a href="{{ route('staff.services.index') }}"
                       class="px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('staff.services.*') ? 'bg-white text-[#3B1A14] font-semibold ck-item-shadow' : 'text-[#866962] hover:text-[#3B1A14]' }}">
                        Layanan
                    </a>
                    <a href="{{ route('staff.reports.daily-revenue') }}"
                       class="px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('staff.reports.*') ? 'bg-white text-[#3B1A14] font-semibold ck-item-shadow' : 'text-[#866962] hover:text-[#3B1A14]' }}">
                        Laporan
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline ml-1">
                        @csrf
                        <button type="submit"
                                class="cursor-pointer px-4 py-2 rounded-xl bg-[#5A271E] hover:bg-[#421B14] text-[#FAF7F2] text-xs font-semibold transition-colors whitespace-nowrap">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ url('/') }}"
                       class="px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap {{ request()->is('/') ? 'bg-white text-[#3B1A14] font-semibold ck-item-shadow' : 'text-[#866962] hover:text-[#3B1A14]' }}">
                        Beranda
                    </a>
                    <a href="{{ route('orders.create') }}"
                       class="px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('orders.create') ? 'bg-white text-[#3B1A14] font-semibold ck-item-shadow' : 'text-[#866962] hover:text-[#3B1A14]' }}">
                        Pesan
                    </a>
                    <a href="{{ route('orders.track.form') }}"
                       class="px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('orders.track*') ? 'bg-white text-[#3B1A14] font-semibold ck-item-shadow' : 'text-[#866962] hover:text-[#3B1A14]' }}">
                        Lacak
                    </a>
                    <a href="{{ route('login') }}"
                       class="ml-1 px-4 py-2 rounded-xl bg-[#5A271E] hover:bg-[#421B14] text-[#FAF7F2] text-xs font-semibold transition-colors whitespace-nowrap">
                        Login Petugas
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    {{-- Flash & Validation Alerts --}}
    @if(session('success') || session('error') || $errors->any())
        <div class="w-full px-5 sm:px-8 lg:px-12 mt-4 space-y-3">
            @if(session('success'))
                <div role="status" class="bg-[#E6F1F8] text-[#1F4E6C] px-4 py-3.5 rounded-2xl text-sm font-medium ck-item-shadow">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div role="alert" class="bg-[#F9E4DF] text-[#7D2418] px-4 py-3.5 rounded-2xl text-sm font-medium ck-item-shadow">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="bg-[#F9E4DF] text-[#7D2418] px-4 py-3.5 rounded-2xl text-sm ck-item-shadow">
                    <p class="font-semibold mb-1">Periksa kembali data berikut:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Main Content --}}
    <main class="w-full flex-grow @yield('main_class', 'w-full px-5 sm:px-8 lg:px-12 py-6')">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="w-full px-5 sm:px-8 lg:px-12 py-6 text-xs text-[#866962] flex flex-col sm:flex-row items-center justify-between gap-2">
        <p>&copy; {{ date('Y') }} CuciKilat &mdash; Laundry Antar-Jemput Kiloan &amp; Satuan (K06)</p>
        <p class="font-mono">CuciKilat</p>
    </footer>

</body>
</html>
