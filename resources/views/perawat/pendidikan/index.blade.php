@extends('layouts.app')

@php
    $pageTitle = 'Riwayat Pendidikan';
    $pageSubtitle = 'Kelola data pendidikan formal dan akademik Anda.';
@endphp

@section('title', 'Pendidikan – DIKSERA')

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

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('perawat.drh') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px; font-size: 12px;">
            <i class="bi bi-arrow-left"></i> Kembali ke DRH
        </a>
    </div>

    <div class="content-card">
        
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
                    + Tambah Data Pendidikan
                </h6>
            </div>
            
            <form action="{{ route('perawat.pendidikan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Baris 1: Info Institusi --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-2">
                        <label class="form-label">Jenjang <span class="text-danger">*</span></label>
                        <input type="text" name="jenjang" class="form-control form-control-custom" placeholder="D3/S1/Ners">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Institusi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_institusi" class="form-control form-control-custom" placeholder="Nama Universitas/STIKES">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control form-control-custom" placeholder="Ilmu Keperawatan">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tempat</label>
                        <input type="text" name="tempat" class="form-control form-control-custom" placeholder="Kota">
                    </div>
                </div>

                {{-- Baris 2: Detail Akademik --}}
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Akreditasi</label>
                        <input type="text" name="akreditasi" class="form-control form-control-custom" placeholder="A/B/Unggul">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Thn Masuk</label>
                        <input type="text" name="tahun_masuk" class="form-control form-control-custom" placeholder="20XX">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Thn Lulus</label>
                        <input type="text" name="tahun_lulus" class="form-control form-control-custom" placeholder="20XX">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ijazah (PDF)</label>
                        <input type="file" name="dokumen" class="form-control form-control-custom" style="padding: 5px 8px;">
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4 w-100" style="border-radius: 8px; background: var(--blue-main); border: none;">
                            Simpan Data
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
                        <th style="width:25%;">Jenjang & Institusi</th>
                        <th>Akreditasi</th>
                        <th>Tempat</th>
                        <th>Jurusan</th>
                        <th>Masuk</th>
                        <th>Lulus</th>
                        <th>Dokumen</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendidikan as $i => $row)
                        <tr>
                            <td class="text-center text-muted">{{ $i+1 }}</td>
                            
                            {{-- Form Update Inline (Colspan harus 8: Total kolom 9 dikurangi kolom No) --}}
                            <td colspan="8" class="p-0">
                                <form action="{{ route('perawat.pendidikan.update',$row->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    {{-- Table Trick untuk Alignment --}}
                                    <table class="w-100 m-0 bg-transparent">
                                        <tr>
                                            <td style="border:none; width: 25%;">
                                                <input type="text" name="jenjang" value="{{ $row->jenjang }}" class="form-control form-control-custom mb-1 fw-bold" placeholder="Jenjang">
                                                <input type="text" name="nama_institusi" value="{{ $row->nama_institusi }}" class="form-control form-control-custom text-muted" style="font-size: 11px;" placeholder="Institusi">
                                            </td>
                                            <td style="border:none;">
                                                <input type="text" name="akreditasi" value="{{ $row->akreditasi }}" class="form-control form-control-custom text-center">
                                            </td>
                                            <td style="border:none;">
                                                <input type="text" name="tempat" value="{{ $row->tempat }}" class="form-control form-control-custom">
                                            </td>
                                            <td style="border:none;">
                                                <input type="text" name="jurusan" value="{{ $row->jurusan }}" class="form-control form-control-custom">
                                            </td>
                                            <td style="border:none; width: 70px;">
                                                <input type="text" name="tahun_masuk" value="{{ $row->tahun_masuk }}" class="form-control form-control-custom text-center">
                                            </td>
                                            <td style="border:none; width: 70px;">
                                                <input type="text" name="tahun_lulus" value="{{ $row->tahun_lulus }}" class="form-control form-control-custom text-center">
                                            </td>
                                            <td style="border:none;">
                                                <div class="d-flex flex-column gap-1" style="font-size: 11px;">
                                                    <input type="file" name="dokumen" class="form-control form-control-custom" style="padding: 4px; font-size: 10px;">
                                                    @if($row->dokumen_path)
                                                        <a href="{{ asset('storage/'.$row->dokumen_path) }}" target="_blank" class="text-decoration-none text-primary">
                                                            <i class="bi bi-file-earmark-pdf"></i> File
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="border:none; width: 140px;">
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <button type="submit" class="btn btn-action btn-outline-primary" title="Simpan">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                </form> 
                                                    <form action="{{ route('perawat.pendidikan.destroy',$row->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-action btn-outline-danger" title="Hapus">
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
                            <td colspan="9" class="text-center py-5"> {{-- Colspan 9 sesuai total header --}}
                                <div class="text-muted mb-2">
                                    <i class="bi bi-mortarboard display-6 opacity-25"></i>
                                </div>
                                <span class="text-muted small">Belum ada data riwayat pendidikan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection