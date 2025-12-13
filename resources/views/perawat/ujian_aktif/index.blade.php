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
    }

    .exam-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.06);
        border-color: #dbeafe; /* Warna biru muda saat hover */
    }

    /* --- 2. Custom Soft Badges (Penting untuk tampilan modern) --- */
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

    /* Warna Status Waktu */
    .bg-soft-success { background-color: #dcfce7; color: #166534; }
    .bg-soft-warning { background-color: #fef9c3; color: #854d0e; }
    .bg-soft-secondary { background-color: #f1f5f9; color: #475569; }
    .bg-soft-danger { background-color: #fee2e2; color: #991b1b; }

    /* Warna Target Peserta */
    .bg-soft-purple { background-color: #f3e8ff; color: #6b21a8; }
    .bg-soft-blue { background-color: #dbeafe; color: #1e40af; }

    /* --- 3. Meta Data Icons --- */
    .icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .icon-box-primary { background: #eff6ff; color: #2563eb; }
    .icon-box-danger { background: #fef2f2; color: #dc2626; }

    /* --- 4. Typography Utils --- */
    .text-limit {
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Batasi 2 baris */
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 42px; /* Menjaga tinggi kartu tetap sama */
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Header Section --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h4 class="fw-bold text-dark mb-1">Daftar Ujian & Survei</h4>
            <p class="text-muted small mb-0">Pilih form yang tersedia di bawah ini untuk mulai mengerjakan.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            {{-- Menampilkan tanggal hari ini agar user aware --}}
            <span class="badge bg-white border text-muted shadow-sm py-2 px-3 rounded-pill">
                <i class="bi bi-calendar-day me-1"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    @if($forms->isEmpty())
        {{-- Empty State yang lebih cantik --}}
        <div class="d-flex flex-column align-items-center justify-content-center py-5 bg-white rounded-4 border border-dashed shadow-sm" style="min-height: 300px;">
            <div class="bg-light rounded-circle p-4 mb-3">
                <i class="bi bi-clipboard-x text-muted display-4"></i>
            </div>
            <h6 class="fw-bold text-dark">Belum Ada Ujian Tersedia</h6>
            <p class="text-muted small text-center px-3" style="max-width: 400px;">
                Saat ini belum ada jadwal ujian atau form survei yang dipublikasikan untuk Anda. Silakan cek kembali secara berkala.
            </p>
        </div>
    @else
        <div class="row g-4">
            @foreach($forms as $form)
                @php
                    // Logika Status Waktu
                    $isStarted = $now->greaterThanOrEqualTo($form->waktu_mulai);
                    $isEnded = $now->greaterThan($form->waktu_selesai);

                    // Setup Tampilan Berdasarkan Status
                    if($isEnded) {
                        $badgeClass = 'bg-soft-secondary';
                        $badgeIcon = 'bi-x-circle';
                        $statusLabel = 'Selesai / Ditutup';
                        $btnClass = 'btn-light text-muted border'; // Tombol abu-abu
                        $btnLabel = 'Waktu Habis';
                        $cardOpacity = 'opacity-75'; // Sedikit transparan
                    } elseif(!$isStarted) {
                        $badgeClass = 'bg-soft-warning';
                        $badgeIcon = 'bi-clock';
                        $statusLabel = 'Belum Dimulai';
                        $btnClass = 'btn-light text-muted border';
                        $btnLabel = 'Menunggu Jadwal';
                        $cardOpacity = '';
                    } else {
                        $badgeClass = 'bg-soft-success'; // Hijau soft
                        $badgeIcon = 'bi-check-circle';
                        $statusLabel = 'Sedang Berlangsung';
                        $btnClass = 'btn-primary shadow-sm'; // Tombol biru primary
                        $btnLabel = 'Kerjakan Sekarang';
                        $cardOpacity = '';
                    }
                @endphp

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 exam-card position-relative {{ $cardOpacity }}">

                        <div class="card-body p-4">
                            {{-- Header Kartu: Badges --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                {{-- Badge Target --}}
                                @if($form->target_peserta == 'khusus')
                                    <span class="badge-soft bg-soft-purple">
                                        <i class="bi bi-lock-fill"></i> Undangan
                                    </span>
                                @else
                                    <span class="badge-soft bg-soft-blue">
                                        <i class="bi bi-globe"></i> Public
                                    </span>
                                @endif

                                {{-- Badge Status Waktu --}}
                                <span class="badge-soft {{ $badgeClass }}">
                                    <i class="bi {{ $badgeIcon }}"></i> {{ $statusLabel }}
                                </span>
                            </div>

                            {{-- Judul & Deskripsi --}}
                            <h5 class="fw-bold text-dark mb-2 text-truncate" title="{{ $form->judul }}">
                                {{ $form->judul }}
                            </h5>
                            <p class="text-muted small mb-4 text-limit">
                                {{ $form->deskripsi ?? 'Tidak ada deskripsi tambahan untuk ujian ini.' }}
                            </p>

                            {{-- Info Waktu (Grid Layout) --}}
                            <div class="bg-light rounded-3 p-3 mb-4 border border-light">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-box icon-box-primary me-3">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted" style="font-size: 10px; text-transform: uppercase;">Waktu Mulai</span>
                                        <span class="fw-bold text-dark" style="font-size: 13px;">
                                            {{ $form->waktu_mulai->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-danger me-3">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted" style="font-size: 10px; text-transform: uppercase;">Batas Akhir</span>
                                        <span class="fw-bold text-dark" style="font-size: 13px;">
                                            {{ $form->waktu_selesai->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            @if($isStarted && !$isEnded)
                                <a href="{{ route('perawat.ujian.show', $form->slug) }}"
                                   class="btn {{ $btnClass }} w-100 rounded-pill py-2 fw-bold stretched-link">
                                    {{ $btnLabel }} <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            @else
                                <button class="btn {{ $btnClass }} w-100 rounded-pill py-2 fw-bold" disabled>
                                    {{ $btnLabel }}
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
