@extends('layouts.app')

@section('title', 'Riwayat Pelatihan – DIKSERA')

@section('content')
<div class="container py-3">
    <div class="dash-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="mb-0 fw-bold">Riwayat Pelatihan</h6>
                <small class="text-muted">Kelola data kursus, seminar, dan pelatihan non-formal.</small>
            </div>
            <div>
                <a href="{{ route('perawat.drh') }}" class="btn btn-sm btn-outline-secondary me-1">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('perawat.pelatihan.create') }}" class="btn btn-sm btn-primary">
                    + Tambah Pelatihan
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small rounded-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- TABEL LIST --}}
        <div class="table-responsive small">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%;" class="text-center">No</th>
                        <th style="width:30%;">Nama Pelatihan & Penyelenggara</th>
                        <th style="width:15%;">Tempat</th>
                        <th style="width:10%;">Durasi</th>
                        <th style="width:20%;">Waktu Pelaksanaan</th>
                        <th style="width:10%;">Dokumen</th>
                        <th style="width:10%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelatihan as $i => $row)
                        <tr>
                            <td class="text-center">{{ $i+1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama_pelatihan }}</div>
                                <div class="text-muted small">{{ $row->penyelenggara }}</div>
                            </td>
                            <td>{{ $row->tempat ?? '-' }}</td>
                            <td>{{ $row->durasi ?? '-' }}</td>
                            <td>
                                <div class="small">
                                    <span class="text-muted">Mulai:</span> {{ $row->tanggal_mulai ? date('d-m-Y', strtotime($row->tanggal_mulai)) : '-' }}<br>
                                    <span class="text-muted">Selesai:</span> {{ $row->tanggal_selesai ? date('d-m-Y', strtotime($row->tanggal_selesai)) : '-' }}
                                </div>
                            </td>
                            <td class="text-center">
                                @if($row->dokumen_path)
                                    <a href="{{ asset('storage/'.$row->dokumen_path) }}" target="_blank" class="btn btn-sm btn-light border" title="Lihat Sertifikat">
                                        <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('perawat.pelatihan.edit', $row->id) }}" class="btn btn-sm btn-warning text-dark">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('perawat.pelatihan.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data pelatihan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <div class="mb-2"><i class="bi bi-journal-bookmark display-6 opacity-25"></i></div>
                                Belum ada data pelatihan yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
