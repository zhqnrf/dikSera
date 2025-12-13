@extends('layouts.app')

@section('title', 'Detail Ujian')

@push('styles')
    <style>
        /* --- 1. Header Gradient & Card --- */
        .exam-header {
            background: radial-gradient(circle at 10% 20%, #eff6ff 0%, #ffffff 90%);
            border-bottom: 1px solid #f1f5f9;
            padding: 30px;
            border-radius: 16px 16px 0 0;
        }

        /* --- 2. Custom Badges (Soft Colors) --- */
        .badge {
            padding: 8px 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Warna Biru Soft */
        .bg-blue-soft { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        /* Warna Ungu Soft */
        .bg-purple-soft { background-color: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }

        /* Warna Hijau Soft (Status Publish) */
        .bg-success-soft { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

        /* Warna Abu Soft (Status Draft/Closed) */
        .bg-secondary-soft { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }


        /* --- 3. Info Box (Kanan) --- */
        .info-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 15px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f8fafc;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
        }

        /* --- 4. Deskripsi & Iframe Content --- */
        .description-content {
            line-height: 1.8;
            color: #475569;
            font-size: 0.95rem;
        }

        /* Agar Google Form terlihat menyatu */
        .description-content iframe {
            width: 100% !important;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-top: 15px;
            min-height: 600px; /* Tinggi minimal agar tidak scroll kecil */
        }

        .description-content a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px dashed #2563eb;
        }
        .description-content a:hover {
            border-bottom-style: solid;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-2">

    {{-- Tombol Kembali --}}
    <div class="mb-4">
        <a href="{{ route('perawat.ujian.index') }}" class="btn btn-sm btn-light border hover-shadow rounded-pill px-4 fw-medium text-muted">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- Header Form --}}
        <div class="exam-header">
            <div class="d-flex align-items-start justify-content-between gap-3 flex-column flex-lg-row">
                <div>
                    {{-- Judul Besar --}}
                    <h2 class="fw-bold text-dark mb-3">{{ $form->judul }}</h2>

                    {{-- Badges Group --}}
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if($form->target_peserta == 'khusus')
                            <span class="badge bg-purple-soft">
                                <i class="bi bi-lock-fill"></i> Khusus Undangan
                            </span>
                        @else
                            <span class="badge bg-blue-soft">
                                <i class="bi bi-globe"></i> Public
                            </span>
                        @endif

                        <span class="badge {{ $form->status == 'publish' ? 'bg-success-soft' : 'bg-secondary-soft' }}">
                            @if($form->status == 'publish')
                                <i class="bi bi-check-circle-fill"></i>
                            @else
                                <i class="bi bi-dash-circle-fill"></i>
                            @endif
                            {{ ucfirst($form->status) }}
                        </span>
                    </div>
                </div>

                {{-- Countdown / Info Waktu --}}
                <div class="bg-white px-4 py-3 rounded-4 border d-flex align-items-center gap-3 shadow-sm">
                    <div class="text-end">
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Batas Waktu</div>
                        <div class="fw-bold text-danger fs-6">
                            {{ $form->waktu_selesai->format('d M Y, H:i') }}
                        </div>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row g-5">

                {{-- Kolom Kiri: Deskripsi & Konten --}}
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="bg-blue-soft text-primary rounded px-2 py-1">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <h5 class="fw-bold text-dark m-0">Instruksi & Soal</h5>
                    </div>

                    <div class="description-content">
                        @if($form->deskripsi)
                            {!! $form->deskripsi !!}
                        @else
                            <div class="text-center py-5 bg-light rounded-3 border border-dashed">
                                <i class="bi bi-info-circle text-muted fs-4 mb-2 d-block"></i>
                                <span class="text-muted">Tidak ada deskripsi atau instruksi khusus.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kolom Kanan: Detail Informasi --}}
                <div class="col-lg-4">
                    <div class="info-box sticky-top" style="top: 100px; z-index: 1;">
                        <h6 class="fw-bold mb-4 text-dark border-bottom pb-3">
                            Detail Pelaksanaan
                        </h6>

                        <div class="info-item">
                            <div class="info-icon text-primary bg-primary bg-opacity-10">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Waktu Mulai</div>
                                <div class="info-value">
                                    {{ $form->waktu_mulai->format('d F Y') }}
                                    <span class="text-muted small fw-normal">Pukul {{ $form->waktu_mulai->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon text-danger bg-danger bg-opacity-10">
                                <i class="bi bi-calendar-x"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Batas Akhir</div>
                                <div class="info-value">
                                    {{ $form->waktu_selesai->format('d F Y') }}
                                    <span class="text-muted small fw-normal">Pukul {{ $form->waktu_selesai->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex gap-3 mt-4 mb-0 border-0 bg-warning bg-opacity-10" role="alert">
                            <i class="bi bi-exclamation-circle-fill text-warning mt-1"></i>
                            <div class="small text-dark lh-sm">
                                <strong>Penting:</strong><br>
                                Pastikan koneksi internet Anda stabil sebelum mengerjakan ujian ini.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
