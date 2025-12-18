@extends('layouts.app')

@section('title', 'Manajemen Lisensi Perawat')

@push('styles')
    {{-- CSS SweetAlert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* --- Card Container --- */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft, #e2e8f0);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }

        /* --- Search Bar --- */
        .search-input {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            padding-left: 12px;
            height: 40px;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* --- Custom Table --- */
        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px 16px;
            vertical-align: middle;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
        }

        /* --- Avatar Inisial --- */
        .avatar-initial {
            width: 38px;
            height: 38px;
            background: #eff6ff;
            color: #3b82f6;
            font-weight: 700;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            border: 1px solid #dbeafe;
        }

        /* --- Badges --- */
        .badge-soft {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #e0f2fe;
            color: #075985;
        }

        /* --- Action Buttons --- */
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
        }

        .btn-icon-primary {
            color: #3b82f6;
            background: #eff6ff;
        }

        .btn-icon-primary:hover {
            background: #3b82f6;
            color: #fff;
        }

        .btn-icon-danger {
            color: #ef4444;
            background: #fef2f2;
        }

        .btn-icon-danger:hover {
            background: #ef4444;
            color: #fff;
        }

        .btn-icon-dark {
            color: #475569;
            background: #f1f5f9;
        }

        .btn-icon-dark:hover {
            background: #1e293b;
            color: #fff;
        }
    </style>
@endpush

@section('content')

    {{-- Header & Tools --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Data Lisensi Perawat</h4>
            <p class="text-muted small mb-0">Monitor masa berlaku dan dokumen lisensi seluruh perawat.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm px-3" style="border-radius: 8px;">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.lisensi.create') }}" class="btn btn-primary px-3 shadow-sm"
                style="border-radius: 8px;">
                <i class="bi bi-plus-lg me-1"></i> Tambah Lisensi
            </a>
        </div>
    </div>

    <div class="content-card">

        {{-- Toolbar: Search --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <form action="" method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input"
                    placeholder="Cari nama perawat atau nomor lisensi..." style="width: 300px;">
                <button class="btn btn-light border" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            {{-- Filter Status (Opsional, UI Only) --}}
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="border-radius: 8px; width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Pemilik Lisensi</th>
                        <th>Detail Lisensi</th>
                        <th>Metode Perpanjangan</th>
                        <th>Masa Berlaku</th>
                        <th class="text-center">File</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration }}</td>

                            {{-- Kolom 1: Identitas User --}}
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    {{-- Avatar Inisial --}}
                                    @php
                                        $name = $item->user->name ?? 'Unknown';
                                        $initials = collect(explode(' ', $name))
                                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                            ->take(2)
                                            ->join('');
                                    @endphp
                                    <div class="avatar-initial">{{ $initials }}</div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $name }}</div>
                                        <div class="text-muted small" style="font-size: 11px;">
                                            {{ $item->user->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kolom 2: Info Lisensi --}}
                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $item->nama }}</div>
                                <div class="text-muted small d-flex flex-column">
                                    <span><i class="bi bi-building me-1"></i> {{ $item->lembaga }}</span>
                                    <span class="text-primary mt-1" style="font-size: 11px;">No: {{ $item->nomor }}</span>
                                </div>
                            </td>

                            {{-- Kolom 3: Metode --}}
                            <td>
                                @if ($item->metode_perpanjangan == 'pg_only')
                                    <span class="badge-soft badge-info">
                                        <i class="bi bi-check-square"></i> Hanya PG
                                    </span>
                                @else
                                    <span class="badge-soft badge-info">
                                        <i class="bi bi-mic"></i> PG + Wawancara
                                    </span>
                                @endif
                            </td>

                            {{-- Kolom 4: Status & Tanggal --}}
                            <td>
                                @php
                                    $expired = \Carbon\Carbon::parse($item->tgl_expired);
                                    $today = \Carbon\Carbon::now();
                                    $diff = $today->diffInDays($expired, false);
                                @endphp

                                <div class="d-flex flex-column gap-1">
                                    @if ($diff < 0)
                                        <span class="badge-soft badge-danger w-fit"><i class="bi bi-x-circle"></i>
                                            Expired</span>
                                    @elseif($diff < 90)
                                        <span class="badge-soft badge-warning w-fit"><i
                                                class="bi bi-exclamation-triangle"></i> Segera Habis</span>
                                    @else
                                        <span class="badge-soft badge-active w-fit"><i class="bi bi-check-circle"></i>
                                            Aktif</span>
                                    @endif

                                    <div class="text-muted small mt-1" style="font-size: 11px;">
                                        Exp: {{ $expired->format('d M Y') }}
                                    </div>
                                </div>
                            </td>

                            {{-- Kolom 5: File --}}
                            <td class="text-center">
                                @if ($item->file_path)
                                    <a href="{{ Storage::url($item->file_path) }}" target="_blank"
                                        class="btn-icon btn-icon-dark" data-bs-toggle="tooltip" title="Lihat Dokumen">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- Kolom 6: Aksi --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.lisensi.edit', $item->id) }}"
                                        class="btn-icon btn-icon-primary" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('admin.lisensi.destroy', $item->id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-danger" data-bs-toggle="tooltip"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-card-checklist display-6 opacity-25"></i>
                                </div>
                                <span class="text-muted small">Belum ada data lisensi ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $data->withQueryString()->links('vendor.pagination.diksera') }}
        </div>
    </div>

@endsection

@push('scripts')
    {{-- SweetAlert JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Init Tooltips Bootstrap 5
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Handle Flash Message
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // Handle Delete
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus Lisensi?',
                        text: "Data lisensi ini akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
