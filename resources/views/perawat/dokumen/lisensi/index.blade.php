@extends('layouts.app')

@section('title', 'Data Lisensi Saya')

@push('styles')
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 14px;
        }

        /* --- Page Header --- */
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

        /* --- Action Area (Create Logic) --- */
        .action-area {
            margin-bottom: 20px;
        }

        .alert-info-custom {
            background-color: #eff6ff;
            border: 1px solid #dbeafe;
            color: #1e40af;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        /* --- Table Styling --- */
        .card-table {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.05);
        }

        .table-custom th {
            background-color: #f8fafc;
            color: var(--text-gray);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-custom td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-dark);
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-custom tr:hover td {
            background-color: #fcfcfc;
        }

        /* --- Data Styling --- */
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
            gap: 4px;
        }

        .license-number {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #334155;
        }

        /* --- Badges --- */
        .badge-status {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .bs-active {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .bs-warning {
            background: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde047;
        }

        .bs-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* --- Buttons --- */
        .btn-create {
            background-color: var(--primary-blue);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            border: none;
        }

        .btn-create:hover {
            background-color: var(--primary-hover);
            color: white;
            transform: translateY(-1px);
        }

        .btn-renew {
            margin-top: 8px;
            background-color: white;
            border: 1px solid #cbd5e1;
            color: var(--text-dark);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: 0.2s;
        }

        .btn-renew:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
            background: #eff6ff;
        }

        .btn-generate {
            color: var(--primary-blue);
            background: #eff6ff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
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

        {{-- LOGIC TOMBOL CREATE --}}
        <div class="action-area">
            @php
                $latestJob = $user->perawatPekerjaans()->orderBy('tahun_mulai', 'desc')->first();
                $canCreate = false;
                if ($latestJob) {
                    $canCreate = !$user
                        ->perawatLisensis()
                        ->where('unit_kerja_saat_buat', $latestJob->unit_kerja)
                        ->exists();
                }
            @endphp

            @if ($canCreate)
                <a href="{{ route('perawat.lisensi.create') }}" class="btn-create shadow-sm">
                    <i class="bi bi-plus-lg"></i> Buat Lisensi Baru
                </a>
            @else
                <div class="alert-info-custom">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div>
                        <strong>Info:</strong> Anda sudah membuat lisensi untuk unit kerja saat ini
                        (<strong>{{ $latestJob->unit_kerja ?? '-' }}</strong>).
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
                            <th width="30%">Detail Lisensi</th>
                            <th width="20%">Nomor Registrasi</th>
                            <th width="20%">Masa Berlaku</th>
                            <th width="15%">Status & Aksi</th>
                            <th width="10%" class="text-end">Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>

                                {{-- Nama & Lembaga --}}
                                <td>
                                    <span class="license-name">{{ $item->nama }}</span>
                                    <div class="license-sub mt-1">
                                        <i class="bi bi-building"></i> {{ $item->lembaga }}
                                    </div>
                                </td>

                                {{-- Nomor (Monospace) --}}
                                <td>
                                    <span class="license-number">{{ $item->nomor }}</span>
                                </td>

                                {{-- Tanggal --}}
                                <td>
                                    <div class="d-flex flex-column" style="font-size: 0.85rem; line-height: 1.4;">
                                        <span class="text-muted">Terbit: <span
                                                class="text-dark fw-medium">{{ \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') }}</span></span>
                                        <span class="text-muted">Habis: <span
                                                class="text-dark fw-medium">{{ \Carbon\Carbon::parse($item->tgl_expired)->format('d M Y') }}</span></span>
                                    </div>
                                </td>

                                {{-- Status & Logic Perpanjangan --}}
                                <td>
                                    @php
                                        $expired = \Carbon\Carbon::parse($item->tgl_expired);
                                        $today = \Carbon\Carbon::now();
                                        $diff = $today->diffInDays($expired, false);
                                    @endphp

                                    @if ($diff < 0)
                                        {{-- EXPIRED --}}
                                        <span class="badge-status bs-danger"><i class="bi bi-x-circle"></i> Expired</span>

                                        <form action="{{ route('perawat.pengajuan.store') }}" method="POST"
                                            class="d-block">
                                            @csrf
                                            <input type="hidden" name="lisensi_id" value="{{ $item->id }}">
                                            <button type="submit" class="btn-renew text-danger border-danger">
                                                <i class="bi bi-arrow-repeat"></i> Perpanjang
                                            </button>
                                        </form>
                                    @elseif($diff < 90)
                                        {{-- HAMPIR EXPIRED --}}
                                        <span class="badge-status bs-warning"><i class="bi bi-exclamation-triangle"></i>
                                            Hampir Expired</span>

                                        <form action="{{ route('perawat.pengajuan.store') }}" method="POST"
                                            class="d-block">
                                            @csrf
                                            <input type="hidden" name="lisensi_id" value="{{ $item->id }}">
                                            <button type="submit" class="btn-renew text-warning border-warning">
                                                <i class="bi bi-arrow-repeat"></i> Perpanjang
                                            </button>
                                        </form>
                                    @else
                                        {{-- AKTIF --}}
                                        <span class="badge-status bs-active"><i class="bi bi-check-circle"></i> Aktif</span>
                                    @endif
                                </td>

                                {{-- Tombol Generate --}}
                                <td class="text-end">
                                    <a href="{{ route('perawat.lisensi.generate', $item->id) }}" class="btn-generate"
                                        title="Generate PDF Terbaru">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center opacity-50">
                                        <i class="bi bi-folder-x display-4 text-muted mb-2"></i>
                                        <h6 class="fw-bold text-muted">Belum Ada Data Lisensi</h6>
                                        <p class="small text-muted mb-0">Silakan buat lisensi baru jika memenuhi syarat.</p>
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
