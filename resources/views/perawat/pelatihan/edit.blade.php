@extends('layouts.app')

@section('title', 'Edit Pelatihan – DIKSERA')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="dash-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0 fw-bold text-warning">Edit Data Pelatihan</h6>
                    <a href="{{ route('perawat.pelatihan.index') }}" class="btn btn-sm btn-outline-secondary">
                        Kembali
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2 px-3 small mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perawat.pelatihan.update', $pelatihan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pelatihan" class="form-control" value="{{ old('nama_pelatihan', $pelatihan->nama_pelatihan) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Penyelenggara</label>
                            <input type="text" name="penyelenggara" class="form-control" value="{{ old('penyelenggara', $pelatihan->penyelenggara) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tempat</label>
                            <input type="text" name="tempat" class="form-control" value="{{ old('tempat', $pelatihan->tempat) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Durasi</label>
                            <input type="text" name="durasi" class="form-control" value="{{ old('durasi', $pelatihan->durasi) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', $pelatihan->tanggal_mulai) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', $pelatihan->tanggal_selesai) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Upload Sertifikat</label>
                            <input type="file" name="dokumen" class="form-control">
                            @if($pelatihan->dokumen_path)
                                <div class="mt-2 small">
                                    <span class="text-muted">File saat ini:</span>
                                    <a href="{{ asset('storage/'.$pelatihan->dokumen_path) }}" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf"></i> Lihat Sertifikat
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-warning text-dark px-4">
                            Update Pelatihan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
