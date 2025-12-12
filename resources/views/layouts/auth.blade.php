<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'DIKSERA - Digitalisasi Kompetensi, Sertifikasi & Evaluasi Perawat' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

    {{-- Fonts & CSS --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --blue-main:#2563eb;
            --blue-soft:#e0ebff;
            --text-dark:#0f172a;
            --text-muted:#6b7280;
        }

        *{
            box-sizing:border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background:
                radial-gradient(circle at top left, #dbeafe 0, transparent 55%),
                radial-gradient(circle at bottom right, #e0f2fe 0, transparent 55%),
                linear-gradient(135deg,#f9fbff,#ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-dark);
        }

        .auth-shell {
            width: 100%;
            max-width: 980px;
        }

        .auth-card {
            position: relative;
            border-radius: 28px;
            background: radial-gradient(circle at top left,rgba(255,255,255,0.97),rgba(248,250,252,0.94));
            border: 1px solid rgba(148,163,184,0.35);
            box-shadow:
                0 32px 80px rgba(15,23,42,0.18),
                0 0 0 1px rgba(255,255,255,0.7) inset;
            padding: 22px 24px;
            overflow: hidden;
            backdrop-filter: blur(20px);
        }

        .auth-card::before{
            content:"";
            position:absolute;
            inset:-60%;
            background:
                radial-gradient(circle at 0% 0%,rgba(59,130,246,0.11),transparent 55%),
                radial-gradient(circle at 100% 100%,rgba(56,189,248,0.12),transparent 55%);
            opacity:.9;
            pointer-events:none;
        }

        .auth-card > *{
            position:relative;
            z-index:1;
        }

        .logo-big {
            width: 86px;
            height: 86px;
            border-radius: 26px;
            background:
                radial-gradient(circle at 20% 0,#eff6ff,#3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(37,99,235,0.55);
            margin-bottom: 8px;
        }

        .logo-big img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .auth-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .auth-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .form-control-light,
        .form-select.form-control-light {
            background: rgba(248,250,252,0.96);
            border: 1px solid #d1d5db;
            font-size: 13px;
            border-radius: 10px;
        }

        .form-control-light:focus,
        .form-select.form-control-light:focus {
            background: #ffffff;
            border-color: var(--blue-main);
            box-shadow: 0 0 0 1px rgba(37,99,235,0.35);
        }

        .btn-solid-blue {
            border-radius: 999px;
            padding: 9px 18px;
            background: linear-gradient(135deg,#2563eb,#60a5fa);
            border: none;
            color: #f9fafb;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 16px 36px rgba(37,99,235,0.55);
        }

        .btn-solid-blue:hover{
            filter: brightness(1.03);
        }

        .btn-outline-secondary{
            border-radius:999px;
            font-size:13px;
            padding:8px 16px;
        }

        .bottom-link {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 10px;
            text-align: center;
        }

        .bottom-link a{
            color:#2563eb;
            text-decoration:none;
        }

        .bottom-link a:hover{
            text-decoration:underline;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: linear-gradient(120deg,rgba(37,99,235,0.06),rgba(59,130,246,0.12));
            font-size: 11px;
            color: #2563eb;
            padding: 2px 6px;
            border-radius: 999px;
        }

        .toggle-password-btn:hover{
            filter:brightness(1.05);
        }

        .is-invalid{
            border-color:#dc2626 !important;
        }

        .alert{
            border-radius:14px;
        }

        /* RESPONSIVE */
        @media (max-width: 992px){
            body{
                padding:16px;
            }
            .auth-shell{
                max-width: 100%;
            }
            .auth-card{
                border-radius:22px;
                padding:18px 16px;
            }
        }

        @media (max-width: 576px){
            body{
                padding:10px;
            }
            .auth-card{
                border-radius:18px;
                padding:14px 12px;
                box-shadow: 0 16px 40px rgba(15,23,42,0.18);
            }
            .auth-title{
                font-size:18px;
            }
            .logo-big{
                width:72px;
                height:72px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="auth-shell">
    <div class="auth-card">
        @yield('content')
    </div>
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
