@extends('layouts.app')

@section('title', 'Dokumen Lisensi – DIKSERA')

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
    .btn-blue {
        background-color: var(--primary-blue);
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid var(--primary-blue);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-blue:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 10px -1px rgba(37, 99, 235, 0.3);
    }

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
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
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

    /* Link Biru */
    .link-blue {
        color: var(--primary-blue);
        font-weight: 600;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.2s;
    }
    .link-blue:hover {
        color: var(--primary-hover);
        text-decoration: underline;
    }

    /* Action Buttons */
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
    .action-btn:hover { background: #dbeafe; color: var(--primary-blue); }
    .action-btn.delete:hover { background: #fee2e2; color: #ef4444; }

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
            <h1 class="page-title">Dokumen Lisensi</h1>
            <p class="page-subtitle">Kelola masa berlaku dokumen legal (STR, SIP, Sertifikat) Anda.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Tombol Kembali (Opsional, jika ingin konsisten dengan code 2) --}}
            {{-- <a href="{{ route('perawat.drh') }}" class="btn-white">
                <i class="bi bi-arrow-left"></i> Kembali
            </a> --}}
            <a href="{{ route('perawat.lisensi.create') }}" class="btn-blue">
                <i class="bi bi-plus-lg"></i> Tambah Dokumen
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('swal'))
        <div class="alert-blue">
            <i class="bi bi-check-circle-fill"></i> {{ session('swal')['text'] ?? 'Berhasil disimpan.' }}
        </div>
    @endif

    {{-- Content Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Jenis & Nomor Dokumen</th>
                        <th width="25%">Masa Berlaku</th>
                        <th width="15%">Status</th>
                        <th width="15%">File</th>
                        <th width="10%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lisensi as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            {{-- Jenis & Nomor (Digabung agar rapi) --}}
                            <td>
                                <span class="data-title">{{ $item->jenis }}</span>
                                <span class="data-sub">
                                    <i class="bi bi-card-heading"></i> {{ $item->nomor }}
                                </span>
                            </td>

                            {{-- Masa Berlaku (Digabung) --}}
                            <td>
                                <div class="text-muted small mb-1">
                                    Terbit: <span class="text-dark fw-medium">{{ \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') }}</span>
                                </div>
                                <div class="text-muted small">
                                    Expired: <span class="text-danger fw-medium">{{ \Carbon\Carbon::parse($item->tgl_expired)->format('d M Y') }}</span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($item->status == 'aktif')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-normal">Aktif</span>
                                @elseif($item->status == 'hampir_expired')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-normal">Hampir Expired</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-normal">Expired</span>
                                @endif
                            </td>

                            {{-- File --}}
                            <td>
                                @if($item->file_path)
                                    <a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" class="link-blue">
                                        <i class="bi bi-file-earmark-pdf"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('perawat.lisensi.edit', $item->id) }}" class="action-btn" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <form action="{{ route('perawat.lisensi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted" style="opacity: 0.6;">
                                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                                    Belum ada dokumen lisensi yang ditambahkan.
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
