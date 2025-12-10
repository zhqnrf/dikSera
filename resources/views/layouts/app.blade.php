<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'DIKSERA' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

    {{-- Fonts, Bootstrap & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* =========================================
        1. VARIABLES & GLOBAL RESET
       ========================================= */
        :root {
            --blue-main: #1d4ed8;
            --blue-soft: #e0edff;
            --blue-soft-2: #f3f6ff;
            --border-soft: #d0d7ee;
            --text-main: #111827;
            --text-muted: #6b7280;
            --sidebar-width: 260px;
            --sidebar-width-collapsed: 80px;
            --header-height: 70px;
            --transition-speed: 0.3s;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', system-ui, sans-serif;
            background: radial-gradient(circle at top, #e0ebff 0, #f9fbff 45%, #ffffff 100%);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* =========================================
       2. LAYOUT UTAMA (SHARED)
       ========================================= */
        .app-shell {
            min-height: 100vh;
            display: flex;
            gap: 16px;
            padding: 16px;
            position: relative;
        }

        .app-main {
            flex: 1;
            background: #ffffff;
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            transition: margin-left var(--transition-speed);
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* =========================================
       3. SIDEBAR COMPONENTS (SHARED)
       ========================================= */
        .app-sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border: 1px solid var(--border-soft);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            padding: 18px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;

            /* Default Positioning (Desktop) */
            position: sticky;
            top: 16px;
            height: calc(100vh - 32px);
            border-radius: 24px;
            z-index: 100;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Scrollbar Cantik */
        .app-sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .app-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* Brand Section */
        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-left: 4px;
            overflow: hidden;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 12px;
            background: radial-gradient(circle at 30% 0, #eff6ff, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .brand-info {
            transition: opacity 0.2s, width 0.2s;
            white-space: nowrap;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
        }

        .brand-caption {
            font-size: 10px;
            color: var(--text-muted);
            max-width: 140px;
            line-height: 1.2;
        }

        /* Navigation Header */
        .nav-section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin: 10px 0 5px 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* Single Link Style */
        .nav-linkx {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            padding: 10px 12px;
            border-radius: 14px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
            cursor: pointer;
        }

        .nav-linkx i {
            font-size: 18px;
            min-width: 24px;
            text-align: center;
            color: #6b7280;
            transition: color 0.2s;
        }

        .nav-linkx:hover,
        .nav-dropdown:hover {
            background: #f3f6ff;
            color: #1d4ed8;
        }

        .nav-linkx:hover i,
        .nav-dropdown:hover i {
            color: #1d4ed8;
        }

        .nav-linkx.active {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.25);
        }

        .nav-linkx.active i {
            color: #fff;
        }

        /* Footer */
        .sidebar-footer {
            margin-top: auto;
            font-size: 10px;
            color: #9ca3af;
            padding-left: 12px;
            white-space: nowrap;
        }

        /* =========================================
       4. DROPDOWN & ANIMATIONS (SHARED)
       ========================================= */
        .nav-dropdown {
            /* Menggunakan style dasar nav-linkx agar konsisten */
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            padding: 10px 12px;
            border-radius: 14px;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        /* Ikon panah (chevron) di kanan */
        .dropdown-icon {
            transition: transform 0.3s ease;
            font-size: 12px;
        }

        .app-shell.is-collapsed .dropdown-icon {
            display: none;
        }

        .nav-submenu {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            padding-left: 20px;
            transform: translateY(-10px);
            transition: all 0.4s ease;
        }

        .nav-submenu.show {
            max-height: 500px;
            /* Cukup tinggi untuk konten */
            opacity: 1;
            transform: translateY(0);
            margin-top: 5px;
            display: block;
            /* Pastikan block untuk flow */
        }

        .nav-sublink {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            padding: 8px 12px;
            border-radius: 12px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .nav-sublink:hover {
            color: var(--blue-main);
            background: var(--blue-soft-2);
        }

        /* =========================================
       5. DESKTOP SPECIFIC (min-width: 992px)
       ========================================= */
        @media (min-width: 992px) {

            /* Floating Toggle Button */
            .sidebar-wrapper {
                position: relative;
            }

            .sidebar-toggle-btn {
                position: absolute;
                top: 20px;
                right: -16px;
                transform: translateX(50%);
                width: 32px;
                height: 32px;
                background: #fff;
                border: 1px solid #d1d5db;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 999;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: all .3s ease;
            }

            .sidebar-toggle-btn i {
                transition: transform 0.4s;
                font-size: 14px;
                font-weight: 700;
            }

            .sidebar-toggle-btn:hover {
                transform: translateX(50%) scale(1.15);
                /* Keep translateX logic */
                border-color: var(--blue-main);
                box-shadow: 0 0 0 4px var(--blue-soft);
            }

            /* --- COLLAPSED (Desktop) --- */
            .app-shell.is-collapsed .app-sidebar {
                width: var(--sidebar-width-collapsed);
                padding: 18px 8px;
            }

            /* Hide texts smoothly */
            .app-shell.is-collapsed .brand-info,
            .app-shell.is-collapsed .link-text,
            .app-shell.is-collapsed .nav-section-title,
            .app-shell.is-collapsed .sidebar-footer,
            .app-shell.is-collapsed .dropdown-icon {
                opacity: 0;
                width: 0;
                overflow: hidden;
                pointer-events: none;
                transition: opacity .2s ease, width .2s ease;
            }

            /* Brand container fix */
            .app-shell.is-collapsed .brand-row {
                justify-content: center;
                gap: 0;
                padding-left: 0;
            }

            /* Link formatting */
            .app-shell.is-collapsed .nav-linkx,
            .app-shell.is-collapsed .nav-dropdown {
                justify-content: center;
                padding: 12px 0 !important;
                gap: 0;
            }

            /* Icon centering */
            .app-shell.is-collapsed .nav-linkx i,
            .app-shell.is-collapsed .nav-dropdown i {
                margin: 0 auto;
            }

            /* Remove spacing for titles */
            .app-shell.is-collapsed .nav-section-title {
                margin: 0;
                padding: 0;
            }

            /* Disable submenu */
            .app-shell.is-collapsed .nav-submenu {
                display: none !important;
            }


            /* Sembunyikan elemen mobile di desktop */
            .header-toggle,
            .mobile-overlay {
                display: none !important;
            }
        }

        /* =========================================
       6. MOBILE SPECIFIC (max-width: 991px)
       ========================================= */
        @media (max-width: 991px) {
            .app-shell {
                padding: 0;
                gap: 0;
                display: block;
                /* Stack layout */
            }

            /* Sidebar Logic: Off-Canvas */
            .app-sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                /* Sembunyi total */
                height: 100vh;
                width: 280px !important;
                /* Lebar fix di mobile */
                border-radius: 0;
                border-right: 1px solid var(--border-soft);
                border-top: none;
                border-bottom: none;
                transition: left 0.3s ease-in-out;
                z-index: 1051;
                /* Di atas overlay */
            }

            .app-sidebar.mobile-active {
                left: 0;
                /* Muncul */
            }

            /* Toggle & Button Logic */
            .sidebar-toggle-btn {
                display: none;
                /* Tidak butuh tombol float di mobile */
            }

            .header-toggle {
                display: block;
                font-size: 24px;
                cursor: pointer;
                color: var(--text-main);
            }

            /* Main Content */
            .app-main {
                border-radius: 0;
                border: none;
                min-height: 100vh;
                padding: 16px;
            }

            /* Pastikan elemen sidebar selalu terlihat di mobile (reset collapsed logic) */
            .brand-info,
            .link-text,
            .nav-section-title,
            .sidebar-footer,
            .dropdown-icon {
                display: block !important;
                opacity: 1 !important;
                width: auto !important;
            }

            .nav-linkx,
            .nav-dropdown {
                justify-content: flex-start !important;
                /* Rata kiri kembali */
            }

            /* Mobile Overlay */
            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1040;
                display: none;
                backdrop-filter: blur(2px);
                opacity: 0;
                transition: opacity 0.3s;
            }

            .mobile-overlay.show {
                display: block;
                opacity: 1;
            }

            /* User Profile Pill di Mobile */
            .user-pill {
                font-size: 12px;
                padding: 6px 12px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 30px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .user-dot {
                width: 8px;
                height: 8px;
                background: #22c55e;
                border-radius: 50%;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Overlay Gelap untuk Mobile --}}
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>

    <div class="app-shell" id="appShell">

        <div class="sidebar-wrapper">
            <div class="sidebar-toggle-btn" onclick="toggleDesktopSidebar()">
                <i class="bi bi-chevron-left"></i>
            </div>

            <aside class="app-sidebar" id="appSidebar">
                <div>
                    <div class="brand-row">
                        <div class="brand-logo">
                            <img src="{{ asset('icon.png') }}" alt="Logo">
                        </div>
                        <div class="brand-info">
                            <div class="brand-name">DIKSERA</div>
                            <div class="brand-caption">Digitalisasi Kompetensi</div>
                        </div>
                    </div>

                    {{-- MENU PERAWAT --}}
                    @if (Auth::check() && Auth::user()->role === 'perawat')
                        <div class="nav-section-title">Umum</div>
                        <a href="{{ route('dashboard') }}"
                            class="nav-linkx {{ isset($menu) && $menu === 'dashboard' ? 'active' : '' }}">
                            <i class="bi bi-grid-fill"></i> <span class="link-text">Dashboard</span>
                        </a>

                        <div class="nav-section-title">Menu Utama</div>

                        {{-- DROPDOWN MASTER DATA --}}
                        <div class="nav-linkx nav-dropdown" data-dropdown="#submenu-master">
                            <i class="bi bi-folder-fill"></i>
                            <span class="link-text">Pelengkapan Data</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </div>

                        <div id="submenu-master" class="nav-submenu">

                            <a href="{{ route('perawat.drh') }}"
                                class="nav-linkx {{ request()->routeIs('perawat.drh') ? 'active' : '' }}">
                                <i class="bi bi-person-vcard-fill"></i> DRH & Profil
                            </a>

                            <a href="{{ route('perawat.pelatihan.index') }}"
                                class="nav-linkx {{ request()->routeIs('perawat.pelatihan.*') ? 'active' : '' }}">
                                <i class="bi bi-award-fill"></i> Pelatihan
                            </a>

                            <a href="{{ route('perawat.pekerjaan.index') }}"
                                class="nav-linkx {{ request()->routeIs('perawat.pekerjaan.*') ? 'active' : '' }}">
                                <i class="bi bi-briefcase-fill"></i> Pekerjaan
                            </a>

                            <a href="{{ route('perawat.keluarga.index') }}"
                                class="nav-linkx {{ request()->routeIs('perawat.keluarga.*') ? 'active' : '' }}">
                                <i class="bi bi-people-fill"></i> Keluarga
                            </a>

                            <a href="{{ route('perawat.organisasi.index') }}"
                                class="nav-linkx {{ request()->routeIs('perawat.organisasi.*') ? 'active' : '' }}">
                                <i class="bi bi-diagram-3-fill"></i> Organisasi
                            </a>

                            <a href="{{ route('perawat.tandajasa.index') }}"
                                class="nav-linkx {{ request()->routeIs('perawat.tandajasa.*') ? 'active' : '' }}">
                                <i class="bi bi-star-fill"></i> Tanda Jasa
                            </a>
                        </div>

                        {{-- DROPDOWN 2: DOKUMEN (BARU DITAMBAHKAN) --}}
                        @php
                            $isDokumenActive = request()->routeIs('perawat.lisensi.*') ||
                                               request()->routeIs('perawat.str.*') ||
                                               request()->routeIs('perawat.sip.*') ||
                                               request()->routeIs('perawat.tambahan.*');
                        @endphp

                        <div class="nav-linkx nav-dropdown {{ $isDokumenActive ? 'active' : '' }}" data-dropdown="#submenu-dokumen">
                            <i class="bi bi-folder-fill"></i>
                            <span class="link-text">Dokumen</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </div>

                        <div id="submenu-dokumen" class="nav-submenu {{ $isDokumenActive ? 'show' : '' }}">

                            {{-- Lisensi --}}
                            <a href="{{ route('perawat.lisensi.index') }}"
                               class="nav-linkx {{ request()->routeIs('perawat.lisensi.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-fill"></i> Dokumen Lisensi
                            </a>

                            {{-- STR --}}
                            <a href="{{ route('perawat.str.index') }}"
                               class="nav-linkx {{ request()->routeIs('perawat.str.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-text-fill"></i> Dokumen STR
                            </a>

                            {{-- SIP --}}
                            <a href="{{ route('perawat.sip.index') }}"
                               class="nav-linkx {{ request()->routeIs('perawat.sip.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-check-fill"></i> Dokumen SIP
                            </a>

                            {{-- Data Tambahan --}}
                            <a href="{{ route('perawat.tambahan.index') }}"
                               class="nav-linkx {{ request()->routeIs('perawat.tambahan.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-plus-fill"></i> Data Tambahan
                            </a>

                        </div>

                        <div class="nav-section-title">Lainnya</div>
                        <a href="{{ route('perawat.telegram.link') }}" class="nav-linkx">
                            <i class="bi bi-gear-fill"></i>
                            <span class="link-text">Telegram</span>
                        </a>
                    @endif


                    {{-- MENU ADMIN --}}
                    @if (Auth::check() && Auth::user()->role === 'admin')
                        <div class="nav-section-title">Admin Panel</div>

                        <a href="{{ route('dashboard.admin') }}"
                            class="nav-linkx {{ isset($menu) && $menu === 'admin' ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> {{-- Icon baru --}}
                            <span class="link-text">Dashboard</span>
                        </a>

                        <a href="{{ route('admin.perawat.index') }}"
                            class="nav-linkx {{ request()->routeIs('admin.perawat.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> {{-- Icon baru --}}
                            <span class="link-text">Data Perawat</span>
                        </a>

                        <div class="nav-section-title">Lainnya</div>
                        <a href="{{ route('admin.profile.index') }}" class="nav-linkx">
                            <i class="bi bi-gear-fill"></i>
                            <span class="link-text">Profile</span>
                        </a>
                    @endif
                </div>

                <div class="sidebar-footer">
                    &copy; {{ date('Y') }} DIKSERA<br>
                    <span>Komite Keperawatan</span>
                </div>
            </aside>
        </div>

        <main class="app-main">
            <div class="main-header">
                <div class="d-flex align-items-center gap-3">
                    {{-- Tombol Hamburger Mobile --}}
                    <i class="bi bi-list header-toggle" onclick="openMobileSidebar()"></i>

                    <div>
                        <div class="page-title">{{ $pageTitle ?? 'Dashboard' }}</div>
                        @isset($pageSubtitle)
                            <div class="page-subtitle">{{ $pageSubtitle }}</div>
                        @endisset
                    </div>
                </div>

                @php($user = Auth::user())
                @if ($user)
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-pill d-none d-md-flex">
                            <span class="user-dot"></span>
                            <span class="fw-medium">{{ $user->name }}</span>
                            <small class="text-muted ms-1">({{ strtoupper($user->role) }})</small>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="bi bi-box-arrow-right"></i>
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

    @if (session('swal'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire(@json(session('swal')));
            });
        </script>
    @endif

    <script>
        const appShell = document.getElementById('appShell');
        const appSidebar = document.getElementById('appSidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const STORAGE_KEY = 'diksera_sidebar_state';

        // 1. Load Desktop State
        if (window.innerWidth > 991) {
            const savedState = localStorage.getItem(STORAGE_KEY);
            if (savedState === 'collapsed') {
                appShell?.classList.add('is-collapsed');
            }
        }

        // 2. Desktop Toggle Logic
        function toggleDesktopSidebar() {
            appShell.classList.toggle('is-collapsed');
            if (appShell.classList.contains('is-collapsed')) {
                localStorage.setItem(STORAGE_KEY, 'collapsed');
            } else {
                localStorage.setItem(STORAGE_KEY, 'expanded');
            }
        }

        // 3. Mobile Toggle Logic
        function openMobileSidebar() {
            appSidebar.classList.add('mobile-active');
            mobileOverlay.classList.add('show');
        }

        function closeMobileSidebar() {
            appSidebar.classList.remove('mobile-active');
            mobileOverlay.classList.remove('show');
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                closeMobileSidebar();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-dropdown]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();

                    const targetSelector = btn.dataset.dropdown;
                    const target = document.querySelector(targetSelector);

                    if (target) {
                        target.classList.toggle('show');
                    } else {
                        console.warn('Target dropdown tidak ditemukan:', targetSelector);
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
