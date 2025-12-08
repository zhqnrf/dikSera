@extends('layouts.app')

@section('title', 'Data Lengkap DRH – DIKSERA')

@section('content')
<div class="container py-5">

    {{-- Header Page --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-1">Daftar Riwayat Hidup</h3>
            <p class="text-muted mb-0">Rekapitulasi seluruh data kompetensi, kualifikasi, dan riwayat perawat.</p>
        </div>
        <a href="{{ route('perawat.drh') }}" class="btn btn-outline-primary px-4 shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>

    {{-- 1. RIWAYAT PENDIDIKAN --}}
    <div class="card dash-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="section-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 ms-3 section-title">A. Riwayat Pendidikan</h6>
                </div>
                <a href="{{ route('perawat.pendidikan.index') }}" class="btn btn-sm btn-action">
                    <i class="bi bi-pencil me-1"></i> Kelola
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-custom">
                    <thead class="bg-light">
                        <tr>
                            <th width="10%" class="text-center">Jenjang</th>
                            <th width="30%">Nama Institusi</th>
                            <th width="25%">Jurusan / Peminatan</th>
                            <th width="15%" class="text-center">Tahun Lulus</th>
                            <th width="15%" class="text-center">Akreditasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendidikan as $p)
                            <tr>
                                <td class="text-center"><span class="badge bg-primary rounded-pill">{{ $p->jenjang }}</span></td>
                                <td class="fw-medium text-primary">
                                    {{ $p->nama_institusi }}
                                    <div class="text-muted small mt-1 text-dark fw-normal"><i class="bi bi-geo-alt me-1"></i>{{ $p->tempat ?? '-' }}</div>
                                </td>
                                <td>{{ $p->jurusan ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ $p->tahun_lulus }}</td>
                                <td class="text-center">{{ $p->akreditasi ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted bg-light rounded">
                                    <i class="bi bi-inbox fs-4 d-block mb-1 opacity-50"></i> Belum ada data pendidikan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 2. RIWAYAT PELATIHAN --}}
    <div class="card dash-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="section-icon">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 ms-3 section-title">B. Pelatihan / Kursus / Seminar</h6>
                </div>
                <a href="{{ route('perawat.pelatihan.index') }}" class="btn btn-sm btn-action">
                    <i class="bi bi-pencil me-1"></i> Kelola
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-custom">
                    <thead class="bg-light">
                        <tr>
                            <th width="30%">Nama Pelatihan</th>
                            <th width="25%">Penyelenggara</th>
                            <th width="15%">Tempat</th>
                            <th width="20%">Waktu Pelaksanaan</th>
                            <th width="10%">Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelatihan as $pl)
                            <tr>
                                <td class="fw-medium text-primary">{{ $pl->nama_pelatihan }}</td>
                                <td>{{ $pl->penyelenggara }}</td>
                                <td><i class="bi bi-geo-alt me-1 text-muted"></i>{{ $pl->tempat }}</td>
                                <td>
                                    <small class="d-block text-muted">Mulai: {{ $pl->tanggal_mulai ? date('d M Y', strtotime($pl->tanggal_mulai)) : '-' }}</small>
                                    <small class="d-block text-muted">Selesai: {{ $pl->tanggal_selesai ? date('d M Y', strtotime($pl->tanggal_selesai)) : '-' }}</small>
                                </td>
                                <td><span class="badge bg-light text-primary border border-primary">{{ $pl->durasi }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted bg-light rounded">
                                    <i class="bi bi-inbox fs-4 d-block mb-1 opacity-50"></i> Belum ada data pelatihan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 3. RIWAYAT PEKERJAAN --}}
    <div class="card dash-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="section-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 ms-3 section-title">C. Riwayat Pekerjaan</h6>
                </div>
                <a href="{{ route('perawat.pekerjaan.index') }}" class="btn btn-sm btn-action">
                    <i class="bi bi-pencil me-1"></i> Kelola
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-custom">
                    <thead class="bg-light">
                        <tr>
                            <th width="30%">Nama Instansi</th>
                            <th width="25%">Jabatan</th>
                            <th width="20%" class="text-center">Periode</th>
                            <th width="25%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pekerjaan as $pk)
                            <tr>
                                <td class="fw-bold text-primary">{{ $pk->nama_instansi }}</td>
                                <td>{{ $pk->jabatan }}</td>
                                <td class="text-center">
                                    <span class="badge bg-blue-subtle text-primary border border-primary-subtle">
                                        {{ $pk->tahun_mulai }} - {{ $pk->tahun_selesai ?? 'Sekarang' }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $pk->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted bg-light rounded">
                                    <i class="bi bi-inbox fs-4 d-block mb-1 opacity-50"></i> Belum ada riwayat pekerjaan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 4. TANDA JASA --}}
    <div class="card dash-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="section-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0 ms-3 section-title">D. Tanda Jasa / Penghargaan</h6>
                </div>
                <a href="{{ route('perawat.tandajasa.index') }}" class="btn btn-sm btn-action">
                    <i class="bi bi-pencil me-1"></i> Kelola
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-custom">
                    <thead class="bg-light">
                        <tr>
                            <th width="35%">Nama Penghargaan</th>
                            <th width="30%">Instansi Pemberi</th>
                            <th width="10%" class="text-center">Tahun</th>
                            <th width="25%">Nomor SK / Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tandajasa as $tj)
                            <tr>
                                <td class="fw-medium text-primary">{{ $tj->nama_penghargaan }}</td>
                                <td>{{ $tj->instansi_pemberi }}</td>
                                <td class="text-center fw-bold">{{ $tj->tahun }}</td>
                                <td>
                                    <div class="small fw-bold">{{ $tj->nomor_sk ?? '-' }}</div>
                                    <div class="small text-muted">{{ $tj->tanggal_sk ? date('d M Y', strtotime($tj->tanggal_sk)) : '' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted bg-light rounded">
                                    <i class="bi bi-inbox fs-4 d-block mb-1 opacity-50"></i> Belum ada tanda jasa
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 5. KELUARGA --}}
        <div class="col-lg-6 mb-4">
            <div class="card dash-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <div class="section-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0 ms-3 section-title">E. Data Keluarga</h6>
                        </div>
                        <a href="{{ route('perawat.keluarga.index') }}" class="btn btn-sm btn-action">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom small">
                            <thead class="bg-light">
                                <tr>
                                    <th>Hubungan</th>
                                    <th>Nama</th>
                                    <th>Lahir</th>
                                    <th>Pekerjaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($keluarga as $kel)
                                    <tr>
                                        <td><span class="badge bg-light text-primary border border-primary-subtle">{{ $kel->hubungan }}</span></td>
                                        <td class="fw-medium">{{ $kel->nama }}</td>
                                        <td>{{ $kel->tanggal_lahir ? date('d/m/Y', strtotime($kel->tanggal_lahir)) : '-' }}</td>
                                        <td>{{ $kel->pekerjaan }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3 bg-light rounded">Kosong</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. ORGANISASI --}}
        <div class="col-lg-6 mb-4">
            <div class="card dash-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <div class="section-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0 ms-3 section-title">F. Organisasi</h6>
                        </div>
                        <a href="{{ route('perawat.organisasi.index') }}" class="btn btn-sm btn-action">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom small">
                            <thead class="bg-light">
                                <tr>
                                    <th>Organisasi</th>
                                    <th>Jabatan</th>
                                    <th>Periode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organisasi as $org)
                                    <tr>
                                        <td class="fw-medium text-primary">
                                            {{ $org->nama_organisasi }}
                                            <div class="text-muted small fw-normal text-dark">{{ $org->tempat }}</div>
                                        </td>
                                        <td>{{ $org->jabatan }}</td>
                                        <td>{{ $org->tahun_mulai }} - {{ $org->tahun_selesai ?? 'Skg' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3 bg-light rounded">Kosong</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    /* 1. All Blue Variables Setup (Using Bootstrap Primary Color) */
    :root {
        --diksera-blue: #0d6efd;
        --diksera-blue-soft: #eff6ff;
        --diksera-blue-hover: #0b5ed7;
    }

    /* 2. Card Styling */
    .dash-card {
        border: 1px solid #eef2f6; /* Very subtle border */
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(13, 110, 253, 0.04); /* Blue-ish subtle shadow */
        border-left: 4px solid var(--diksera-blue); /* Consistent Blue Accent */
        background: #fff;
        transition: all 0.3s ease;
    }
    .dash-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(13, 110, 253, 0.1);
    }

    /* 3. Icons Container */
    .section-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        background-color: var(--diksera-blue-soft);
        color: var(--diksera-blue);
    }
    .section-title {
        letter-spacing: 0.3px;
        font-size: 1rem;
    }

    /* 4. Buttons */
    .btn-action {
        border-radius: 6px;
        font-size: 0.75rem;
        padding: 5px 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--diksera-blue);
        border: 1px solid #bfd3f5;
        background: white;
        transition: all 0.2s;
    }
    .btn-action:hover {
        background-color: var(--diksera-blue);
        color: white;
        border-color: var(--diksera-blue);
    }

    /* 5. Tables */
    .table-custom thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 10px;
        background-color: #f8fafc;
    }
    .table-custom tbody td {
        padding: 14px 10px;
        font-size: 0.9rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }
    /* Hover effect on rows */
    .table-hover tbody tr:hover {
        background-color: #fcfdff; /* Very light blue tint */
    }

    /* 6. Badges custom */
    .bg-blue-subtle {
        background-color: #eff6ff;
    }
</style>
@endpush
