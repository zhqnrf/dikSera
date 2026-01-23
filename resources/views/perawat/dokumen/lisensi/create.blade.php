@extends('layouts.app')

@section('title', 'Form Pengajuan Sertifikat')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        .form-section { display: none; }
        .form-section.active { display: block; animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .required-star { color: red; }
    </style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-file-earmark-text me-2 text-primary"></i> Form Pengajuan Sertifikat</h5>
                </div>
                <div class="card-body p-4">
                    
                    {{-- Arahkan ke PengajuanSertifikatController --}}
                    <form action="{{ route('perawat.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. PILIH STATUS PENGAJUAN --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Status Pengajuan <span class="required-star">*</span></label>
                            <select name="jenis_pengajuan" id="jenis_pengajuan" class="form-select form-select-lg bg-light" required>
                                <option value="" selected disabled>-- Pilih Jenis Pengajuan --</option>
                                <option value="baru">Pengajuan Baru (New Submission)</option>
                                <option value="lama">Perpanjangan (Extension)</option>
                            </select>
                            <div class="form-text">Pilih "Baru" untuk lisensi baru, atau "Perpanjangan" untuk lisensi yang sudah ada.</div>
                        </div>

                        <hr class="my-4 dashed">

                        {{-- SECTION: PENGAJUAN BARU --}}
                        <div id="section-baru" class="form-section">
                            <div class="alert alert-info border-0 d-flex align-items-center gap-3">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                                <div>
                                    <strong>Dokumen Pengajuan Baru</strong><br>
                                    Silakan unggah satu file PDF berisi seluruh berkas persyaratan pengajuan baru.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload Dokumen Lengkap (PDF) <span class="required-star">*</span></label>
                                <input type="file" name="file_dokumen_baru" class="form-control" accept=".pdf">
                                <div class="form-text">Maksimal ukuran file 5MB.</div>
                            </div>
                        </div>

                        {{-- SECTION: PENGAJUAN LAMA --}}
                        <div id="section-lama" class="form-section">
                            <div class="alert alert-warning border-0 d-flex align-items-center gap-3 mb-4">
                                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                <div>
                                    <strong>Dokumen Perpanjangan</strong><br>
                                    Lengkapi dokumen di bawah ini untuk verifikasi perpanjangan lisensi.
                                </div>
                            </div>

                            {{-- Dropdown Pilih Lisensi --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Lisensi yang Diperpanjang <span class="required-star">*</span></label>
                                <select name="lisensi_id" class="form-select">
                                    <option value="">-- Pilih Lisensi --</option>
                                    @foreach($myLisensis ?? [] as $l)
                                        <option value="{{ $l->id }}">{{ $l->nama }} - {{ $l->nomor }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Surat Rekomendasi <span class="required-star">*</span></label>
                                    <input type="file" name="file_rekomendasi" class="form-control" accept=".pdf,.jpg,.png">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Sertifikat Lama <span class="required-star">*</span></label>
                                    <input type="file" name="file_sertifikat_lama" class="form-control" accept=".pdf,.jpg,.png">
                                </div>
                            </div>
                        </div>

                        {{-- GLOBAL: LINK GDRIVE --}}
                        <div id="section-global" class="form-section">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Link Google Drive <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-google"></i></span>
                                    <input type="url" name="link_gdrive" class="form-control" placeholder="https://drive.google.com/..." required>
                                </div>
                                <div class="form-text">Pastikan link GDrive dapat diakses (Public/Anyone with the link).</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="bi bi-send-fill me-2"></i> Kirim Pengajuan
                                </button>
                                <a href="{{ route('perawat.lisensi.index') }}" class="btn btn-light text-muted">Batal</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectJenis = document.getElementById('jenis_pengajuan');
        const sectionBaru = document.getElementById('section-baru');
        const sectionLama = document.getElementById('section-lama');
        const sectionGlobal = document.getElementById('section-global');

        // Reset Inputs function
        function resetInputs(container) {
            container.querySelectorAll('input, select').forEach(el => el.value = '');
        }

        selectJenis.addEventListener('change', function() {
            const val = this.value;
            
            // Sembunyikan semua dulu
            sectionBaru.classList.remove('active');
            sectionLama.classList.remove('active');
            sectionGlobal.classList.remove('active');

            if (val === 'baru') {
                sectionBaru.classList.add('active');
                sectionGlobal.classList.add('active');
                
                // Set required attr
                sectionBaru.querySelector('input').setAttribute('required', 'required');
                
                // Remove required from Lama
                sectionLama.querySelectorAll('input, select').forEach(el => el.removeAttribute('required'));
                
            } else if (val === 'lama') {
                sectionLama.classList.add('active');
                sectionGlobal.classList.add('active');

                // Set required attr
                sectionLama.querySelectorAll('input, select').forEach(el => el.setAttribute('required', 'required'));

                // Remove required from Baru
                sectionBaru.querySelector('input').removeAttribute('required');
            }
        });
    });
</script>
@endpush