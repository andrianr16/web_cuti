<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal E-Cuti Karyawan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold tracking-tight text-white flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                E-Cuti System
            </span>
        </div>
        <div>
            @if (Route::has('login'))
                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ auth()->user()->role === 'hrd' ? route('hrd.cuti.index') : route('cuti.index') }}"
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow transition">
                            Masuk / Log in
                        </a>
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <!-- Konten Utama Hero -->
    <main class="w-full max-w-4xl mx-auto px-6 py-16 text-center">
        <span class="px-3.5 py-1.5 text-xs font-semibold uppercase tracking-wider text-blue-400 bg-blue-950/80 border border-blue-800 rounded-full inline-block mb-6">
            Sistem Informasi Kepegawaian & HRD
        </span>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
            Pengajuan Izin & Cuti Karyawan Lebih Cepat, Terdata, dan Efisien.
        </h1>
        <p class="mt-6 text-base sm:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
            Portal terintegrasi untuk pengajuan hak cuti kerja, persetujuan cepat oleh tim personalia/HRD, serta monitoring kuota tahunan karyawan secara mandiri.
        </p>

        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="{{ route('login') }}" class="px-7 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-lg transition">
                Masuk ke Akun Anda &rarr;
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-500 border-t border-slate-800">
        &copy; {{ date('Y') }} Sistem E-Cuti Terintegrasi. Seluruh hak cipta dilindungi.
    </footer>

</body>
</html>