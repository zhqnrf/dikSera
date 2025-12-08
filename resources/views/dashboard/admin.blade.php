@extends('layouts.app')

@section('title', 'Dashboard Admin – DIKSERA')

@section('content')
    <div class="container-fluid py-3">

        {{-- HERO --}}
        <div class="row g-3 align-items-stretch">
            <div class="col-lg-8">
                <div class="dash-card h-100 d-flex flex-column flex-md-row align-items-center p-3">
                    <div class="hero-icon-wrapper me-md-3 mb-3 mb-md-0">
                        <div class="hero-icon-circle">
                            <img src="{{ asset('icon.png') }}" alt="DIKSERA" class="hero-icon-img">
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h1 class="dash-title mb-1">Halo Admin 👋</h1>
                        <p class="dash-subtitle mb-2">
                            Selamat datang di <strong>DIKSERA</strong><br>
                            Panel kendali untuk monitoring, validasi, dan manajemen data perawat.
                        </p>
                        <div class="small text-muted">
                            Kelola data, verifikasi DRH, pantau aktifitas pengguna, dan modifikasi sistem di sini.
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD METRIK UTAMA --}}
            <div class="col-lg-4">
                <div class="dash-card h-100 p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Total Perawat Terdaftar</span>
                            <span class="badge bg-primary-subtle text-primary fw-semibold">
                                {{ $totalPerawat }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Menunggu Verifikasi DRH</span>
                            <span class="badge bg-warning-subtle text-warning fw-semibold">
                                {{ $pendingVerifikasi }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Total Pengguna Sistem</span>
                            <span class="badge bg-success-subtle text-success fw-semibold">
                                {{ $totalUsers }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('admin.perawat.index') }}" class="btn btn-sm btn-primary w-100 mt-3">
                        Kelola Data Perawat
                    </a>
                </div>
            </div>
        </div>

        {{-- BARIS METRIK KOMITE TAMBAHAN --}}
        <div class="row g-3 mt-1">
            {{-- CARD ELIGIBLE --}}
            <div class="col-md-3">
                <div class="dash-card p-3 h-100 bg-info-subtle border-info">
                    <span class="small text-muted d-block mb-1">Perawat Eligible Ujian</span>
                    <h4 class="mb-0 text-info fw-bold">{{ $eligibleCount }}</h4>
                    <div class="small mt-1 text-info-emphasis">
                        <i class="bi bi-person-check"></i> Sudah diverifikasi & memenuhi syarat
                    </div>
                </div>
            </div>
            {{-- CARD LULUS FINAL --}}
            <div class="col-md-3">
                <div class="dash-card p-3 h-100 bg-success-subtle border-success">
                    <span class="small text-muted d-block mb-1">Lulus (Tersertifikasi)</span>
                    <h4 class="mb-0 text-success fw-bold">{{ $lulusFinalCount }}</h4>
                    <div class="small mt-1 text-success-emphasis">
                        <i class="bi bi-award"></i> Total perawat siap lisensi
                    </div>
                </div>
            </div>
            {{-- CARD LISENSI EXPIRED --}}
            <div class="col-md-3">
                <div class="dash-card p-3 h-100 bg-danger-subtle border-danger">
                    <span class="small text-muted d-block mb-1">Lisensi Hampir Kadaluarsa</span>
                    <h4 class="mb-0 text-danger fw-bold">{{ $almostExpired }}</h4>
                    <div class="small mt-1 text-danger-emphasis">
                        <i class="bi bi-clock-history"></i> Perlu segera perpanjangan
                    </div>
                </div>
            </div>
            {{-- BUTTON EXPORT LAPORAN --}}
            <div class="col-md-3">
                <div class="dash-card p-3 h-100 d-flex flex-column justify-content-center bg-primary-subtle border-primary">
                    <h6 class="mb-2 text-primary">Laporan & Export Data</h6>
                    <p class="small text-muted mb-3">
                        Export data komprehensif (Perawat, Status, Ujian, Wawancara).
                    </p>
                    <a href="#" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Export Laporan Final
                    </a>
                </div>
            </div>
        </div>

        {{-- ROW CHART & RINGKASAN --}}
        <div class="row g-3 mt-1">
            {{-- CHART KELULUSAN PER BULAN --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <h6 class="mb-3">Chart Kelulusan Ujian Per Bulan</h6>

                    {{-- Area untuk Chart.js/ApexCharts --}}
                    <div style="height: 300px;">
                        <canvas id="monthlyPassChart"></canvas>
                    </div>

                    <div class="small text-muted mt-2">
                        Total: {{ array_sum($monthlyPassRates) }} Kelulusan dalam 6 bulan terakhir.
                    </div>
                </div>
            </div>

            {{-- RINGKASAN VERIFIKASI --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Ringkasan Verifikasi DRH</h6>
                        <span class="badge bg-light text-primary border">
                            Monitoring
                        </span>
                    </div>

                    <div class="small">
                        <div class="mb-1 d-flex justify-content-between">
                            <span class="label">Belum Diperiksa</span>
                            <span class="value">{{ $pendingVerifikasi }}</span>
                        </div>

                        <div class="mb-1 d-flex justify-content-between">
                            <span class="label">Sedang Diproses</span>
                            <span class="value">{{ $onProgress }}</span>
                        </div>

                        <div class="mb-1 d-flex justify-content-between">
                            <span class="label">Selesai Diverifikasi</span>
                            <span class="value">{{ $verified }}</span>
                        </div>

                        <hr>

                        <div class="mb-1 d-flex justify-content-between fw-bold">
                            <span class="label">Total Perawat</span>
                            <span class="value">{{ $totalPerawat }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW AKTIVITAS & MODUL --}}
        <div class="row g-3 mt-1">

            {{-- LOG & AKTIVITAS PENGGUNA --}}
            <div class="col-md-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Aktivitas Terbaru</h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            {{ $recentActivities->count() }} Log Terakhir
                        </span>
                    </div>

                    <ul class="small mb-0 ps-3">
                        @forelse($recentActivities as $act)
                            <li class="mb-1">
                                <strong>{{ $act->user->name }}</strong> —
                                {{ $act->description }}
                                <br>
                                <span class="text-muted">{{ $act->created_at->diffForHumans() }}</span>
                            </li>
                        @empty
                            <li class="text-muted">Belum ada aktivitas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- TABLE STATUS MODUL --}}
            <div class="col-md-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Status Modul & Fitur</h6>
                        <span class="small text-muted">Pantau progres implementasi modul DIKSERA.</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px;">No</th>
                                    <th>Modul</th>
                                    <th class="text-center" style="width:140px;">Status</th>
                                    <th class="text-center" style="width:120px;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modulesStatus as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $row['nama'] }}</td>
                                        <td class="text-center">
                                            @if ($row['status'] === 'ready')
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle">
                                                    Siap
                                                </span>
                                            @elseif($row['status'] === 'progress')
                                                <span
                                                    class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                    Proses
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                    Coming soon
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $row['catatan'] ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="small text-muted mt-2">
                        * Data ini dapat diatur dari konfigurasi admin.
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('styles')
    {{-- Seluruh style tetap sama --}}
    <style>
        .dash-card {
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(209, 213, 219, 0.8);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.09);
            backdrop-filter: blur(10px);
        }

        .dash-title {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
        }

        .dash-subtitle {
            font-size: 13px;
            color: #6b7280;
        }

        .hero-icon-circle {
            width: 82px;
            height: 82px;
            border-radius: 26px;
            background: radial-gradient(circle at 20% 0, #eff6ff, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 40px rgba(37, 99, 235, 0.45);
            overflow: hidden;
        }

        .hero-icon-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .label {
            display: inline-block;
            width: 150px;
            color: #6b7280;
        }

        .value {
            font-weight: 500;
            color: #111827;
        }

        @media (max-width: 767.98px) {
            .dash-title {
                font-size: 16px;
            }

            .hero-icon-circle {
                width: 70px;
                height: 70px;
            }
        }
    </style>
@endpush

@push('scripts')
    {{-- Script untuk Chart Kelulusan Bulanan --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monthlyPassChart');
            const data = {
                labels: {!! json_encode(array_keys($monthlyPassRates)) !!},
                datasets: [{
                    label: 'Jumlah Lulus Ujian',
                    data: {!! json_encode(array_values($monthlyPassRates)) !!},
                    backgroundColor: 'rgba(29, 78, 216, 0.7)',
                    borderColor: 'rgba(29, 78, 216, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            };

            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
@endpush
