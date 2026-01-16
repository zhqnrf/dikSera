@extends('layouts.app')

@section('title', 'Edit Pendidikan – DIKSERA')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-blue: #2563eb;
        --text-dark: #0f172a;
        --text-gray: #64748b;
        --bg-light: #f8fafc;
        --input-border: #e2e8f0;
        --accent-orange: #f59e0b; /* Warna tema Edit */
    }

    body {
        background-color: var(--bg-light);
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
    }

    /* --- Header --- */
    .page-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        letter-spacing: -0.5px;
    }

    /* --- Form Card --- */
    .form-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        padding: 40px;
    }

    /* --- Inputs --- */
    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
        display: block;
    }

    .required-star {
        color: #ef4444;
    }

    /* .form-select agar style dropdown sama dengan input */
    .form-control, .form-select {
        border: 1px solid var(--input-border);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.95rem;
        color: var(--text-dark);
        background-color: #fff;
        transition: all 0.2s ease;
        width: 100%;
    }

    /* Focus Orange untuk Edit */
    .form-control:focus, .form-select:focus {
        border-color: var(--accent-orange);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        outline: none;
    }

    /* --- Buttons --- */
    .btn-submit-edit {
        background-color: var(--accent-orange);
        color: white;
        width: 100%;
        padding: 14px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.25);
        transition: all 0.2s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .btn-submit-edit:hover {
        background-color: #d97706;
        transform: translateY(-2px);
        box-shadow: 0 8px 12px -1px rgba(245, 158, 11, 0.3);
        color: white;
    }

    .btn-back {
        background: white;
        border: 1px solid #e2e8f0;
        color: var(--text-gray);
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background-color: #f1f5f9;
        color: var(--text-dark);
        border-color: #cbd5e1;
    }

    /* --- File Preview Style --- */
    .file-preview-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }
    .file-link {
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 600;
    }
    .file-link:hover { text-decoration: underline; }

    /* Alert Style */
    .alert-danger {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        border-radius: 10px;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Header --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit Data Pendidikan</h1>
                </div>
                <a href="{{ route('perawat.pendidikan.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            {{-- Form Card --}}
            <div class="form-card">

                @if($errors->any())
                    <div class="alert alert-danger py-3 px-4 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perawat.pendidikan.update', $pendidikan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        {{-- Jenjang & Institusi --}}
                        <div class="col-md-4">
                            <label class="form-label">Jenjang <span class="required-star">*</span></label>

                            {{-- Dropdown Jenjang --}}
                            <select name="jenjang" class="form-select" required>
                                <option value="">- Pilih Jenjang -</option>
                                {{-- Ambil value lama dari old input atau dari database --}}
                                @php $j = old('jenjang', $pendidikan->jenjang); @endphp

                                <option value="SD" {{ $j == 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ $j == 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ $j == 'SMA' ? 'selected' : '' }}>SMA</option>
                                <option value="SMK" {{ $j == 'SMK' ? 'selected' : '' }}>SMK</option>
                                <option value="D3" {{ $j == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="D4" {{ $j == 'D4' ? 'selected' : '' }}>D4</option>
                                <option value="S1" {{ $j == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2" {{ $j == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ $j == 'S3' ? 'selected' : '' }}>S3</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Nama Institusi <span class="required-star">*</span></label>
                            <input type="text" name="nama_institusi" class="form-control" value="{{ old('nama_institusi', $pendidikan->nama_institusi) }}" required>
                        </div>

                        {{-- Jurusan & Tempat --}}
                        <div class="col-md-6">
                            <label class="form-label">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $pendidikan->jurusan) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat / Kota</label>
                            <input type="text" name="tempat" class="form-control" value="{{ old('tempat', $pendidikan->tempat) }}">
                        </div>

                        {{-- Akreditasi, Tahun & No Ijazah --}}
                        {{-- Ubah menjadi col-md-3 agar muat 4 kolom --}}

                        <div class="col-md-3">
                            <label class="form-label">Akreditasi</label>

                            {{-- Dropdown Akreditasi --}}
                            <select name="akreditasi" class="form-select">
                                <option value="">- Pilih -</option>
                                @php $a = old('akreditasi', $pendidikan->akreditasi); @endphp

                                <option value="Unggul" {{ $a == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                                <option value="A" {{ $a == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ $a == 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ $a == 'C' ? 'selected' : '' }}>C</option>
                                <option value="Baik Sekali" {{ $a == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                                <option value="Baik" {{ $a == 'Baik' ? 'selected' : '' }}>Baik</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Thn Masuk</label>
                            <input type="number" name="tahun_masuk" class="form-control" value="{{ old('tahun_masuk', $pendidikan->tahun_masuk) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Thn Lulus</label>
                            <input type="number" name="tahun_lulus" class="form-control" value="{{ old('tahun_lulus', $pendidikan->tahun_lulus) }}">
                        </div>

                        {{-- NEW: Field Nomor Ijazah --}}
                        <div class="col-md-3">
                            <label class="form-label">Nomor Ijazah</label>
                            <input type="text" name="nomor_ijazah" class="form-control" value="{{ old('nomor_ijazah', $pendidikan->nomor_ijazah) }}" placeholder="No. Seri Ijazah">
                        </div>

                        {{-- Upload Dokumen --}}
                        <div class="col-12">
                            <label class="form-label">Update Ijazah (Opsional)</label>
                            <input type="file" name="dokumen" class="form-control">

                            @if($pendidikan->dokumen_path)
                                <div class="file-preview-box">
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    <span class="text-muted">File saat ini:</span>
                                    <a href="{{ asset('storage/'.$pendidikan->dokumen_path) }}" target="_blank" class="file-link">
                                        Lihat Ijazah
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-submit-edit">
                            <i class="bi bi-check-lg"></i> Update Data Pendidikan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
