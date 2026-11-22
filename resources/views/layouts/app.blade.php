<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CuciKilat') — CuciKilat</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-gray-50 text-gray-800 font-sans antialiased flex flex-col justify-between">

    {{-- Navbar --}}
    <nav class="bg-blue-600 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight flex items-center gap-2">
                🧺 CuciKilat
            </a>
            <div class="flex items-center gap-3 text-sm">
                @auth
                    <span class="hidden sm:inline opacity-80">{{ Auth::user()->name }}</span>
                    <a href="{{ route('staff.orders.index') }}" class="hover:underline">Order</a>
                    <a href="{{ route('staff.services.index') }}" class="hover:underline">Layanan</a>
                    <a href="{{ route('staff.reports.daily-revenue') }}" class="hover:underline">Laporan</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-xs font-semibold transition">Logout</button>
                    </form>
                @else
                    <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                    <a href="{{ route('orders.create') }}" class="hover:underline">Pesan</a>
                    <a href="{{ route('orders.track.form') }}" class="hover:underline">Lacak</a>
                    <a href="{{ route('login') }}" class="bg-white text-blue-600 px-3 py-1 rounded text-xs font-semibold hover:bg-blue-50 transition">Login Petugas</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    <div class="max-w-5xl mx-auto px-4 w-full">
        @if(session('success'))
            <div class="mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <p class="font-semibold mb-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Main Content --}}
    <main class="max-w-5xl mx-auto px-4 py-6 w-full flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="text-center text-xs text-gray-400 py-6 border-t mt-12">
        &copy; {{ date('Y') }} CuciKilat — Layanan Laundry Kiloan & Satuan (K06)
    </footer>

</body>
</html>
