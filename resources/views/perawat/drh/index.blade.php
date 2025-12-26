@extends('layouts.app')

@section('title', 'DRH Perawat – DIKSERA')

@section('content')
    <div class="container-fluid py-3">

        {{-- SECTION 1: HEADER UTAMA & FOTO --}}
        <div class="dash-card p-4 mb-3">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                {{-- Foto Profil --}}
                <div class="flex-shrink-0">
                    <div class="hero-icon-circle" style="width:100px;height:100px;">
                        @if (!empty($profile->foto_3x4))
                            <img src="{{ asset('storage/' . $profile->foto_3x4) }}" class="hero-icon-img"
                                style="object-fit: cover" alt="Foto Profil">
                        @else
                            <img src="{{ asset('icon.png') }}" class="hero-icon-img" alt="Default Avatar">
                        @endif
                    </div>
                </div>
                {{-- Nama & Info Utama --}}
                <div class="flex-grow-1 text-center text-md-start">
                    <h4 class="fw-bold mb-1">{{ $profile->nama_lengkap ?? $user->name }}</h4>
                    <div class="text-muted mb-2">
                        {{ $profile->jabatan ?? 'Jabatan belum diisi' }}
                        @if ($profile->nip ?? 'NIP belum diisi')
                            | NIP. {{ $profile->nip ?? 'NIP belum diisi' }}
                        @endif
                    </div>
                    <div class="badge bg-primary bg-opacity-10 text-primary">
                        {{ $profile->pangkat ?? 'Pangkat (-)' }} / {{ $profile->golongan ?? 'Gol (-)' }}
                    </div>
                </div>
                {{-- Tombol Action --}}
                <div class="flex-shrink-0 d-flex gap-2">
                    {{-- BUTTON BARU: DATA LENGKAP --}}
                    <a href="{{ route('perawat.data.lengkap') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text me-1"></i> Data Lengkap
                    </a>
                    {{-- BUTTON EDIT IDENTITAS --}}
                    <a href="{{ route('perawat.identitas.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> Edit Identitas
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{-- SECTION 2: DATA PRIBADI & KEPEGAWAIAN --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        <i class="bi bi-person-lines-fill me-2"></i>Data Pribadi & Kepegawaian
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-borderless table-sm small align-middle mb-0">
                            <tr>
                                <td class="text-muted" width="35%">NIK</td>
                                <td class="fw-bold">: {{ $profile->nik ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIP</td>
                                <td class="fw-bold">: {{ $profile->nip ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIRP</td>
                                <td class="fw-bold">: {{ $profile->nirp ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tipe Perawat</td>
                                <td class="fw-bold">: {{ $profile->type_perawat ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tempat, Tgl Lahir</td>
                                <td class="fw-bold">
                                    :
                                    {{-- 1. Use optional() so it doesn't crash if $profile is missing --}}
                                    {{ optional($profile)->tempat_lahir ?? '—' }},

                                    {{-- 2. Check if profile AND tanggal_lahir exist before formatting --}}
                                    @if (optional($profile)->tanggal_lahir)
                                        {{ date('d-m-Y', strtotime($profile->tanggal_lahir)) }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin</td>
                                <td class="fw-bold">:
                                    {{ optional($profile)->jenis_kelamin == 'L' ? 'Laki-laki' : (optional($profile)->jenis_kelamin == 'P' ? 'Perempuan' : '—') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Agama</td>
                                <td class="fw-bold">: {{ $profile->agama ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Aliran Kepercayaan</td>
                                <td class="fw-bold">: {{ $profile->aliran_kepercayaan ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status Perkawinan</td>
                                <td class="fw-bold">: {{ $profile->status_perkawinan ?? '—' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: FISIK, KONTAK & LAINNYA --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    {{-- Keterangan Badan --}}
                    <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        <i class="bi bi-activity me-2"></i>Keterangan Fisik & Ciri
                    </h6>
                    <div class="row g-2 small mb-4">
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Gol. Darah</span>
                            <strong>{{ $profile->golongan_darah ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Tinggi (cm)</span>
                            <strong>{{ $profile->tinggi_badan ?? '-' }} cm</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Berat (kg)</span>
                            <strong>{{ $profile->berat_badan ?? '-' }} kg</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Warna Kulit</span>
                            <strong>{{ $profile->warna_kulit ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mt-3">
                            <span class="text-muted d-block">Rambut</span>
                            <strong>{{ $profile->rambut ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mt-3">
                            <span class="text-muted d-block">Bentuk Muka</span>
                            <strong>{{ $profile->bentuk_muka ?? '-' }}</strong>
                        </div>
                        <div class="col-12 col-md-6 mt-3">
                            <span class="text-muted d-block">Ciri Khas / Cacat</span>
                            <strong>{{ $profile->ciri_khas ?? '-' }} / {{ $profile->cacat_tubuh ?? '-' }}</strong>
                        </div>
                    </div>

                    {{-- Kontak & Alamat --}}
                    <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        <i class="bi bi-geo-alt-fill me-2"></i>Kontak & Alamat
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm small align-middle mb-0">
                            <tr>
                                <td class="text-muted" width="35%">No. HP (WA)</td>
                                <td class="fw-bold text-success">: {{ $profile->no_hp ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kota Domisili</td>
                                <td class="fw-bold">: {{ $profile->kota ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat Lengkap</td>
                                <td class="fw-bold">: {{ $profile->alamat ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Hobi</td>
                                <td class="fw-bold">: {{ $profile->hobby ?? '—' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: PENDIDIKAN (Existing Code) --}}
        <div class="dash-card p-3 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-mortarboard-fill me-2"></i>Riwayat Pendidikan</h6>
                <a href="{{ route('perawat.pendidikan.index') }}" class="btn btn-sm btn-outline-primary">
                    Kelola Pendidikan
                </a>
            </div>

            @if ($pendidikan->isEmpty())
                <div class="alert alert-light text-center small text-muted mb-0 border-0">
                    Belum ada data pendidikan. Silakan tambahkan minimal satu riwayat pendidikan formal.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Jenjang</th>
                                <th>Nama Institusi</th>
                                <th>Akreditasi</th>
                                <th>Tempat</th>
                                <th>Tahun Lulus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendidikan as $i => $row)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td><span class="badge bg-secondary">{{ $row->jenjang }}</span></td>
                                    <td class="fw-semibold">{{ $row->nama_institusi }}</td>
                                    <td>{{ $row->akreditasi ?? '—' }}</td>
                                    <td>{{ $row->tempat ?? '—' }}</td>
                                    <td class="text-center">{{ $row->tahun_lulus ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .dash-card {
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(209, 213, 219, 0.8);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            backdrop-filter: blur(10px);
            transition: transform 0.2s;
        }

        .hero-icon-circle {
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #f8fafc, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 3px solid #fff;
        }

        .hero-icon-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .table-borderless td {
            padding-top: 4px;
            padding-bottom: 4px;
        }
    </style>
@endpush
