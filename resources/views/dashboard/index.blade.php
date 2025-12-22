@extends('layouts.app')

@section('title', 'Dashboard Perawat')

@section('content')
<div class="container-fluid py-4 bg-light-subtle">

    {{-- 1. HEADER SECTION (Minimalist Welcome) --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold text-dark mb-1">Halo, {{ $profile->nama_lengkap ?? explode(' ', $user->name)[0] }}! 👋</h4>
                <p class="text-muted small mb-0">Selamat datang kembali di panel profesional Anda.</p>
            </div>
            <div>
                <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill fw-normal shadow-sm">
                    <i class="bi bi-calendar-event me-2"></i> {{ now()->format('d F Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ALERT: HANYA MUNCUL JIKA KRITIKAL --}}
    @if(count($warnings) > 0)
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4 rounded-3" role="alert">
        <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle me-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div>
            <strong class="d-block text-danger">Tindakan Diperlukan</strong>
            <span class="small text-muted">Dokumen berikut mendekati kadaluwarsa: {{ implode(', ', $warnings) }}</span>
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI (MAIN CONTENT) --}}
        <div class="col-lg-8">
            
            {{-- 2. QUICK STATS (Clean Cards) --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-primary-subtle text-primary me-3">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $counts['pendidikan'] }}</h5>
                                <span class="text-muted small">Riwayat Pendidikan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-success-subtle text-success me-3">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $counts['pelatihan'] }}</h5>
                                <span class="text-muted small">Sertifikat Pelatihan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-warning-subtle text-warning me-3">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $counts['pekerjaan'] }}</h5>
                                <span class="text-muted small">Pengalaman Kerja</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. PROGRESS DRH (Clean List) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0">Kelengkapan Data Diri (DRH)</h6>
                        <p class="text-muted extra-small mb-0">Lengkapi data ini untuk mengajukan sertifikasi.</p>
                    </div>
                    <div class="text-end">
                        <h4 class="fw-bold text-primary mb-0">{{ $progressPercent }}%</h4>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    {{-- Progress Line --}}
                    <div class="progress bg-light mb-4" style="height: 6px; border-radius: 10px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercent }}%; border-radius: 10px;"></div>
                    </div>

                    <div class="row g-3">
                        @foreach($sections as $sec)
<div class="col-md-6">
    <div class="d-flex align-items-center p-3 rounded-3 border h-100 section-item {{ $sec['status'] ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'bg-white' }}">
        <div class="me-3">
            @if($sec['status'])
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
            @else
                <div class="rounded-circle border border-2 border-secondary d-flex align-items-center justify-content-center text-secondary" style="width: 24px; height: 24px; font-size: 10px;">
                    <i class="bi bi-circle-fill text-white"></i>
                </div>
            @endif
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-0 fw-bold small text-dark">{{ $sec['nama'] }}</h6>
            <small class="{{ $sec['status'] ? 'text-success' : 'text-muted' }}" style="font-size: 11px;">
                {{ $sec['status'] ? 'Lengkap' : ($sec['wajib'] ? 'Wajib Diisi' : 'Opsional') }}
            </small>
        </div>
        @if(!$sec['status'])
            @php
                $routes = [
                    'bio'   => route('perawat.identitas.edit'),
                    'edu'   => route('perawat.pendidikan.index'),
                    'job'   => route('perawat.pekerjaan.index'),
                    'train' => route('perawat.pelatihan.index'),
                    'fam'   => route('perawat.keluarga.index'),
                    'doc'   => route('perawat.str.index'),
                ];
            @endphp
            <a href="{{ $routes[$sec['id']] ?? '#' }}" class="btn btn-sm btn-light border text-primary rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                <i class="bi bi-arrow-right"></i>
            </a>
        @endif
    </div>
</div>
@endforeach

                    </div>
                </div>
            </div>

            {{-- 4. CHART PORTFOLIO (Simple) --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-1">Visualisasi Portfolio</h6>
                            <p class="text-muted extra-small mb-3">Sebaran data kompetensi yang Anda miliki.</p>
                            <div style="height: 180px;">
                                <canvas id="cleanChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4 text-center border-start">
                            <h2 class="fw-bold mb-0 text-dark">{{ array_sum($chartData['data']) }}</h2>
                            <p class="text-muted small mb-0">Total Item Data</p>
                            <hr class="w-25 mx-auto my-3 text-muted">
                            <a href="{{ route('perawat.data.lengkap') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (SIDEBAR) --}}
        <div class="col-lg-4">
            
            {{-- 1. PROFILE CARD (Clean Vertical) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 text-center overflow-hidden">
                <div class="card-body p-4">
                    <div class="mb-3 position-relative d-inline-block">
                        @if($profile && $profile->foto_3x4)
                            <img src="{{ asset('storage/'.$profile->foto_3x4) }}" class="rounded-circle shadow-sm" width="100" height="100" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto text-secondary display-6 fw-bold" style="width: 100px; height: 100px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle"></span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $profile->nama_lengkap ?? $user->name }}</h5>
                    <p class="text-muted small mb-3">{{ $profile->nik ?? 'NIK Belum Lengkap' }}</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('perawat.drh') }}" class="btn btn-light border btn-sm">Edit Profil</a>
                    </div>
                </div>
            </div>

           <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden position-relative" 
     style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); transition: all 0.3s ease;">
    
    {{-- Efek Glow Halus (Opsional, agar tidak flat) --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-0" 
         style="background: radial-gradient(circle at top right, rgba(255,255,255,0.15), transparent 60%);"></div>

 <div class="card-body p-4 position-relative z-1">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <h6 class="fw-bold text-white-50 text-uppercase" style="font-size: 11px; letter-spacing: 1.5px;">
            Status Pengajuan
        </h6>
        
        {{-- Indikator titik berkedip untuk SEMUA status "sedang proses" --}}
        @if($latestPengajuan && in_array($latestPengajuan->status, ['pending', 'method_selected', 'exam_passed', 'interview_scheduled']))
            <span class="position-relative d-flex h-10 w-10">
              <span class="animate-ping position-absolute d-inline-flex h-100 w-100 rounded-circle bg-warning opacity-75"></span>
              <span class="position-relative d-inline-flex rounded-circle h-10 w-10 bg-warning" style="width:10px; height:10px;"></span>
            </span>
        @endif
    </div>

    @if($latestPengajuan)
        <h3 class="fw-bold mb-2">{{ ucfirst($latestPengajuan->tipe_sertifikat) }}</h3>
        
        <div class="d-flex align-items-center mb-4 flex-wrap gap-2">
            
            {{-- 1. Status DISETUJUI --}}
            @if($latestPengajuan->status == 'disetujui')
                <span class="badge bg-white text-success border border-white px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                </span>

            {{-- 2. Status DITOLAK --}}
            @elseif(in_array($latestPengajuan->status, ['ditolak', 'gagal']))
                <span class="badge bg-white text-danger border border-white px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                </span>

            {{-- 3. Status PROSES (Pending, Method, Exam, Interview) --}}
            @else
                <span class="badge bg-white text-warning border border-white px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-hourglass-split me-1"></i> 
                    @switch($latestPengajuan->status)
                        @case('pending')
                            Menunggu Verifikasi
                            @break
                        @case('method_selected')
                            Proses Sertifikasi
                            @break
                        @case('exam_passed')
                            Lulus Ujian Tulis
                            @break
                        @case('interview_scheduled')
                            Wawancara Dijadwalkan
                            @break
                        @default
                            Sedang Diproses
                    @endswitch
                </span>
            @endif

            <span class="ms-2 small text-white-50">
                {{ $latestPengajuan->created_at->format('d M Y') }}
            </span>
        </div>

        <a href="{{ route('perawat.pengajuan.index') }}" 
           class="btn btn-sm btn-outline-light w-100 rounded-pill py-2 fw-semibold"
           style="border-color: rgba(255,255,255,0.4); backdrop-filter: blur(4px);">
            Lihat Detail Pengajuan
        </a>

    @else
        {{-- Tampilan Kosong (Empty State) --}}
        <div class="text-center py-3">
            <h5 class="fw-bold mb-2">Belum Ada Pengajuan</h5>
            <p class="text-white-50 small mb-4">Mulai proses sertifikasi atau perpanjangan lisensi Anda sekarang.</p>
            
            <a href="{{ route('perawat.pengajuan.index') }}" 
               class="btn btn-white text-primary w-100 fw-bold rounded-pill shadow-sm py-2">
               + Buat Pengajuan Baru
            </a>
        </div>
    @endif
</div>
</div>

            {{-- 3. DOKUMEN LEGALITAS (Clean List) --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Legalitas Dokumen</h6>
                </div>
                <div class="card-body p-4">
                    {{-- STR --}}
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="rounded p-2 me-3 {{ $legalitas['str']['data'] ? ($legalitas['str']['status'] == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') : 'bg-light text-muted' }}">
                            <i class="bi bi-card-heading fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold small">STR (Registrasi)</h6>
                            <small class="text-muted" style="font-size: 11px;">
                                {{ $legalitas['str']['data'] ? 'Exp: '.\Carbon\Carbon::parse($legalitas['str']['data']->tgl_expired)->format('d M Y') : 'Belum diupload' }}
                            </small>
                        </div>
                        <span class="badge {{ $legalitas['str']['data'] ? ($legalitas['str']['status'] == 'active' ? 'bg-success' : 'bg-danger') : 'bg-secondary' }} rounded-pill" style="font-size: 10px;">
                            {{ $legalitas['str']['msg'] }}
                        </span>
                    </div>

                    {{-- SIP --}}
                    <div class="d-flex align-items-center">
                        <div class="rounded p-2 me-3 {{ $legalitas['sip']['data'] ? ($legalitas['sip']['status'] == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') : 'bg-light text-muted' }}">
                            <i class="bi bi-file-medical fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold small">SIP (Praktik)</h6>
                            <small class="text-muted" style="font-size: 11px;">
                                {{ $legalitas['sip']['data'] ? 'Exp: '.\Carbon\Carbon::parse($legalitas['sip']['data']->tgl_expired)->format('d M Y') : 'Belum diupload' }}
                            </small>
                        </div>
                        <span class="badge {{ $legalitas['sip']['data'] ? ($legalitas['sip']['status'] == 'active' ? 'bg-success' : 'bg-danger') : 'bg-secondary' }} rounded-pill" style="font-size: 10px;">
                            {{ $legalitas['sip']['msg'] }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling khusus agar lebih clean */
    body {
        background-color: #f8f9fa; /* Light Gray Background */
    }
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .icon-square {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .extra-small {
        font-size: 0.75rem;
    }
    .section-item {
        transition: background-color 0.2s;
    }
    .section-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('cleanChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar', // Bar lebih mudah dibaca untuk clean UI
        data: {
            labels: ['Pend.', 'Latih', 'Kerja', 'Org', 'Jasa'],
            datasets: [{
                label: 'Jumlah Data',
                data: {!! json_encode($chartData['data']) !!},
                backgroundColor: '#0d6efd',
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4], drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush