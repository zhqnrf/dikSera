@extends('layouts.app')

@section('title', 'Buat Pengajuan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        .form-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
            padding: 32px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
            color: #334155;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .btn-submit {
            background: #0ea5e9;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            width: 100%;
            transition: 0.2s;
        }

        .btn-submit:hover {
            background: #0284c7;
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">
                            {{ $metode == 'interview_only' ? 'Pengajuan Kredensialing' : 'Pengajuan Uji Kompetensi' }}
                        </h2>
                        <p class="text-muted mb-0">Lengkapi data di bawah ini</p>
                    </div>
                    <a href="{{ route('perawat.pengajuan.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <div class="form-card">
                    <form action="{{ route('perawat.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Kirim Metode ke Controller --}}
                        <input type="hidden" name="metode" value="{{ $metode }}">

                        {{-- 1. STATUS PENGAJUAN --}}
                        <div class="mb-4">
                            <label class="form-label">Jenis Pengajuan</label>

                            <select name="jenis_pengajuan" class="form-select form-select-lg bg-light" required>
                                {{-- LOGIKA: Jika belum punya lisensi tipe ini, tampilkan BARU --}}
                                @if (!$sudahPunyaLisensi)
                                    <option value="baru" selected>Pengajuan Baru</option>
                                @else
                                    {{-- Jika sudah punya, paksa PERPANJANGAN --}}
                                    <option value="perpanjangan" selected>Perpanjangan / Re-Sertifikasi</option>
                                @endif
                            </select>

                            {{-- Info Helper --}}
                            @if ($sudahPunyaLisensi)
                                <div class="alert alert-warning mt-2 small d-flex align-items-center">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    Anda sudah terdaftar di kategori ini. Silakan lakukan perpanjangan.
                                </div>
                            @else
                                <div class="form-text text-muted mt-2">
                                    Anda belum memiliki dokumen kategori ini, silakan ajukan baru.
                                </div>
                            @endif
                        </div>

                        <hr class="border-light my-4">

                        {{-- 2. FORM DINAMIS --}}

                        {{-- A. FORM BARU (Render jika belum punya) --}}
                        @if (!$sudahPunyaLisensi)
                            <div id="area-baru">
                                <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    @if ($metode == 'interview_only')
                                        <strong>Syarat Kredensialing:</strong> Upload Sertifikat PK (Ujikom), Logbook, dan
                                        Surat Permohonan dalam 1 file PDF.
                                    @else
                                        Silakan upload dokumen persyaratan lengkap dalam 1 file PDF.
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">File Dokumen Pengajuan (PDF) <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="file_dokumen_baru" class="form-control"
                                        accept="application/pdf" required>
                                </div>
                            </div>
                        @endif

                        {{-- B. FORM PERPANJANGAN (Render jika sudah punya) --}}
                        @if ($sudahPunyaLisensi)
                            <div id="area-perpanjangan">
                                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning mb-3">
                                    <i class="bi bi-arrow-repeat me-2"></i>
                                    Upload dokumen lama untuk verifikasi perpanjangan.
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                {{ $metode == 'interview_only' ? 'SPK / RKK Lama' : 'Sertifikat Kompetensi Lama' }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" name="file_sertifikat_lama" class="form-control"
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Surat Rekomendasi <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" name="file_surat_rekomendasi" class="form-control"
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <hr class="border-light my-4">

                        {{-- 3. LINK GDRIVE --}}
                        <div class="mb-4">
                            <label class="form-label">Link Google Drive (Berkas Pendukung) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-success border-end-0"><i
                                        class="bi bi-google"></i></span>
                                <input type="url" name="link_gdrive" class="form-control border-start-0 ps-0"
                                    placeholder="https://drive.google.com/..." required value="{{ old('link_gdrive') }}">
                            </div>
                        </div>

                        {{-- 4. KFK --}}
                        <div class="mb-4">
                            <label class="form-label">Jenjang Kompetensi yang Diajukan <span
                                    class="text-danger">*</span></label>
                            <select name="kfk[]" id="choice-kfk" class="form-select" multiple required>
                                <option value="">Pilih Jenjang...</option>
                                <optgroup label="Jenjang PK">
                                    <option value="Perawat PK 1">Perawat PK 1</option>
                                    <option value="Perawat PK 2">Perawat PK 2</option>
                                    <option value="Perawat PK 3">Perawat PK 3</option>
                                    <option value="Perawat PK 4">Perawat PK 4</option>
                                    <option value="Perawat PK 5">Perawat PK 5</option>
                                </optgroup>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-send-fill me-2"></i>Kirim Pengajuan
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elementKfk = document.getElementById('choice-kfk');
            if (elementKfk) {
                new Choices(elementKfk, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih Kompetensi...',
                    searchEnabled: true
                });
            }
        });
    </script>
@endpush
