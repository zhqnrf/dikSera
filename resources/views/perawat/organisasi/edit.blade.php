@extends('layouts.app')

@section('title', 'Edit Organisasi – DIKSERA')

@push('styles')
<style>
    .content-card { background: #ffffff; border-radius: 16px; border: 1px solid var(--border-soft); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02); padding: 24px; }
    .form-control-custom { border-radius: 8px; border: 1px solid var(--border-soft); padding: 8px 12px; font-size: 13px; transition: all 0.2s; }
    .form-control-custom:focus { border-color: var(--blue-main); box-shadow: 0 0 0 3px var(--blue-soft); }
    .form-label { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; }
</style>
@endpush

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0 fw-bold text-warning">Edit Pengalaman Organisasi</h6>
                    <a href="{{ route('perawat.organisasi.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                        Kembali
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2 px-3 small rounded-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perawat.organisasi.update', $organisasi->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Organisasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_organisasi" class="form-control form-control-custom" value="{{ old('nama_organisasi', $organisasi->nama_organisasi) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" class="form-control form-control-custom" value="{{ old('jabatan', $organisasi->jabatan) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tempat / Kota</label>
                            <input type="text" name="tempat" class="form-control form-control-custom" value="{{ old('tempat', $organisasi->tempat) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Pimpinan</label>
                            <input type="text" name="pemimpin" class="form-control form-control-custom" value="{{ old('pemimpin', $organisasi->pemimpin) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tahun_mulai" class="form-control form-control-custom" value="{{ old('tahun_mulai', $organisasi->tahun_mulai) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tahun_selesai" class="form-control form-control-custom" value="{{ old('tahun_selesai', $organisasi->tahun_selesai) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Upload SK / Dokumen (Biarkan kosong jika tidak diubah)</label>
                            <input type="file" name="dokumen" class="form-control form-control-custom">
                            @if($organisasi->dokumen_path)
                                <div class="mt-2 small">
                                    <span class="text-muted">File saat ini:</span>
                                    <a href="{{ asset('storage/'.$organisasi->dokumen_path) }}" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-warning text-white px-4" style="border-radius: 8px;">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
