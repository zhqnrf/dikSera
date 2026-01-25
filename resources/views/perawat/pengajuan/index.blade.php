@extends('layouts.app')

@section('title', 'Status Pengajuan')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-body: #f8fafc;
            --card-border: #e2e8f0;

            /* Status Variables */
            --st-pending-bg: #f1f5f9;
            --st-pending-text: #475569;
            --st-primary-bg: #eff6ff;
            --st-primary-text: #2563eb;
            --st-success-bg: #f0fdf4;
            --st-success-text: #166534;
            --st-warning-bg: #fffbeb;
            --st-warning-text: #b45309;
            --st-danger-bg: #fef2f2;
            --st-danger-text: #b91c1c;
            --st-info-bg: #f0f9ff;
            --st-info-text: #0369a1;
            --st-purple-bg: #f3e8ff;
            --st-purple-text: #7e22ce;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* --- ANIMATIONS DEFINITIONS --- */
        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes subtlePulseYellow {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 251, 235, 0.7), inset 0 0 0 1px #fef3c7;
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 251, 235, 0), inset 0 0 0 1px #fef3c7;
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 251, 235, 0), inset 0 0 0 1px #fef3c7;
            }
        }

        @keyframes popInBouncy {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            70% {
                opacity: 1;
                transform: scale(1.02);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-enter {
            animation: slideUpFade 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }

        .stagger-item {
            opacity: 0;
        }

        /* --- Page Header --- */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.025em;
        }

        .page-subtitle {
            color: var(--text-gray);
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        /* --- Cards --- */
        .request-card {
            background: white;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }

        .request-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.07);
        }

        .card-header-custom {
            padding: 24px;
            background: white;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            flex-wrap: wrap;
        }

        .card-body-custom {
            padding: 24px;
        }

        /* --- Typography & Badges --- */
        .lisensi-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .lisensi-meta {
            font-size: 0.875rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .request-card:hover .status-badge {
            transform: scale(1.05);
        }

        /* Color Variants */
        .badge-pending {
            background: var(--st-pending-bg);
            color: var(--st-pending-text);
        }

        .badge-primary {
            background: var(--st-primary-bg);
            color: var(--st-primary-text);
        }

        .badge-success {
            background: var(--st-success-bg);
            color: var(--st-success-text);
        }

        .badge-warning {
            background: var(--st-warning-bg);
            color: var(--st-warning-text);
        }

        .badge-danger {
            background: var(--st-danger-bg);
            color: var(--st-danger-text);
        }

        .badge-info {
            background: var(--st-info-bg);
            color: var(--st-info-text);
        }

        .badge-purple {
            background: var(--st-purple-bg);
            color: var(--st-purple-text);
        }

        /* --- Action Boxes & Alerts --- */
        .alert-modern {
            padding: 16px;
            border-radius: 12px;
            font-size: 0.925rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            animation: fadeIn 0.5s ease-in-out both;
        }

        .alert-modern.blue {
            background: #eff6ff;
            border-color: #dbeafe;
            color: #1e40af;
        }

        .alert-modern.yellow {
            background: #fffbeb;
            border-color: #fef3c7;
            color: #92400e;
        }

        .alert-modern.red {
            background: #fef2f2;
            border-color: #fee2e2;
            color: #991b1b;
        }

        .alert-modern.yellow.action-required {
            animation: subtlePulseYellow 2s infinite;
        }

        .action-container {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            margin-top: 10px;
            animation: slideUpFade 0.5s ease-out both;
        }

        .section-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 12px;
            display: block;
            letter-spacing: 0.05em;
        }

        /* --- Form Elements --- */
        .form-label-sm {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn {
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        /* --- Certificate Box --- */
        .certificate-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: popInBouncy 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            animation-delay: 0.2s;
        }

        .certificate-icon {
            background: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            color: var(--st-success-text);
            font-size: 1.75rem;
        }

        /* --- Empty State --- */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 16px;
            border: 1px dashed var(--card-border);
            animation: fadeIn 0.8s ease-out both;
        }

        .empty-icon {
            font-size: 3.5rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="page-header animate-enter">
                    <h1 class="page-title">Status Pengajuan</h1>
                    <p class="page-subtitle">Pantau progres perpanjangan lisensi dan jadwal evaluasi Anda secara real-time.
                    </p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                @forelse($pengajuan as $item)
                    <div class="request-card stagger-item animate-enter"
                        style="animation-delay: {{ $loop->index * 0.15 + 0.2 }}s">

                        {{-- HEADER CARD --}}
                        <div class="card-header-custom">
                            <div>
                                <h5 class="lisensi-name">{{ $item->lisensiLama->nama ?? 'Lisensi Perawat' }}</h5>
                                <div class="lisensi-meta">
                                    <i class="bi bi-upc-scan"></i>
                                    Nomor: {{ $item->lisensiLama->nomor ?? '-' }}
                                </div>
                            </div>

                            {{-- STATUS BADGES --}}
                            <div>
                                @if ($item->status == 'pending')
                                    <span class="status-badge badge-pending"><i class="bi bi-hourglass-split"></i> Menunggu
                                        Verifikasi</span>
                                @elseif($item->status == 'method_selected')
                                    <span class="status-badge badge-primary"><i class="bi bi-pencil-square"></i> Siap
                                        Ujian</span>
                                @elseif($item->status == 'exam_passed')
                                    <span class="status-badge badge-info">
                                        <i class="bi bi-check-lg"></i>
                                        {{ $item->metode == 'interview_only' ? 'Siap Wawancara' : 'Lulus Tulis' }}
                                    </span>
                                @elseif($item->status == 'interview_scheduled')
                                    <span class="status-badge badge-warning"><i class="bi bi-calendar-event"></i> Jadwal
                                        Wawancara</span>
                                @elseif($item->status == 'completed')
                                    @if (is_null($item->metode))
                                        <span class="status-badge badge-success"><i class="bi bi-check-circle-fill"></i>
                                            Penerbitan Selesai</span>
                                    @else
                                        <span class="status-badge badge-success"><i class="bi bi-patch-check-fill"></i>
                                            Selesai</span>
                                    @endif
                                @elseif($item->status == 'rejected')
                                    <span class="status-badge badge-danger"><i class="bi bi-x-circle-fill"></i>
                                        Ditolak</span>
                                @elseif($item->status == 'interview_failed')
                                    <span class="status-badge badge-danger"><i class="bi bi-x-circle-fill"></i> Gagal
                                        Wawancara</span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body-custom">

                            {{-- INFO METODE --}}
                            @if (!is_null($item->metode) && !in_array($item->status, ['pending', 'rejected']))
                                <div class="alert-modern blue">
                                    <i class="bi bi-info-circle-fill fs-5"></i>
                                    <div>
                                        <span class="fw-bold d-block">Metode Evaluasi:</span>
                                        <span>
                                            @if ($item->metode == 'pg_only')
                                                Hanya Ujian Tulis
                                            @elseif($item->metode == 'interview_only')
                                                Hanya Wawancara
                                            @else
                                                Ujian Tulis + Wawancara
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- INFO LISENSI BARU --}}
                            @if (is_null($item->metode) && $item->status == 'pending')
                                <div class="alert-modern blue">
                                    <i class="bi bi-info-circle-fill fs-5"></i>
                                    <div>
                                        <span class="fw-bold d-block">Pengajuan Lisensi Baru</span>
                                        <span>Dokumen Anda sedang diperiksa oleh Admin.</span>
                                    </div>
                                </div>
                            @endif

                            {{-- INSTRUKSI UJIAN --}}
                            @if ($item->status == 'method_selected')
                                <div class="alert-modern yellow action-required">
                                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                    <div>
                                        <strong class="d-block mb-1">Tindakan Diperlukan:</strong>
                                        Silakan akses menu <strong>Ujian & Evaluasi</strong> untuk mengerjakan soal ujian.
                                    </div>
                                </div>
                            @endif

                            {{-- FORM: PENGAJUAN JADWAL --}}
                            @if (in_array($item->status, ['exam_passed', 'interview_failed']) &&
                                    in_array($item->metode, ['pg_interview', 'interview_only']))
                                <div class="action-container">
                                    <span class="section-label"><i class="bi bi-calendar-plus me-1"></i> Ajukan Jadwal
                                        Wawancara</span>

                                    {{-- Alert jika ditolak --}}
                                    @php $lastJadwal = $item->jadwalWawancara; @endphp
                                    @if ($lastJadwal && $lastJadwal->status == 'rejected')
                                        <div class="alert-modern red mb-4">
                                            <i class="bi bi-x-octagon-fill fs-5"></i>
                                            <div>
                                                <strong>Pengajuan Jadwal Ditolak:</strong>
                                                <p class="mb-0 mt-1">"{{ $lastJadwal->catatan_admin }}"</p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Alert jika Gagal Wawancara --}}
                                    @if ($item->status == 'interview_failed' && $lastJadwal && $lastJadwal->penilaian)
                                        <div class="alert-modern red mb-4">
                                            <i class="bi bi-x-octagon-fill fs-5"></i>
                                            <div>
                                                <strong>Hasil Wawancara: Gagal</strong>
                                                @if (!empty($lastJadwal->penilaian->catatan_pewawancara))
                                                    <p class="mb-0 mt-1">Catatan:
                                                        "{{ $lastJadwal->penilaian->catatan_pewawancara }}"</p>
                                                @endif
                                                <p class="mb-0 mt-2">Silakan ajukan ulang jadwal.</p>
                                            </div>
                                        </div>
                                    @endif

                                    <form action="{{ route('perawat.pengajuan.store_wawancara', $item->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label-sm">Pilih Pewawancara</label>
                                                <select name="penanggung_jawab_id" class="form-select" required>
                                                    <option value="">-- Silakan Pilih --</option>
                                                    @foreach ($pjs as $pj)
                                                        <option value="{{ $pj->id }}">{{ $pj->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label-sm">Usulan Lokasi</label>
                                                <input type="text" name="lokasi_wawancara" class="form-control"
                                                    placeholder="Contoh: R. Komite Lt.2" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label-sm">Tanggal</label>
                                                <input type="date" name="tgl_wawancara" class="form-control" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label-sm">Jam</label>
                                                <input type="time" name="jam_wawancara" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                                    <i class="bi bi-send me-2"></i> Kirim Pengajuan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            {{-- READ ONLY: STATUS JADWAL --}}
                            @if ($item->status == 'interview_scheduled' && $item->jadwalWawancara)
                                @php $jadwal = $item->jadwalWawancara; @endphp

                                {{-- JADWAL PENDING (Menunggu Admin) --}}
                                @if ($jadwal->status == 'pending')
                                    <div class="action-container bg-white border border-secondary border-opacity-25">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="section-label mb-0 text-dark">Detail Jadwal Wawancara</span>
                                            <span class="badge bg-warning text-dark rounded-pill px-3">Menunggu
                                                Konfirmasi</span>
                                        </div>
                                        <div class="row g-4 mt-1">
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block mb-1">Pewawancara</small>
                                                <div class="fw-semibold text-dark">{{ $jadwal->pewawancara->nama ?? '-' }}
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block mb-1">Waktu</small>
                                                <div class="fw-semibold text-dark">
                                                    {{ $jadwal->waktu_wawancara->format('d M Y, H:i') }} WIB</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block mb-1">Lokasi</small>
                                                <div class="fw-semibold text-dark">{{ $jadwal->lokasi }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- JADWAL APPROVED (Sudah di-acc Admin) --}}
                                @elseif ($jadwal->status == 'approved')
                                    <div
                                        class="action-container bg-white border border-success border-opacity-25 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="section-label mb-0 text-dark fw-bold">
                                                <i class="bi bi-calendar-check-fill text-success me-2"></i> Jadwal
                                                Terkonfirmasi
                                            </span>
                                            <span class="badge bg-success text-white rounded-pill px-3">Disetujui
                                                Admin</span>
                                        </div>

                                        {{-- Alert Info Perubahan --}}
                                        <div class="alert alert-success d-flex align-items-center py-2 px-3 small mb-3">
                                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                            <div>Silakan hadir sesuai waktu dan lokasi yang tercantum di bawah ini.</div>
                                        </div>

                                        <div class="row g-4">
                                            {{-- Pewawancara --}}
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block mb-1 text-uppercase"
                                                    style="font-size: 0.7rem;">Pewawancara</small>
                                                <div class="fw-bold text-dark">{{ $jadwal->pewawancara->nama ?? '-' }}
                                                </div>
                                            </div>

                                            {{-- Waktu --}}
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block mb-1 text-uppercase"
                                                    style="font-size: 0.7rem;">Waktu Pelaksanaan</small>
                                                <div class="fw-bold text-dark">
                                                    <span
                                                        class="text-primary">{{ $jadwal->waktu_wawancara->format('d M Y') }}</span>
                                                    <span class="mx-1">•</span>
                                                    {{ $jadwal->waktu_wawancara->format('H:i') }} WIB
                                                </div>
                                            </div>

                                            {{-- Lokasi --}}
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block mb-1 text-uppercase"
                                                    style="font-size: 0.7rem;">Lokasi</small>
                                                <div class="fw-bold text-dark">{{ $jadwal->lokasi }}</div>
                                            </div>

                                            {{-- DESKRIPSI SKILL (FITUR BARU) --}}
                                            @if (!empty($jadwal->deskripsi_skill))
                                                <div class="col-12">
                                                    <div
                                                        class="p-3 bg-light rounded border border-secondary border-opacity-10 mt-2">
                                                        <small class="text-primary fw-bold text-uppercase mb-2 d-block">
                                                            <i class="bi bi-card-checklist me-1"></i> Materi / Fokus
                                                            Wawancara
                                                        </small>
                                                        <p class="mb-0 text-dark"
                                                            style="font-size: 0.95rem; line-height: 1.5;">
                                                            {!! nl2br(e($jadwal->deskripsi_skill)) !!}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- Jika Status Completed --}}
                            @if ($item->status == 'completed')
                                <div class="certificate-box mt-3">
                                    <div class="certificate-icon"><i class="bi bi-trophy-fill"></i></div>

                                    @if ($item->metode == 'interview_only')
                                        {{-- Tampilan Kredensialing --}}
                                        <h4 class="fw-bold text-success mb-2">Selamat! Kredensial Valid</h4>
                                        <p class="text-secondary mb-4 col-md-8 mx-auto">
                                            Proses validasi kompetensi Anda telah selesai. Silakan unduh hasil keputusan.
                                        </p>
                                        {{-- [PERBAIKAN] Menggunakan route 'perawat.pengajuan.print' --}}
                                        <a href="{{ route('perawat.pengajuan.print', $item->id) }}"
                                            class="btn btn-primary fw-bold px-4 py-2 shadow-sm" target="_blank">
                                            <i class="bi bi-file-earmark-arrow-down me-2"></i> Unduh Dokumen SK
                                        </a>
                                    @else
                                        {{-- Tampilan Uji Kompetensi / Lisensi Baru --}}
                                        <h4 class="fw-bold text-success mb-2">Selamat! Lisensi Terbit</h4>
                                        <p class="text-secondary mb-4 col-md-8 mx-auto">
                                            Anda dinyatakan kompeten. Sertifikat digital Anda sudah siap.
                                        </p>
                                        {{-- [PERBAIKAN] Menggunakan route 'perawat.pengajuan.print' --}}
                                        <a href="{{ route('perawat.pengajuan.print', $item->id) }}"
                                            class="btn btn-success fw-bold px-4 py-2 shadow-sm" target="_blank">
                                            <i class="bi bi-award-fill me-2"></i> Unduh Sertifikat PDF
                                        </a>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="empty-state animate-enter">
                        <i class="bi bi-clipboard-x empty-icon"></i>
                        <h5 class="fw-bold text-dark">Belum Ada Pengajuan</h5>
                        <p class="text-muted">Riwayat pengajuan perpanjangan lisensi Anda akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
