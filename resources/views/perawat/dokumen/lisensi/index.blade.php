@extends('layouts.app')

@section('title', 'Data Lisensi Saya')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        /* --- Header Area --- */
        .page-header {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: var(--text-gray);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* --- Buttons --- */
        .btn-white {
            background: white;
            color: var(--text-gray);
            border: 1px solid #e2e8f0;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-white:hover {
            background-color: #f8fafc;
            color: var(--text-dark);
            border-color: #cbd5e1;
        }

        /* --- Table Card --- */
        .table-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .custom-table tbody tr {
            transition: background-color 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-table tbody tr:last-child {
            border-bottom: none;
        }

        .custom-table tbody tr:hover {
            background-color: #eff6ff;
        }

        .custom-table td {
            padding: 20px 24px;
            vertical-align: middle;
            font-size: 0.95rem;
            color: var(--text-gray);
        }

        /* --- Typography & Elements --- */
        .data-title {
            font-weight: 600;
            color: var(--text-dark);
            display: block;
            margin-bottom: 4px;
            font-size: 1rem;
        }

        .data-sub {
            font-size: 0.85rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* --- Custom Status Badges --- */
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-warning {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde047;
        }

        .status-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Action Buttons (For View File Only) */
        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            transition: 0.2s;
            background: transparent;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            background: #dbeafe;
            color: var(--primary-blue);
        }

        /* Alert */
        .alert-blue {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        {{-- Header --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Data Lisensi Saya</h1>
                <p class="page-subtitle">Daftar lisensi (STR/SIP) yang tercatat dalam sistem.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard') }}" class="btn-white"><i class="bi bi-arrow-left"></i> Kembali ke
                    Dashboard</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-blue"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif

        <div class="table-card">
            <div class="table-responsive">
                {{-- resources/views/perawat/dokumen/lisensi/index.blade.php --}}
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
                    <a href="{{ route('perawat.lisensi.create') }}" class="btn btn-primary">
                        Buat Lisensi
                    </a>
                @else
                    <button class="btn btn-secondary" disabled>
                        Sudah Membuat Lisensi untuk Unit Ini
                    </button>
                @endif
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Lisensi</th>
                            <th width="20%">Nomor</th>
                            <th width="25%">Masa Berlaku</th>
                            <th width="15%">Status</th>
                            <th width="5%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- Nama & Lembaga --}}
                                <td>
                                    <span class="data-title">{{ $item->nama }}</span>
                                    <span class="text-muted" style="font-size: 0.8rem;">
                                        <i class="bi bi-building"></i> {{ $item->lembaga }}
                                    </span>
                                </td>

                                {{-- Nomor --}}
                                <td><span class="data-title">{{ $item->nomor }}</span></td>

                                {{-- Tanggal --}}
                                <td>
                                    <span class="data-sub">
                                        {{ \Carbon\Carbon::parse($item->tgl_terbit)->format('d-m-Y') }} s/d
                                        {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d-m-Y') }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @php
                                        $expired = \Carbon\Carbon::parse($item->tgl_expired);
                                        $terbit = \Carbon\Carbon::parse($item->tgl_terbit);
                                        $today = \Carbon\Carbon::now();
                                        $diff = $today->diffInDays($expired, false);

                                        // LOGIKA TOMBOL UPDATE PDF
                                        // Default tombol update: FALSE
                                        $showUpdateBtn = false;

                                        // Cek jika file ada
                                        if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                                            // Ambil waktu terakhir file diubah
                                            $fileTimestamp = Storage::disk('public')->lastModified($item->file_path);
                                            $fileDate = \Carbon\Carbon::createFromTimestamp($fileTimestamp);

                                            // JIKA Tanggal Terbit Lisensi (DB) LEBIH BARU dari Tanggal File
                                            // Berarti lisensi sudah diperpanjang, tapi file PDF masih versi lama
                                            if ($terbit->startOfDay()->gt($fileDate)) {
                                                $showUpdateBtn = true;
                                            }
                                        }
                                    @endphp

                                    @if ($diff < 0)
                                        {{-- KONDISI 1: EXPIRED --}}
                                        <span class="status-badge status-danger">Expired</span>

                                        {{-- Form Perpanjang Muncul --}}
                                        <div class="mt-2">
                                            <form action="{{ route('perawat.pengajuan.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="lisensi_id" value="{{ $item->id }}">
                                                <button type="submit" class="btn btn-sm btn-primary"
                                                    style="font-size: 0.75rem;">
                                                    <i class="bi bi-arrow-repeat"></i> Perpanjang
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($diff < 90)
                                        {{-- KONDISI 2: HAMPIR EXPIRED --}}
                                        <span class="status-badge status-warning">Hampir Expired</span>

                                        {{-- Form Perpanjang Muncul --}}
                                        <div class="mt-2">
                                            <form action="{{ route('perawat.pengajuan.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="lisensi_id" value="{{ $item->id }}">
                                                <button type="submit" class="btn btn-sm btn-primary"
                                                    style="font-size: 0.75rem;">
                                                    <i class="bi bi-arrow-repeat"></i> Perpanjang
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        {{-- KONDISI 3: AKTIF --}}
                                        <span class="status-badge status-active">Aktif</span>
                                    @endif
                                </td>

                                {{-- KOLOM DOKUMEN / AKSI --}}
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-2 align-items-center">


                                        {{-- Tombol Generate/Update PDF: Selalu tampil agar isi dokumen selalu up to date --}}
                                        <a href="{{ route('perawat.lisensi.generate', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary fw-bold" title="Generate/Update PDF">
                                            <i class="bi bi-printer"></i> Generate
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted" style="opacity: 0.6;">
                                        <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i> Belum ada data lisensi.
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
