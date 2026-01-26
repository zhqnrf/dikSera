@extends('layouts.app')

@section('title', 'Daftar Ujian & Form Aktif')

@push('styles')
    <style>
        /* --- 1. Card Styling --- */
        .exam-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            background: #fff;
            overflow: hidden;
            position: relative;
        }

        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06);
            border-color: #dbeafe;
        }

        /* --- 2. Custom Soft Badges --- */
        .badge-soft {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Varian Warna Badge */
        .bg-soft-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .bg-soft-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .bg-soft-secondary {
            background-color: #f1f5f9;
            color: #475569;
        }

        .bg-soft-primary {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .bg-soft-purple {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .bg-soft-blue {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .bg-soft-info {
            background-color: #e0f7fa;
            color: #006064;
        }

        /* --- 3. Info Box Waktu --- */
        .time-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #f1f5f9;
        }

        .icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .icon-wrap-blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .icon-wrap-red {
            background: #fef2f2;
            color: #ef4444;
        }

        /* --- 4. Utils --- */
        .text-limit {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 40px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Daftar Ujian & Survei</h4>
                <p class="text-muted small mb-0">Pilih form yang tersedia di bawah ini untuk mulai mengerjakan.</p>
            </div>

            {{-- Date Badge --}}
            <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border">
                <i class="bi bi-calendar-event text-primary"></i>
                <span class="fw-bold text-dark small">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>

        @if ($forms->isEmpty())
            {{-- Empty State --}}
            <div class="d-flex flex-column align-items-center justify-content-center py-5 bg-white rounded-4 border border-dashed text-center"
                style="min-height: 350px;">
                <div class="bg-light rounded-circle p-4 mb-3 text-muted">
                    <i class="bi bi-clipboard-x display-3 opacity-25"></i>
                </div>
                <h5 class="fw-bold text-dark">Belum Ada Ujian Tersedia</h5>
                <p class="text-muted small px-3" style="max-width: 450px;">
                    Saat ini belum ada jadwal ujian atau form survei yang dipublikasikan untuk akun Anda. Silakan cek
                    kembali secara berkala.
                </p>
                <button onclick="location.reload()" class="btn btn-outline-primary rounded-pill px-4 mt-2">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh Halaman
                </button>
            </div>
        @else
            <div class="row g-4">
                @foreach ($forms as $form)
                    @php
                        // --- 1. AMBIL INFO PENGAJUAN TERKAIT (Untuk cek metode) ---
                        // Asumsi: Kita bisa mendeteksi pengajuan aktif user.
                        // Jika tidak ada di controller, kita pakai logika umum saja.
                        // (Idealnya controller mengirim variabel $pengajuanAktif)
                        $user = auth()->user();
                        $pengajuanAktif = \App\Models\PengajuanSertifikat::where('user_id', $user->id)
                            ->whereIn('status', ['method_selected', 'exam_passed', 'interview_scheduled'])
                            ->latest()
                            ->first();

                        $isKredensialing = $pengajuanAktif && $pengajuanAktif->metode == 'interview_only';

                        // --- 2. LOGIKA STATUS UJIAN ---
                        $userResult = $form->examResults->first();
                        $isSubmitted = $userResult !== null;

                        $isStarted = $now->greaterThanOrEqualTo($form->waktu_mulai);
                        $isEnded = $now->greaterThan($form->waktu_selesai);

                        // Cek hasil terakhir (untuk remidi)
                        $lastResult = $form->examResults->sortByDesc('id')->first();
                        $isRemidiLast = $lastResult && ($lastResult->remidi ?? $lastResult->total_nilai < 75);

                        // --- 3. PENENTUAN BADGE & TOMBOL ---
                        if ($isKredensialing) {
                            // JIKA KREDENSIALING (SKIP UJIAN)
                            $badgeClass = 'bg-soft-info';
                            $badgeIcon = 'bi-info-circle';
                            $statusLabel = 'Kredensialing';

                            $btnClass = 'btn-light text-muted border';
                            $btnLabel = 'Tidak Perlu Ujian';
                            $btnIcon = 'bi-slash-circle';
                            $linkRoute = '#';
                            $isClickable = false;
                            $opacityClass = 'opacity-75';
                        } elseif ($isSubmitted && !$isRemidiLast) {
                            // SUDAH LULUS
                            $badgeClass = 'bg-soft-primary';
                            $badgeIcon = 'bi-check-all';
                            $statusLabel = 'Selesai Dikerjakan';

                            $btnClass = 'btn-outline-primary';
                            $btnLabel = 'Lihat Nilai Saya';
                            $btnIcon = 'bi-trophy';
                            $linkRoute = route('perawat.ujian.selesai', ['form' => $form->slug]); // Route Selesai
                            $isClickable = true;
                            $opacityClass = '';
                        } elseif ($isRemidiLast && $isStarted && !$isEnded) {
                            // REMIDI (Boleh Ulang)
                            $badgeClass = 'bg-soft-warning';
                            $badgeIcon = 'bi-exclamation-triangle';
                            $statusLabel = 'Remidi';

                            $btnClass = 'btn-danger';
                            $btnLabel = 'Kerjakan Lagi';
                            $btnIcon = 'bi-arrow-repeat';
                            $linkRoute = route('perawat.ujian.kerjakan', $form->slug); // [FIX] Ganti show jadi kerjakan
                            $isClickable = true;
                            $opacityClass = '';
                        } elseif ($isRemidiLast && $isEnded) {
                            // REMIDI (Waktu Habis)
                            $badgeClass = 'bg-soft-secondary';
                            $badgeIcon = 'bi-x-circle';
                            $statusLabel = 'Remidi (Waktu Habis)';

                            $btnClass = 'btn-light text-muted border';
                            $btnLabel = 'Waktu Habis';
                            $btnIcon = 'bi-clock-history';
                            $linkRoute = '#';
                            $isClickable = false;
                            $opacityClass = 'opacity-75 grayscale';
                        } elseif ($isEnded) {
                            // EXPIRED
                            $badgeClass = 'bg-soft-secondary';
                            $badgeIcon = 'bi-x-circle';
                            $statusLabel = 'Ditutup / Expired';

                            $btnClass = 'btn-light text-muted border';
                            $btnLabel = 'Waktu Habis';
                            $btnIcon = 'bi-clock-history';
                            $linkRoute = '#';
                            $isClickable = false;
                            $opacityClass = 'opacity-75 grayscale';
                        } elseif (!$isStarted) {
                            // BELUM MULAI
                            $badgeClass = 'bg-soft-warning';
                            $badgeIcon = 'bi-hourglass';
                            $statusLabel = 'Belum Dimulai';

                            $btnClass = 'btn-light text-muted border';
                            $btnLabel = 'Menunggu Jadwal';
                            $btnIcon = 'bi-calendar';
                            $linkRoute = '#';
                            $isClickable = false;
                            $opacityClass = '';
                        } else {
                            // NORMAL (Bisa Dikerjakan)
                            $badgeClass = 'bg-soft-success';
                            $badgeIcon = 'bi-play-circle-fill';
                            $statusLabel = 'Sedang Berlangsung';

                            $btnClass = 'btn-primary shadow-sm';
                            $btnLabel = 'Kerjakan Sekarang';
                            $btnIcon = 'bi-arrow-right-circle';
                            $linkRoute = route('perawat.ujian.kerjakan', $form->slug); // [FIX] Ganti show jadi kerjakan
                            $isClickable = true;
                            $opacityClass = '';
                        }
                    @endphp

                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 exam-card {{ $opacityClass }}">
                            <div class="card-body p-4 d-flex flex-column">

                                {{-- Badges Header --}}
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    {{-- Badge Target --}}
                                    @if ($form->target_peserta == 'khusus')
                                        <span class="badge-soft bg-soft-purple" title="Undangan Khusus">
                                            <i class="bi bi-star-fill"></i> Khusus
                                        </span>
                                    @else
                                        <span class="badge-soft bg-soft-blue">
                                            <i class="bi bi-globe"></i> Public
                                        </span>
                                    @endif

                                    {{-- Badge Status --}}
                                    <span class="badge-soft {{ $badgeClass }}">
                                        <i class="bi {{ $badgeIcon }}"></i> {{ $statusLabel }}
                                    </span>
                                </div>

                                {{-- Judul --}}
                                <h5 class="fw-bold text-dark mb-2 text-truncate" title="{{ $form->judul }}">
                                    {{ $form->judul }}
                                </h5>
                                <p class="text-muted small mb-4 text-limit">
                                    {{ $form->deskripsi ?? 'Silakan kerjakan ujian ini dengan teliti.' }}
                                </p>

                                {{-- Info Waktu --}}
                                <div class="time-box mb-4 mt-auto">
                                    {{-- Mulai --}}
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-wrap icon-wrap-blue me-3">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-secondary"
                                                style="font-size: 10px; font-weight: 600;">MULAI</span>
                                            <span class="fw-bold text-dark" style="font-size: 12px;">
                                                {{ $form->waktu_mulai->format('d M Y, H:i') }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Selesai --}}
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrap icon-wrap-red me-3">
                                            <i class="bi bi-calendar-x"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-secondary" style="font-size: 10px; font-weight: 600;">BATAS
                                                AKHIR</span>
                                            <span class="fw-bold text-dark" style="font-size: 12px;">
                                                {{ $form->waktu_selesai->format('d M Y, H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Alert Khusus Kredensialing --}}
                                @if ($isKredensialing)
                                    <div
                                        class="alert alert-info py-2 px-3 small border-0 bg-opacity-10 bg-info text-info mb-3">
                                        <i class="bi bi-info-circle me-1"></i> Jalur Kredensialing tidak perlu ujian.
                                    </div>
                                @endif

                                {{-- Action Button --}}
                                <a href="{{ $linkRoute }}"
                                    class="btn {{ $btnClass }} w-100 rounded-pill py-2 fw-bold {{ !$isClickable ? 'disabled' : '' }}">
                                    @if ($isSubmitted)
                                        <i class="bi {{ $btnIcon }} me-1"></i> {{ $btnLabel }}
                                    @else
                                        {{ $btnLabel }} <i class="bi {{ $btnIcon }} ms-1"></i>
                                    @endif
                                </a>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
