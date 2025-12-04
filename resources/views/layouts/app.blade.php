<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'DIKSERA' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

    {{-- Fonts & Bootstrap --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --blue-main: #1d4ed8;
            --blue-soft: #e0edff;
            --blue-soft-2: #f3f6ff;
            --border-soft: #d0d7ee;
            --text-main: #111827;
            --text-muted: #6b7280;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(circle at top, #e0ebff 0, #f9fbff 45%, #ffffff 100%);
            color: var(--text-main);
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
            gap: 16px;
            padding: 16px;
        }

        .app-sidebar {
            width: 260px;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid var(--border-soft);
            box-shadow:
                0 18px 50px rgba(15, 23, 42, 0.1),
                0 0 0 1px rgba(148, 163, 184, 0.15);
            padding: 18px 16px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: radial-gradient(circle at 20% 0,#eff6ff,#3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(37,99,235,0.35);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
        }

        .brand-caption {
            font-size: 11px;
            color: var(--text-muted);
        }

        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: #9ca3af;
            margin-top: 6px;
            margin-bottom: 4px;
        }

        .nav-linkx {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 999px;
            color: #111827;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .nav-linkx span.dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #bfdbfe;
        }

        .nav-linkx.active {
            background: linear-gradient(135deg,#2563eb,#60a5fa);
            color: #f9fafb;
            box-shadow: 0 14px 32px rgba(37,99,235,0.45);
        }

        .nav-linkx.active span.dot { background: #eff6ff; }

        .nav-linkx:hover:not(.active) {
            background: #f3f6ff;
            border-color: #d1ddff;
        }

        .sidebar-footer {
            margin-top: auto;
            font-size: 11px;
            color: var(--text-muted);
        }

        .app-main {
            flex: 1;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid var(--border-soft);
            box-shadow:
                0 20px 55px rgba(15, 23, 42, 0.1),
                0 0 0 1px rgba(148, 163, 184, 0.16);
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-main);
        }

        .page-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .user-pill {
            font-size: 11px;
            border-radius: 999px;
            padding: 7px 12px;
            background: #f3f6ff;
            border: 1px solid #d1ddff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #22c55e;
        }

        .btn-logout {
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
        }

        .main-body {
            flex: 1;
            border-radius: 18px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 18px 18px;
        }

        @media (max-width: 900px) {
            .app-shell { flex-direction: column; padding: 10px; }
            .app-sidebar { width: 100%; flex-direction: row; align-items: center; gap: 10px; }
            .nav-section-title { display: none; }
            .sidebar-footer { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="app-shell">
    {{-- SIDEBAR --}}
    <aside class="app-sidebar">
        <div>
            <div class="brand-row mb-2">
                <div class="brand-logo">
                    <img src="{{ asset('icon.png') }}" alt="DIKSERA">
                </div>
                <div>
                    <div class="brand-name">DIKSERA</div>
                    <div class="brand-caption">
                        Digitalisasi Kompetensi, Sertifikasi & Evaluasi Perawat
                    </div>
                </div>
            </div>

            <div class="nav-section-title">Umum</div>
            <a href="{{ route('dashboard') }}" class="nav-linkx {{ (isset($menu) && $menu === 'dashboard') ? 'active' : '' }}">
                <span class="dot"></span>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-title">Master & Proses</div>
            <a href="#" class="nav-linkx">
                <span class="dot"></span>
                <span>DRH & Profil Perawat</span>
            </a>
            <a href="{{ route('perawat.pelatihan.index') }}" class="nav-linkx">
                <span class="dot"></span>
                <span>Pelatihan</span>
            </a>
            <a href="{{ route('perawat.organisasi.index') }}" class="nav-linkx">
                <span class="dot"></span>
                <span>Organisasi</span>
            </a>
            <a href="{{ route('perawat.tandajasa.index') }}" class="nav-linkx">
                <span class="dot"></span>
                <span>Tanda Jasa</span>
            </a>
            <a href="#" class="nav-linkx">
                <span class="dot"></span>
                <span>Dokumen Lisensi & Sertifikat</span>
            </a>
            <a href="#" class="nav-linkx">
                <span class="dot"></span>
                <span>Bank Soal & Ujian</span>
            </a>
            <a href="#" class="nav-linkx">
                <span class="dot"></span>
                <span>Wawancara Kompetensi</span>
            </a>

            <div class="nav-section-title">Lainnya</div>
            <a href="#" class="nav-linkx">
                <span class="dot"></span>
                <span>Pengaturan</span>
            </a>
        </div>

        <div class="sidebar-footer">
            &copy; {{ date('Y') }} DIKSERA<br>
            <span>Komite Keperawatan</span>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="app-main">
        <div class="main-header">
            <div>
                <div class="page-title">
                    {{ $pageTitle ?? 'Dashboard' }}
                </div>
                @isset($pageSubtitle)
                    <div class="page-subtitle">
                        {{ $pageSubtitle }}
                    </div>
                @endisset
            </div>

            @php($user = \Illuminate\Support\Facades\Auth::user())
            @if($user)
                <div class="d-flex align-items-center gap-2">
                    <div class="user-pill">
                        <span class="user-dot"></span>
                        <span style="font-weight:500;">{{ $user->name }}</span>
                        <span class="text-muted" style="font-size:10px;">{{ strtoupper($user->role) }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-logout">
                            Logout
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="main-body">
            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('swal'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire(@json(session('swal')));
});
</script>
@endif

@stack('scripts')
</body>
</html>
