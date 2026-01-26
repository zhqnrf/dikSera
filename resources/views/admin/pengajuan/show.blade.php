@extends('layouts.app')

@section('title', 'Detail Verifikasi - ' . $pengajuan->user->name)

@push('styles')
    <style>
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: white;
            transition: transform 0.2s;
        }

        .header-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .info-group {
            padding: 12px;
            background-color: #f8fafc;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }

        .file-item:hover {
            border-color: #cbd5e1;
            background-color: #f8fafc;
        }

        .status-card {
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            background: #f0f9ff;
            border: 1px dashed #bae6fd;
            color: #0369a1;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">

        {{-- HEADER NAVIGATION --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Detail Verifikasi Berkas</h1>
                <p class="text-muted small mb-0">Verifikasi dokumen pengajuan dan tentukan metode evaluasi.</p>
            </div>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="row g-4">

            {{-- KOLOM KIRI: INFO USER & BERKAS --}}
            <div class="col-lg-8">

                {{-- 1. KARTU PROFIL USER --}}
                <div class="card card-custom mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                                <i class="bi bi-person-bounding-box fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1">{{ $pengajuan->user->name }}</h5>
                                <div class="text-muted small mb-2"><i class="bi bi-envelope me-1"></i>
                                    {{ $pengajuan->user->email }}</div>

                                {{-- [FIX ERROR] Menggunakan unit_kerja langsung dari user atau string default --}}
                                <div class="text-muted small">
                                    <i class="bi bi-building me-1"></i> Unit Kerja:
                                    {{ $pengajuan->user->unit_kerja ?? 'RSUD Simpang Lima Gumul' }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="header-label">JENIS PENGAJUAN</span>
                                @if ($pengajuan->jenis_pengajuan == 'baru')
                                    <span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i
                                            class="bi bi-stars me-1"></i> BARU</span>
                                @else
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i
                                            class="bi bi-arrow-repeat me-1"></i> PERPANJANGAN</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. KARTU DOKUMEN --}}
                <div class="card card-custom">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="fw-bold m-0"><i class="bi bi-folder2-open me-2 text-primary"></i>Kelengkapan Dokumen
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        {{-- LINK GDRIVE --}}
                        <div class="file-item">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded me-3">
                                    <i class="bi bi-google fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Google Drive</div>
                                    <div class="small text-muted">Berkas pendukung lengkap</div>
                                </div>
                            </div>
                            @if ($pengajuan->link_gdrive)
                                <a href="{{ $pengajuan->link_gdrive }}" target="_blank"
                                    class="btn btn-sm btn-outline-success fw-bold">
                                    Buka Link <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            @else
                                <span class="badge bg-secondary">Tidak Ada</span>
                            @endif
                        </div>

                        @if ($pengajuan->jenis_pengajuan == 'baru')
                            {{-- DOKUMEN BARU --}}
                            <div class="file-item">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded me-3">
                                        <i class="bi bi-file-earmark-pdf fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Dokumen Pengajuan</div>
                                        <div class="small text-muted">Format PDF</div>
                                    </div>
                                </div>
                                @if ($pengajuan->file_dokumen_baru)
                                    <a href="{{ asset('storage/' . $pengajuan->file_dokumen_baru) }}" target="_blank"
                                        class="btn btn-sm btn-outline-danger fw-bold">
                                        <i class="bi bi-download me-1"></i> Unduh
                                    </a>
                                @else
                                    <span class="badge bg-secondary">Belum Upload</span>
                                @endif
                            </div>
                        @else
                            {{-- SERTIFIKAT LAMA --}}
                            <div class="file-item">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded me-3">
                                        <i class="bi bi-award fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Sertifikat Lama</div>
                                        <div class="small text-muted">Bukti lisensi sebelumnya</div>
                                    </div>
                                </div>
                                @if ($pengajuan->file_sertifikat_lama)
                                    <a href="{{ asset('storage/' . $pengajuan->file_sertifikat_lama) }}" target="_blank"
                                        class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>
                                @else
                                    <span class="badge bg-secondary">Belum Upload</span>
                                @endif
                            </div>

                            {{-- SURAT REKOMENDASI --}}
                            <div class="file-item">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded me-3">
                                        <i class="bi bi-envelope-paper-heart fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Surat Rekomendasi</div>
                                        <div class="small text-muted">Dari Karu/Atasan</div>
                                    </div>
                                </div>
                                @if ($pengajuan->file_surat_rekomendasi)
                                    <a href="{{ asset('storage/' . $pengajuan->file_surat_rekomendasi) }}" target="_blank"
                                        class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>
                                @else
                                    <span class="badge bg-secondary">Belum Upload</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: AKSI & TANGGAL --}}
            <div class="col-lg-4">

                {{-- 1. SETTING TANGGAL (PENTING) --}}
                <div class="card card-custom mb-4">
                    <div class="card-header bg-light py-3">
                        <h6 class="fw-bold m-0 text-dark"><i class="bi bi-calendar-range me-2"></i>Masa Berlaku Lisensi
                        </h6>
                    </div>
                    <div class="card-body">
                        @php $sudahAdaTanggal = !is_null($pengajuan->tgl_mulai_berlaku); @endphp

                        @if ($sudahAdaTanggal)
                            <div class="alert alert-success d-flex align-items-center small p-2 mb-3">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div><strong>Tersimpan.</strong> Tanggal sudah ditetapkan.</div>
                            </div>
                        @else
                            <div class="alert alert-warning small p-2 mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                <strong>Penting:</strong> Set tanggal sebelum menyelesaikan proses.
                            </div>
                        @endif

                        <form action="{{ route('admin.pengajuan.updateDates', $pengajuan->id) }}" method="POST"
                            onsubmit="return confirm('Yakin simpan tanggal? Data ini akan tercetak di sertifikat.')">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">TANGGAL TERBIT</label>
                                <input type="date" name="tgl_mulai_berlaku" class="form-control"
                                    value="{{ $pengajuan->tgl_mulai_berlaku ?? ($pengajuan->lisensiLama->tgl_terbit ?? date('Y-m-d')) }}"
                                    {{ $sudahAdaTanggal ? 'readonly' : '' }}>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">TANGGAL EXPIRED (3 TH)</label>
                                <input type="date" name="tgl_akhir_berlaku" class="form-control"
                                    value="{{ $pengajuan->tgl_akhir_berlaku ?? ($pengajuan->lisensiLama->tgl_expired ?? date('Y-m-d', strtotime('+3 years'))) }}"
                                    {{ $sudahAdaTanggal ? 'readonly' : '' }}>
                            </div>

                            @if (!$sudahAdaTanggal)
                                <button type="submit" class="btn btn-primary w-100 fw-bold">
                                    <i class="bi bi-save me-2"></i>SIMPAN TANGGAL
                                </button>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- 2. AKSI APPROVAL --}}
                <div class="card card-custom">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold m-0 text-dark"><i class="bi bi-check2-square me-2"></i>Status & Aksi</h6>
                    </div>
                    <div class="card-body">

                        @if ($pengajuan->status == 'pending')
                            {{-- FORM APPROVE --}}
                            <form action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">METODE EVALUASI</label>
                                    <select name="metode_pilihan" class="form-select border-success fw-bold" required>
                                        <option value="pg_interview" selected>Ujian Tulis + Wawancara</option>
                                        <option value="interview_only">Hanya Wawancara (Kredensialing)</option>
                                    </select>
                                    <div class="form-text small mt-1 text-muted">
                                        Pilih "Hanya Wawancara" untuk skip ujian tulis.
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-success fw-bold py-2"
                                        onclick="return confirm('Setujui berkas ini?')">
                                        <i class="bi bi-check-lg me-2"></i> TERIMA BERKAS
                                    </button>

                                    {{-- FORM REJECT --}}
                                    <button type="button" class="btn btn-outline-danger py-2"
                                        onclick="document.getElementById('form-reject').submit();">
                                        <i class="bi bi-x-lg me-2"></i> TOLAK BERKAS
                                    </button>
                                </div>
                            </form>
                            {{-- HIDDEN FORM REJECT --}}
                            <form id="form-reject" action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}"
                                method="POST" class="d-none">@csrf</form>
                        @else
                            {{-- STATUS DISPLAY JIKA SUDAH DIPROSES --}}
                            <div class="status-card mb-3">
                                <div class="text-uppercase small fw-bold mb-1 opacity-75">Status Saat Ini</div>
                                <h5 class="fw-bold mb-0">
                                    {{ strtoupper(str_replace('_', ' ', $pengajuan->status)) }}
                                </h5>
                            </div>

                            <div class="info-group d-flex justify-content-between">
                                <span class="small fw-bold text-muted">Metode:</span>
                                @if ($pengajuan->metode == 'interview_only')
                                    <span class="badge bg-info text-dark">Kredensialing</span>
                                @else
                                    <span class="badge bg-primary">Uji Kompetensi</span>
                                @endif
                            </div>

                            {{-- TOMBOL FINALIZE (Selesaikan Proses) --}}
                            @if ($pengajuan->status != 'completed' && $pengajuan->status != 'rejected')
                                <hr>
                                <form action="{{ route('admin.pengajuan.complete', $pengajuan->id) }}" method="GET">
                                    <div class="alert alert-warning small mb-2">
                                        <i class="bi bi-info-circle me-1"></i> Klik tombol di bawah jika seluruh proses
                                        selesai untuk menerbitkan lisensi.
                                    </div>
                                    <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm"
                                        onclick="return confirm('Selesaikan proses dan terbitkan lisensi?')">
                                        <i class="bi bi-patch-check-fill me-2"></i> SELESAIKAN & TERBITKAN
                                    </button>
                                </form>
                            @endif

                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
