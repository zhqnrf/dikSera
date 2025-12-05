@extends('layouts.app')

@php
    $pageTitle = 'Detail DRH Perawat';
    $pageSubtitle = 'Data lengkap Daftar Riwayat Hidup ' . $user->name;
@endphp

@section('title', 'Detail Perawat – Admin DIKSERA')

@push('styles')
    <style>
        /* Global Card Style */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
            height: 30%;
            /* Agar tinggi card seragam di grid */
        }

        /* Profile Specific */
        .profile-img-box {
            width: 140px;
            height: 180px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 600;
            color: var(--blue-main);
            background: var(--blue-soft);
        }

        /* Label & Value Typography */
        .data-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .data-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
        }

        /* Section Headers */
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--blue-main);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title::before {
            content: '';
            display: block;
            width: 4px;
            height: 16px;
            background: var(--blue-main);
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')

    {{-- Tombol Kembali --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.perawat.index') }}" class="btn btn-sm btn-outline-secondary px-3"
            style="border-radius: 8px; font-size: 12px;">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    {{-- 1. HEADER PROFIL UTAMA --}}
    <div class="content-card mb-4">
        <div class="row g-4">
            {{-- Kolom Foto --}}
            <div class="col-md-auto">
                <div class="profile-img-box">
                    @if ($profile && $profile->foto_3x4)
                        <img src="{{ asset('storage/' . $profile->foto_3x4) }}" alt="Foto">
                    @else
                        <div class="profile-avatar-fallback">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kolom Info --}}
            <div class="col-md">
                <div class="mb-3">
                    <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                    <span
                        class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-3 rounded-pill">
                        Perawat
                    </span>
                </div>

                <hr class="border-light my-3">

                <div class="row g-3">
                    {{-- Kolom Kiri Info --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="data-label">Nomor Induk Kependudukan (NIK)</div>
                            <div class="data-value font-monospace">{{ $profile->nik ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">Tempat Lahir</div>
                                <div class="data-value">{{ $profile->tempat_lahir ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="data-label">Tanggal Lahir</div>
                                <div class="data-value">
                                    {{ $profile->tanggal_lahir ? date('d M Y', strtotime($profile->tanggal_lahir)) : '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">Jenis Kelamin</div>
                                <div class="data-value">{{ $profile->jenis_kelamin ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="data-label">Agama</div>
                                <div class="data-value">{{ $profile->agama ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan Info --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="data-label">Alamat Domisili</div>
                            <div class="data-value">{{ $profile->alamat ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="data-label">Nomor HP / WhatsApp</div>
                            <div class="data-value text-success">
                                <i class="bi bi-whatsapp me-1"></i> {{ $profile->no_hp ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">Fisik (TB / BB)</div>
                                <div class="data-value">
                                    {{ $profile->tinggi_badan ?? '-' }} cm / {{ $profile->berat_badan ?? '-' }} kg
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="data-label">Golongan Darah</div>
                                <div class="data-value">{{ $profile->golongan_darah ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. DETAIL SECTIONS (Grid Layout) --}}
    <div class="row g-4">

        {{-- Kolom Kiri --}}
        <div class="col-lg-6 d-flex flex-column gap-4">
            @include('admin.perawat._section', [
                'title' => 'Riwayat Pendidikan',
                'icon' => 'bi-mortarboard',
                'data' => $pendidikan,
                'cols' => ['Jenjang' => 'jenjang', 'Institusi' => 'nama_institusi', 'Lulus' => 'tahun_lulus'],
            ])

            @include('admin.perawat._section', [
                'title' => 'Kursus / Pelatihan',
                'icon' => 'bi-journal-bookmark',
                'data' => $pelatihan,
                'cols' => [
                    'Pelatihan' => 'nama_pelatihan',
                    'Penyelenggara' => 'penyelenggara',
                    'Tahun' => 'tanggal_mulai',
                ],
            ])

            @include('admin.perawat._section', [
                'title' => 'Riwayat Keluarga',
                'icon' => 'bi-people',
                'data' => $keluarga,
                'cols' => ['Hubungan' => 'hubungan', 'Nama' => 'nama', 'Pekerjaan' => 'pekerjaan'],
            ])
        </div>

        {{-- Kolom Kanan --}}
        <div class="col-lg-6 d-flex flex-column gap-4">
            @include('admin.perawat._section', [
                'title' => 'Riwayat Pekerjaan',
                'icon' => 'bi-briefcase',
                'data' => $pekerjaan,
                'cols' => [
                    'Instansi' => 'nama_instansi',
                    'Jabatan' => 'jabatan',
                    'Mulai' => 'tahun_mulai',
                    'Selesai' => 'tahun_selesai',
                ],
            ])

            @include('admin.perawat._section', [
                'title' => 'Pengalaman Organisasi',
                'icon' => 'bi-diagram-3',
                'data' => $organisasi,
                'cols' => ['Organisasi' => 'nama_organisasi', 'Jabatan' => 'jabatan', 'Periode' => 'tahun_mulai'],
            ])

            @include('admin.perawat._section', [
                'title' => 'Tanda Jasa / Penghargaan',
                'icon' => 'bi-trophy',
                'data' => $tandajasa,
                'cols' => ['Penghargaan' => 'nama_penghargaan', 'Tahun' => 'tahun'],
            ])
        </div>

    </div>

@endsection
