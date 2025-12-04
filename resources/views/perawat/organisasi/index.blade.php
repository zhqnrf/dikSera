@extends('layouts.app')

@php
    $pageTitle = 'Riwayat Organisasi';
    $pageSubtitle = 'Kelola data pengalaman organisasi profesi atau kemasyarakatan.';
@endphp

@section('title', 'Organisasi – DIKSERA')

@push('styles')
<style>
    .content-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        padding: 24px;
    }
    .form-control-custom {
        border-radius: 8px;
        border: 1px solid var(--border-soft);
        padding: 8px 12px;
        font-size: 13px;
        transition: all 0.2s;
    }
    .form-control-custom:focus {
        border-color: var(--blue-main);
        box-shadow: 0 0 0 3px var(--blue-soft);
    }
    .form-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 6px;
    }
    .table-custom th {
        background-color: var(--blue-soft-2);
        color: var(--text-main);
        font-weight: 600;
        font-size: 12px;
        border-bottom: 2px solid #dbeafe;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 8px;
        vertical-align: middle;
    }
    .table-custom td {
        vertical-align: middle;
        padding: 10px 8px;
        border-bottom: 1px solid var(--blue-soft-2);
    }
    .btn-action {
        border-radius: 8px;
        font-size: 11px;
        padding: 6px 10px;
        font-weight: 500;
    }
</style>
@endpush

@section('content')

    {{-- Tombol Kembali --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('perawat.drh') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px; font-size: 12px;">
            <i class="bi bi-arrow-left"></i> Kembali ke DRH
        </a>
    </div>

    <div class="content-card">
        
        {{-- Alert Error --}}
        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-4 border-0 bg-danger-subtle text-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM TAMBAH --}}
        <div class="p-3 mb-4 rounded-3" style="background-color: #f8fafc; border: 1px dashed var(--border-soft);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0" style="font-size: 14px; color: var(--blue-main); font-weight: 600;">
                    + Tambah Pengalaman Organisasi
                </h6>
            </div>
            
            <form action="{{ route('perawat.organisasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Baris 1: Info Utama --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Nama Organisasi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_organisasi" class="form-control form-control-custom" placeholder="Contoh: PPNI / BEM">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan" class="form-control form-control-custom" placeholder="Ketua / Anggota / Sekretaris">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tempat</label>
                        <input type="text" name="tempat" class="form-control form-control-custom" placeholder="Kota / Lokasi">
                    </div>
                </div>

                {{-- Baris 2: Detail Tambahan --}}
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Nama Pimpinan</label>
                        <input type="text" name="pemimpin" class="form-control form-control-custom" placeholder="Ketua Umum / Direktur">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mulai</label>
                        <input type="date" name="tahun_mulai" class="form-control form-control-custom">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Selesai</label>
                        <input type="date" name="tahun_selesai" class="form-control form-control-custom">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SK / Dokumen (PDF)</label>
                        <input type="file" name="dokumen" class="form-control form-control-custom" style="padding: 5px 8px;">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm px-4 w-100" style="border-radius: 8px; background: var(--blue-main); border: none;">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL LIST --}}
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th style="width:30%;">Organisasi & Jabatan</th>
                        <th>Tempat & Pimpinan</th>
                        <th>Periode Aktif</th>
                        <th>Dokumen</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organisasi as $i => $row)
                        <tr>
                            <td class="text-center text-muted">{{ $i+1 }}</td>
                            
                            {{-- Nested Form --}}
                            <td colspan="5" class="p-0">
                                <form action="{{ route('perawat.organisasi.update',$row->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <table class="w-100 m-0 bg-transparent">
                                        <tr>
                                            {{-- Kolom 1: Nama Org & Jabatan --}}
                                            <td style="border:none; width: 30%;">
                                                <input type="text" name="nama_organisasi" value="{{ $row->nama_organisasi }}" class="form-control form-control-custom mb-1 fw-bold" placeholder="Nama Organisasi">
                                                <input type="text" name="jabatan" value="{{ $row->jabatan }}" class="form-control form-control-custom text-muted" style="font-size: 11px;" placeholder="Jabatan">
                                            </td>
                                            
                                            {{-- Kolom 2: Tempat & Pimpinan --}}
                                            <td style="border:none;">
                                                <input type="text" name="tempat" value="{{ $row->tempat }}" class="form-control form-control-custom mb-1" style="font-size: 11px;" placeholder="Tempat">
                                                <input type="text" name="pemimpin" value="{{ $row->pemimpin }}" class="form-control form-control-custom text-muted" style="font-size: 11px;" placeholder="Pimpinan">
                                            </td>

                                            {{-- Kolom 3: Periode (Dates) --}}
                                            <td style="border:none; width: 140px;">
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <span class="text-muted" style="font-size:10px; width:15px;">M:</span>
                                                    <input type="date" name="tahun_mulai" value="{{ $row->tahun_mulai }}" class="form-control form-control-custom p-1" style="font-size:11px;">
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="text-muted" style="font-size:10px; width:15px;">S:</span>
                                                    <input type="date" name="tahun_selesai" value="{{ $row->tahun_selesai }}" class="form-control form-control-custom p-1" style="font-size:11px;">
                                                </div>
                                            </td>

                                            {{-- Kolom 4: Dokumen --}}
                                            <td style="border:none;">
                                                <div class="d-flex flex-column gap-1" style="font-size: 11px;">
                                                    <input type="file" name="dokumen" class="form-control form-control-custom" style="padding: 4px; font-size: 10px;">
                                                    @if($row->dokumen_path)
                                                        <a href="{{ asset('storage/'.$row->dokumen_path) }}" target="_blank" class="text-decoration-none text-primary">
                                                            <i class="bi bi-file-earmark-pdf"></i> Lihat SK
                                                        </a>
                                                    @else
                                                        <span class="text-muted text-opacity-50">- Kosong -</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Kolom 5: Aksi --}}
                                            <td style="border:none; width: 140px;">
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <button type="submit" class="btn btn-action btn-outline-primary" title="Simpan Perubahan">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                </form> 
                                                    {{-- Note: Pastikan route destroy sudah ada di web.php --}}
                                                    <form action="{{ route('perawat.organisasi.destroy',$row->id) }}" method="POST" onsubmit="return confirm('Hapus data organisasi ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-action btn-outline-danger" title="Hapus Data">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                
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
@endsection