@extends('layouts.app')

@section('title', 'Data Lisensi Saya')

@push('styles')
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 14px;
        }

        /* --- Header --- */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }

        /* --- Action Area --- */
        .action-area {
            margin-bottom: 20px;
        }

        /* --- Table Card --- */
        .card-table {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .table-custom th {
            background-color: #f1f5f9;
            color: var(--text-gray);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-custom td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-custom tr:hover td {
            background-color: #f8fafc;
        }

        /* --- Typography --- */
        .license-name {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 0.95rem;
            display: block;
        }

        .license-sub {
            font-size: 0.8rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 4px;
        }

        .license-number {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        /* --- Badges --- */
        .badge-metode {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .bg-metode-interview {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .bg-metode-pg {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-status {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .bs-active {
            background: #dcfce7;
            color: #166534;
        }

        .bs-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .bs-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* --- Buttons --- */
        .btn-create {
            background-color: var(--primary-blue);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-create:hover {
            background-color: var(--primary-hover);
            color: white;
        }

        .btn-renew {
            margin-top: 8px;
            background-color: white;
            border: 1px solid #cbd5e1;
            color: var(--text-dark);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            width: 100%;
            transition: 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
        }

        .btn-renew:hover {
            border-color: #f59e0b;
            background-color: #fffbeb;
            color: #b45309;
        }

        .btn-renew.danger-renew:hover {
            border-color: #ef4444;
            background-color: #fef2f2;
            color: #b91c1c;
        }

        .btn-generate {
            color: var(--primary-blue);
            background: #e0f2fe;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-generate:hover {
            background: var(--primary-blue);
            color: white;
        }

        .btn-back {
            color: var(--text-gray);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: white;
            border-color: #e2e8f0;
            color: var(--text-dark);
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Data Lisensi Saya</h1>
                <p class="text-muted small mb-0">Kelola dan perpanjang masa berlaku lisensi (STR/SIP) Anda.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali Dashboard
            </a>
        </div>

        @if (session('success'))
            <div
                class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        {{-- LOGIC TOMBOL CREATE (Updated Logic) --}}
        <div class="action-area">
            @php
                $latestJob = $user->perawatPekerjaans()->orderBy('tahun_mulai', 'desc')->first();
                $unitKerja = $latestJob ? $latestJob->unit_kerja : null;

                $canInterview = false;
                $canPG = false;

                if ($unitKerja) {
                    $hasInterview = $user
                        ->perawatLisensis()
                        ->where('unit_kerja_saat_buat', $unitKerja)
                        ->where('metode_perpanjangan', 'interview_only')
                        ->exists();

                    $hasPG = $user
                        ->perawatLisensis()
                        ->where('unit_kerja_saat_buat', $unitKerja)
                        ->where('metode_perpanjangan', 'pg_interview')
                        ->exists();

                    $canInterview = !$hasInterview;
                    $canPG = !$hasPG;
                }
            @endphp

            @if ($canInterview || $canPG)
                <div class="dropdown d-inline-block">
                    <button class="btn btn-create dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-plus-lg"></i> Ajukan Lisensi Baru
                    </button>
                    <ul class="dropdown-menu shadow-sm border-0 rounded-3 mt-2">
                        @if ($canInterview)
                            <li>
                                <a class="dropdown-item py-2 px-3"
                                    href="{{ route('perawat.lisensi.create', 'interview_only') }}">
                                    <i class="bi bi-mic me-2 text-primary"></i> Kredensialing
                                </a>
                            </li>
                        @else
                            <li>
                                <button class="dropdown-item py-2 px-3 text-muted" disabled style="opacity: 0.6;">
                                    <i class="bi bi-check-circle-fill me-2 text-success"></i> Kredensialing
                                </button>
                            </li>
                        @endif

                        @if ($canInterview && $canPG)
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        @endif

                        @if ($canPG)
                            <li>
                                <a class="dropdown-item py-2 px-3"
                                    href="{{ route('perawat.lisensi.create', 'pg_interview') }}">
                                    <i class="bi bi-file-earmark-text me-2 text-success"></i> Uji Kompetensi 
                                </a>
                            </li>
                        @else
                            <li>
                                <button class="dropdown-item py-2 px-3 text-muted" disabled style="opacity: 0.6;">
                                    <i class="bi bi-check-circle-fill me-2 text-success"></i> Uji Kompetensi
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center p-3 mb-0"
                    style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; color: #1e40af;">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Info Pengajuan</strong><br>
                        Anda sudah melengkapi kedua jenis lisensi untuk unit: <strong>{{ $unitKerja ?? '-' }}</strong>.
                    </div>
                </div>
            @endif
        </div>

        {{-- TABEL DATA --}}
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Detail Lisensi</th>
                            <th width="15%">Metode</th>
                            <th width="20%">Nomor & Masa Berlaku</th>
                            <th width="20%">Status & Aksi</th>
                            <th width="12%" class="text-end">Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td class="text-muted text-center">{{ $loop->iteration }}</td>

                                {{-- Nama & Lembaga --}}
                                <td>
                                    <span class="license-name">{{ $item->nama }}</span>
                                    <div class="license-sub">
                                        <i class="bi bi-building"></i> {{ $item->lembaga }}
                                    </div>
                                    <div class="license-sub text-primary">
                                        <i class="bi bi-geo-alt"></i> Unit: {{ $item->unit_kerja_saat_buat }}
                                    </div>
                                </td>

                                {{-- Metode Badge --}}
                                <td>
                                    @if ($item->metode_perpanjangan == 'interview_only')
                                        <span class="badge-metode bg-metode-interview">Wawancara</span>
                                    @else
                                        <span class="badge-metode bg-metode-pg">Ujian & Wawancara</span>
                                    @endif
                                </td>

                                {{-- Nomor & Tanggal --}}
                                <td>
                                    <div class="mb-2">
                                        <span class="license-number">{{ $item->nomor }}</span>
                                    </div>
                                    <div class="d-flex flex-column" style="font-size: 0.8rem; color: #64748b;">
                                        <span>Terbit:
                                            {{ \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') }}</span>
                                        <span
                                            class="fw-bold {{ \Carbon\Carbon::parse($item->tgl_expired)->isPast() ? 'text-danger' : 'text-dark' }}">
                                            Habis: {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d M Y') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Status & Tombol Perpanjang (INI YANG DIPERBAIKI) --}}
                                <td>
                                    @php
                                        $expired = \Carbon\Carbon::parse($item->tgl_expired);
                                        $today = \Carbon\Carbon::now();
                                        $diff = $today->diffInDays($expired, false);
                                    @endphp

                                    @if ($diff < 0)
                                        {{-- EXPIRED --}}
                                        <span class="badge-status bs-danger mb-1"><i class="bi bi-x-circle"></i>
                                            Expired</span>

                                        <form action="{{ route('perawat.pengajuan.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="lisensi_id" value="{{ $item->id }}">
                                            <button type="submit" class="btn-renew danger-renew">
                                                <i class="bi bi-arrow-repeat"></i> Perpanjang
                                            </button>
                                        </form>
                                    @elseif($diff < 90)
                                        {{-- HAMPIR EXPIRED --}}
                                        <span class="badge-status bs-warning mb-1"><i
                                                class="bi bi-exclamation-triangle"></i> Hampir Expired</span>

                                        <form action="{{ route('perawat.pengajuan.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="lisensi_id" value="{{ $item->id }}">
                                            <button type="submit" class="btn-renew">
                                                <i class="bi bi-arrow-repeat"></i> Perpanjang
                                            </button>
                                        </form>
                                    @else
                                        {{-- AKTIF --}}
                                        <span class="badge-status bs-active"><i class="bi bi-check-circle"></i> Aktif</span>
                                    @endif
                                </td>

                                {{-- Generate PDF --}}
                                <td class="text-end">
                                    <a href="{{ route('perawat.lisensi.generate', $item->id) }}" class="btn-generate"
                                        title="Unduh PDF">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center opacity-50">
                                        <i class="bi bi-clipboard-x display-4 text-muted mb-2"></i>
                                        <h6 class="fw-bold text-muted">Belum Ada Data Lisensi</h6>
                                        <p class="small text-muted mb-0">Klik tombol "Ajukan Lisensi Baru" untuk memulai.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
