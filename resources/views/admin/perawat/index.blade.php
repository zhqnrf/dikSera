@extends('layouts.app')

@php
    $pageTitle = 'Data Perawat';
    $pageSubtitle = 'Monitor dan kelola seluruh data perawat yang terdaftar dalam sistem.';
@endphp

@section('title', 'Data Perawat – Admin DIKSERA')

@push('styles')
    <style>
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }

        /* Styling Tabel Konsisten */
        .table-custom th {
            background-color: var(--blue-soft-2);
            color: var(--text-main);
            font-weight: 600;
            font-size: 12px;
            border-bottom: 2px solid #dbeafe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            vertical-align: middle;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 12px 16px;
            border-bottom: 1px solid var(--blue-soft-2);
            font-size: 13px;
        }

        /* Tombol Aksi Container */
        .action-group {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        /* Tombol Khusus Icon */
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Avatar Styling */
        .avatar-circle {
            width: 36px;
            height: 36px;
            background-color: var(--blue-soft);
            color: var(--blue-main);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .avatar-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Search Input Style */
        .search-input {
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            font-size: 13px;
            padding-left: 12px;
        }

        .search-input:focus {
            border-color: var(--blue-main);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }
    </style>
@endpush

@section('content')

    <div class="content-card">

        {{-- Header Tools: Total & Search Bar --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">

            {{-- Bagian Kiri: Total Data --}}
            <div>
                <h6 class="m-0 text-muted" style="font-size: 13px;">
                    Total Data: <strong class="text-dark">{{ $perawat->total() }}</strong> Perawat
                </h6>
            </div>

            {{-- Bagian Kanan: Search Form --}}
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm search-input"
                    placeholder="Cari nama, NIK, HP..." value="{{ request('search') }}" style="width: 240px;">
                <button type="submit" class="btn btn-sm btn-primary px-3" style="border-radius: 8px;">
                    <i class="bi bi-search"></i>
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.perawat.index') }}" class="btn btn-sm btn-outline-secondary px-3"
                        style="border-radius: 8px;" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;" class="text-center">No</th>
                        <th>Identitas Perawat</th>
                        <th>Kontak & NIK</th>
                        <th>Alamat Domisili</th>
                        <th style="width:160px;" class="text-center">Aksi</th> {{-- Lebar ditambah dikit biar muat 4 tombol --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($perawat as $i => $p)
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration + $perawat->firstItem() - 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    {{-- Avatar --}}
                                    <div class="avatar-wrapper">
                                        @if (!empty($p->profile->foto_3x4))
                                            {{-- Cek relasi profile --}}
                                            <img src="{{ asset('storage/' . $p->profile->foto_3x4) }}" class="avatar-img"
                                                alt="{{ $p->name }}">
                                        @else
                                            <div class="avatar-circle">{{ strtoupper(substr($p->name, 0, 1)) }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $p->name }}</div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            Joined: {{ $p->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-2 text-dark">
                                        <i class="bi bi-person-vcard text-muted" style="font-size: 14px;"></i>
                                        <span style="font-family: monospace;">{{ $p->profile->nik ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 12px;">
                                        <i class="bi bi-whatsapp text-success" style="font-size: 12px;"></i>
                                        <span>{{ $p->profile->no_hp ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 250px;"
                                    title="{{ $p->profile->alamat ?? '-' }}">
                                    {{ $p->profile->alamat ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="action-group">
                                    {{-- 1. Detail --}}
                                    <a href="{{ route('admin.perawat.show', $p->id) }}"
                                        class="btn btn-icon btn-outline-primary" title="Detail DRH"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- 2. Sertifikat (Menggunakan Icon agar muat) --}}
                                    <a href="{{ route('admin.perawat.sertifikat', $p->id) }}"
                                        class="btn btn-icon btn-outline-success" title="Lihat Sertifikat & Dokumen"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-patch-check"></i>
                                    </a>

                                    {{-- 3. Edit --}}
                                    <a href="{{ route('admin.perawat.edit', $p->id) }}"
                                        class="btn btn-icon btn-outline-warning" title="Edit Data" data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- 4. Hapus --}}
                                    <form action="{{ route('admin.perawat.destroy', $p->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data perawat ini beserta seluruh riwayatnya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-outline-danger" title="Hapus"
                                            data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-search display-6 opacity-25"></i>
                                </div>
                                <span class="text-muted small">Data tidak ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $perawat->links('vendor.pagination.diksera') }}
            </div>
        </div>
    </div>
@endsection
