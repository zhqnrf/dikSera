@extends('layouts.app')

@section('title', 'Detail Pengajuan - ' . $pengajuan->user->name)

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        /* --- Header --- */
        .page-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }

        /* --- Cards --- */
        .content-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header-clean {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }

        .card-title-text {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body-clean {
            padding: 24px;
        }

        /* --- Profile Section --- */
        .profile-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .avatar-lg {
            width: 64px;
            height: 64px;
            background: #eff6ff;
            color: var(--primary-blue);
            font-size: 24px;
            font-weight: 700;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #dbeafe;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            width: 100%;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        /* --- Score Widget --- */
        .score-box {
            text-align: center;
            padding: 16px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .score-val {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .score-lbl {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .val-primary {
            color: var(--primary-blue);
        }

        .val-success {
            color: #16a34a;
        }

        .val-danger {
            color: #dc2626;
        }

        /* --- Timeline Vertical --- */
        .timeline {
            position: relative;
            padding-left: 12px;
            margin-top: 10px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 5px;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 24px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #cbd5e1;
            z-index: 2;
        }

        .timeline-item.active .timeline-dot {
            border-color: var(--primary-blue);
            background: var(--primary-blue);
            box-shadow: 0 0 0 3px #dbeafe;
        }

        .timeline-date {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .timeline-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 2px;
        }

        .timeline-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* --- Status Badges --- */
        .badge-status {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .bs-pending {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .bs-info {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        .bs-success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }

        .bs-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <div class="d-flex align-items-center gap-3">
                    <h1 class="page-title">Detail Pengajuan</h1>
                    <span class="badge bg-light text-secondary border">#{{ $pengajuan->id }}</span>
                </div>
                <p class="text-muted small mb-0 mt-1">Informasi lengkap pemohon dan riwayat seleksi.</p>
            </div>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-light border shadow-sm px-3 fw-bold"
                style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row g-4">

            {{-- KOLOM KIRI: INFO & HASIL --}}
            <div class="col-lg-8">

                {{-- 1. INFORMASI PEMOHON --}}
                <div class="content-card">
                    <div class="card-header-clean">
                        <div class="card-title-text"><i class="bi bi-person-badge text-primary"></i> Data Pemohon</div>
                        <div class="text-muted small">{{ $pengajuan->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="card-body-clean">
                        <div class="profile-wrapper">
                            {{-- Avatar Initials --}}
                            @php
                                $initials = collect(explode(' ', $pengajuan->user->name))
                                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                    ->take(2)
                                    ->join('');
                            @endphp
                            <div class="avatar-lg">{{ $initials }}</div>

                            {{-- Grid Data --}}
                            <div class="info-grid">
                                <div>
                                    <div class="info-label">Nama Lengkap</div>
                                    <div class="info-value">{{ $pengajuan->user->name }}</div>
                                </div>

                                {{-- TAMPILKAN JENIS --}}
                                <div>
                                    <div class="info-label">Jenis Pengajuan</div>
                                    <div class="info-value">
                                        @if ($pengajuan->jenis_pengajuan == 'baru')
                                            <span class="badge bg-primary">Pengajuan Baru</span>
                                        @else
                                            <span class="badge bg-secondary">Perpanjangan</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- TAMPILKAN LINK GDRIVE --}}
                                <div class="col-span-2">
                                    <div class="info-label">Dokumen Google Drive</div>
                                    @if ($pengajuan->link_gdrive)
                                        <a href="{{ $pengajuan->link_gdrive }}" target="_blank"
                                            class="btn btn-sm btn-outline-danger mt-1">
                                            <i class="bi bi-google me-1"></i> Buka Folder Drive
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-4 border-light">

                            {{-- LIST DOKUMEN --}}
                            <h6 class="fw-bold mb-3"><i class="bi bi-paperclip text-primary"></i> Berkas Lampiran</h6>
                            <div class="row g-3">
                                @if ($pengajuan->jenis_pengajuan == 'lama')
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-light">
                                            <label class="small text-muted fw-bold d-block mb-2">Surat Rekomendasi</label>
                                            @if ($pengajuan->file_rekomendasi)
                                                <a href="{{ asset('storage/' . $pengajuan->file_rekomendasi) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary w-100"><i
                                                        class="bi bi-eye"></i> Lihat</a>
                                            @else
                                                <span class="text-danger small">Kosong</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-light">
                                            <label class="small text-muted fw-bold d-block mb-2">Sertifikat Lama</label>
                                            @if ($pengajuan->file_sertifikat_lama)
                                                <a href="{{ asset('storage/' . $pengajuan->file_sertifikat_lama) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary w-100"><i
                                                        class="bi bi-eye"></i> Lihat</a>
                                            @else
                                                <span class="text-danger small">Kosong</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- PENGAJUAN BARU --}}
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-light">
                                            <label class="small text-muted fw-bold d-block mb-2">Dokumen Pengajuan Baru
                                                (PDF)</label>
                                            @if ($pengajuan->file_dokumen_baru)
                                                <a href="{{ asset('storage/' . $pengajuan->file_dokumen_baru) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary w-100"><i
                                                        class="bi bi-file-earmark-pdf"></i> Buka PDF Dokumen</a>
                                            @else
                                                <span class="text-danger small">Kosong</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. HASIL UJIAN TULIS (Conditional) --}}
                @if ($pengajuan->user && $pengajuan->user->examResult)
                    @php
                        $result = $pengajuan->user->examResult;
                        // Logika lulus sederhana (atau ambil dari kolom db jika ada)
                        $isPassed = $result->total_nilai >= 70;
                    @endphp
                    <div class="content-card">
                        <div class="card-header-clean">
                            <div class="card-title-text"><i class="bi bi-laptop text-primary"></i> Hasil Ujian Tulis (CBT)
                            </div>
                            <span
                                class="badge {{ $isPassed ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $isPassed ? 'text-success' : 'text-danger' }} border {{ $isPassed ? 'border-success' : 'border-danger' }} px-3">
                                {{ $isPassed ? 'LULUS (PASSING GRADE)' : 'TIDAK LULUS' }}
                            </span>
                        </div>
                        <div class="card-body-clean">
                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="score-box">
                                        <div class="score-val val-primary">{{ $result->total_nilai }}</div>
                                        <div class="score-lbl">SKOR TOTAL</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="score-box">
                                        <div class="score-val val-success">{{ $result->total_benar ?? 0 }}</div>
                                        <div class="score-lbl">BENAR</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="score-box">
                                        <div class="score-val val-danger">{{ $result->total_salah ?? 0 }}</div>
                                        <div class="score-lbl">SALAH</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <span class="text-muted small"><i class="bi bi-clock me-1"></i> Diselesaikan pada
                                    {{ \Carbon\Carbon::parse($result->created_at)->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 3. HASIL WAWANCARA (Conditional) --}}
                @if ($pengajuan->jadwalWawancara)
                    @php $jadwal = $pengajuan->jadwalWawancara; @endphp
                    <div class="content-card">
                        <div class="card-header-clean">
                            <div class="card-title-text"><i class="bi bi-mic text-primary"></i> Sesi Wawancara</div>
                            @if ($jadwal->penilaian)
                                <span class="badge bg-info bg-opacity-10 text-info border border-info px-3">
                                    {{ strtoupper($jadwal->penilaian->keputusan) }}
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3">BELUM
                                    DINILAI</span>
                            @endif
                        </div>
                        <div class="card-body-clean">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="info-label">Pewawancara</div>
                                    <div class="info-value">{{ $jadwal->pewawancara->nama ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Jadwal Pelaksanaan</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($jadwal->waktu_wawancara)->format('d F Y, H:i') }} WIB
                                    </div>
                                </div>
                            </div>

                            @if ($jadwal->penilaian)
                                <div class="p-3 bg-light border rounded-3 mt-3">
                                    <div class="row text-center g-2">
                                        <div class="col-4 border-end">
                                            <div class="fw-bold fs-5">{{ $jadwal->penilaian->skor_kompetensi }}</div>
                                            <div class="text-muted" style="font-size: 10px;">KOMPETENSI</div>
                                        </div>
                                        <div class="col-4 border-end">
                                            <div class="fw-bold fs-5">{{ $jadwal->penilaian->skor_sikap }}</div>
                                            <div class="text-muted" style="font-size: 10px;">SIKAP</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold fs-5">{{ $jadwal->penilaian->skor_pengetahuan }}</div>
                                            <div class="text-muted" style="font-size: 10px;">PENGETAHUAN</div>
                                        </div>
                                    </div>
                                    @if ($jadwal->penilaian->catatan)
                                        <hr class="my-3 opacity-25">
                                        <div class="small text-muted fst-italic">
                                            <i class="bi bi-chat-quote me-1"></i> "{{ $jadwal->penilaian->catatan }}"
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            {{-- KOLOM KANAN: STATUS & TIMELINE --}}
            <div class="col-lg-4">

                {{-- STATUS CARD --}}
                <div
                    class="content-card border-top-0 border-end-0 border-start-0 border-bottom-0 shadow-none bg-transparent">
                    <div class="bg-white border rounded-3 p-4 text-center shadow-sm">
                        <h6 class="info-label mb-3">Status Saat Ini</h6>
                        <div class="mb-4">
                            @if ($pengajuan->status == 'pending')
                                <span class="badge-status bs-pending"><i class="bi bi-hourglass-split"></i> Menunggu
                                    Approval</span>
                            @elseif($pengajuan->status == 'method_selected')
                                <span class="badge-status bs-info"><i class="bi bi-pencil-square"></i> Sedang Ujian</span>
                            @elseif($pengajuan->status == 'exam_passed')
                                <span class="badge-status bs-info"><i class="bi bi-check-circle"></i> Lulus Ujian</span>
                            @elseif($pengajuan->status == 'interview_scheduled')
                                <span class="badge-status bs-info"><i class="bi bi-calendar-event"></i> Jadwal
                                    Wawancara</span>
                            @elseif($pengajuan->status == 'completed')
                                <span class="badge-status bs-success"><i class="bi bi-check-all"></i> Selesai</span>
                            @elseif($pengajuan->status == 'rejected')
                                <span class="badge-status bs-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        @if ($pengajuan->status == 'pending')
                            <div class="d-grid gap-2">
                                {{-- TOMBOL BUKA MODAL APPROVE --}}
                                <button type="button" class="btn btn-success w-100 fw-bold shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalApprove">
                                    <i class="bi bi-check-lg"></i> Verifikasi & Terbitkan Lisensi
                                </button>

                                {{-- TOMBOL TOLAK (Tetap) --}}
                                <form action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-outline-danger w-100 fw-bold mt-2"
                                        onclick="return confirm('Tolak pengajuan ini?')">
                                        <i class="bi bi-x-lg"></i> Tolak Pengajuan
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-2 bg-light rounded text-muted small border text-center">
                                <i class="bi bi-lock-fill"></i> Pengajuan telah diproses.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- MODAL APPROVE (ADMIN ISI TANGGAL DISINI) --}}
                {{-- ========================================== --}}
                <div class="modal fade" id="modalApprove" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Input Data
                                    Lisensi</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <form action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="alert alert-info border-0 d-flex gap-3 align-items-center">
                                        <i class="bi bi-info-circle-fill fs-4"></i>
                                        <div>
                                            <strong>Perhatian Admin:</strong><br>
                                            Silakan isi Tanggal Terbit, Expired, dan Nomor Lisensi berdasarkan dokumen
                                            fisik/PDF yang telah Anda validasi. Data ini akan masuk ke riwayat lisensi
                                            perawat.
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        {{-- JIKA PENGAJUAN BARU, ADMIN HARUS ISI NAMA LISENSI JUGA --}}
                                        @if ($pengajuan->jenis_pengajuan == 'baru')
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Nama Lisensi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="nama" class="form-control"
                                                    placeholder="Contoh: STR, SIP, Sertifikat BTCLS" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Penerbit / Lembaga <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="lembaga" class="form-control"
                                                    placeholder="Contoh: Kemenkes RI" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold">Bidang Kompetensi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="bidang" class="form-control"
                                                    placeholder="Contoh: Keperawatan Umum" required>
                                            </div>
                                        @else
                                            {{-- JIKA PERPANJANGAN, TAMPILKAN INFO LAMA (READONLY) --}}
                                            <div class="col-12">
                                                <label class="form-label text-muted">Lisensi yang Diperpanjang</label>
                                                <input type="text" class="form-control bg-light"
                                                    value="{{ $pengajuan->lisensiLama->nama ?? '-' }}" readonly>
                                            </div>
                                        @endif

                                        <div class="col-12">
                                            <hr>
                                        </div>

                                        {{-- INPUT DATA BARU (WAJIB DIISI ADMIN) --}}
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Nomor Lisensi Baru <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="nomor" class="form-control font-monospace"
                                                placeholder="Nomor Surat / Sertifikat" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Tanggal Terbit <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="tgl_terbit" class="form-control" required
                                                value="{{ date('Y-m-d') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Berlaku Sampai (Expired) <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="tgl_expired" class="form-control" required>
                                        </div>

                                        {{-- Opsi KFK (Opsional, jika admin perlu set level) --}}
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Jenjang KFK (Opsional)</label>
                                            <input type="text" name="kfk_manual" class="form-control"
                                                placeholder="Isi jika perlu update jenjang (misal: PK 1, PK 2)">
                                            <div class="form-text small">Kosongkan jika tidak ada perubahan jenjang.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success fw-bold"><i
                                            class="bi bi-save me-1"></i> Simpan & Setujui</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- TIMELINE --}}
                <div class="content-card">
                    <div class="card-header-clean py-3">
                        <div class="card-title-text" style="font-size: 0.9rem;">Riwayat Proses</div>
                    </div>
                    <div class="card-body-clean">
                        <div class="timeline">
                            {{-- Step 1 --}}
                            <div class="timeline-item active">
                                <div class="timeline-dot"></div>
                                <div class="timeline-date">{{ $pengajuan->created_at->format('d M Y, H:i') }}</div>
                                <div class="timeline-title">Pengajuan Dibuat</div>
                                <div class="timeline-desc">Menunggu verifikasi admin.</div>
                            </div>

                            {{-- Step 2 (Approved) --}}
                            @if (in_array($pengajuan->status, ['method_selected', 'exam_passed', 'interview_scheduled', 'completed']))
                                <div class="timeline-item active">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-title">Disetujui Admin</div>
                                    <div class="timeline-desc">Metode:
                                        {{ $pengajuan->metode == 'pg_only' ? 'Tulis' : 'Wawancara' }}</div>
                                </div>
                            @endif

                            {{-- Step 3 (Exam) --}}
                            @if ($pengajuan->user->examResult)
                                <div class="timeline-item active">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-title">Ujian Tulis Selesai</div>
                                    <div class="timeline-desc">Skor: {{ $pengajuan->user->examResult->total_nilai }}</div>
                                </div>
                            @endif

                            {{-- Step 4 (Interview) --}}
                            @if ($pengajuan->jadwalWawancara)
                                <div class="timeline-item active">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-title">Wawancara Dijadwalkan</div>
                                    <div class="timeline-desc">Tanggal:
                                        {{ \Carbon\Carbon::parse($pengajuan->jadwalWawancara->waktu_wawancara)->format('d M') }}
                                    </div>
                                </div>
                                @if ($pengajuan->jadwalWawancara->penilaian)
                                    <div class="timeline-item active">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-title">Wawancara Selesai</div>
                                        <div class="timeline-desc">Hasil:
                                            {{ ucfirst($pengajuan->jadwalWawancara->penilaian->keputusan) }}</div>
                                    </div>
                                @endif
                            @endif

                            {{-- Final --}}
                            @if ($pengajuan->status == 'completed')
                                <div class="timeline-item active">
                                    <div class="timeline-dot" style="background: #16a34a; border-color: #16a34a;"></div>
                                    <div class="timeline-title text-success">Selesai</div>
                                    <div class="timeline-desc">Proses perpanjangan tuntas.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
