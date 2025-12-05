@extends('layouts.app')

@section('title', 'Riwayat Organisasi – DIKSERA')

@push('styles')
<style>
    /* Style disamakan dengan inputan Anda */
    .content-card { background: #ffffff; border-radius: 16px; border: 1px solid var(--border-soft); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02); padding: 24px; }
    .table-custom th { background-color: var(--blue-soft-2); color: var(--text-main); font-weight: 600; font-size: 12px; border-bottom: 2px solid #dbeafe; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 8px; vertical-align: middle; }
    .table-custom td { vertical-align: middle; padding: 10px 8px; border-bottom: 1px solid var(--blue-soft-2); font-size: 13px; }
    .btn-action { border-radius: 8px; font-size: 11px; padding: 6px 10px; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="container py-3">
    {{-- Header & Tombol --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="mb-0 fw-bold">Riwayat Organisasi</h6>
            <small class="text-muted">Kelola data pengalaman organisasi profesi atau kemasyarakatan.</small>
        </div>
        <div>
            <a href="{{ route('perawat.drh') }}" class="btn btn-sm btn-outline-secondary me-1" style="border-radius: 8px;">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('perawat.organisasi.create') }}" class="btn btn-sm btn-primary" style="border-radius: 8px;">
                + Tambah Organisasi
            </a>
        </div>
    </div>

    <div class="content-card">
        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small rounded-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- TABEL LIST --}}
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:5%;">No</th>
                        <th style="width:25%;">Organisasi & Jabatan</th>
                        <th style="width:25%;">Tempat & Pimpinan</th>
                        <th style="width:20%;">Periode Aktif</th>
                        <th style="width:15%;">Dokumen</th>
                        <th style="width:10%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organisasi as $i => $row)
                        <tr>
                            <td class="text-center text-muted">{{ $i+1 }}</td>
                            <td>
                                <div class="fw-bold text-primary">{{ $row->nama_organisasi }}</div>
                                <div class="text-muted small">{{ $row->jabatan }}</div>
                            </td>
                            <td>
                                <div>{{ $row->tempat ?? '-' }}</div>
                                <div class="text-muted small">Pimpinan: {{ $row->pemimpin ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-muted">Mulai:</span> {{ $row->tahun_mulai ? date('d-m-Y', strtotime($row->tahun_mulai)) : '-' }}<br>
                                    <span class="text-muted">Selesai:</span> {{ $row->tahun_selesai ? date('d-m-Y', strtotime($row->tahun_selesai)) : 'Sekarang' }}
                                </div>
                            </td>
                            <td>
                                @if($row->dokumen_path)
                                    <a href="{{ asset('storage/'.$row->dokumen_path) }}" target="_blank" class="btn btn-sm btn-light border" style="font-size: 11px;">
                                        <i class="bi bi-file-earmark-pdf text-danger"></i> Lihat SK
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('perawat.organisasi.edit', $row->id) }}" class="btn btn-action btn-outline-warning text-dark" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('perawat.organisasi.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data organisasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-people display-6 opacity-25"></i>
                                </div>
                                <span class="text-muted small">Belum ada data pengalaman organisasi.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
