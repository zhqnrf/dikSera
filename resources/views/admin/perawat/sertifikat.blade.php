@extends('layouts.app')

@php
    $pageTitle = 'Kredensialing & Sertifikasi';
    $pageSubtitle = 'Kelola data STR, SIP, dan sertifikat kompetensi milik ' . $user->name;
@endphp

@section('title', 'Detail Sertifikat – Admin DIKSERA')

@push('styles')
    <style>
        /* Card Style */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
            height: 100%;
        }

        /* Section Title */
        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--blue-main);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--blue-soft-2);
        }

        .section-title i {
            font-size: 18px;
        }

        /* Custom Table */
        .table-custom th {
            background-color: var(--blue-soft-2);
            color: var(--text-main);
            font-weight: 600;
            font-size: 12px;
            border-bottom: 2px solid #dbeafe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            vertical-align: middle;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 12px 16px;
            border-bottom: 1px solid var(--blue-soft-2);
            font-size: 13px;
            color: var(--text-main);
        }

        /* Status Badges (Soft Color) */
        .badge-soft {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-soft-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-soft-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-soft-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-soft-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        /* File Button */
        .btn-file {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            border: 1px solid var(--border-soft);
            background: #fff;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .btn-file:hover {
            border-color: var(--blue-main);
            color: var(--blue-main);
            background: var(--blue-soft);
        }

        /* Soft Action Buttons */
        .btn-verify {
            border: none;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
            line-height: 1;
        }

        .btn-verify-success {
            background: #dcfce7;
            color: #166534;
        }

        .btn-verify-success:hover,
        .btn-verify-success.active {
            background: #166534;
            color: #ffffff;
        }

        .btn-verify-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-verify-danger:hover,
        .btn-verify-danger.active {
            background: #991b1b;
            color: #ffffff;
        }

        .btn-verify-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-verify-secondary:hover,
        .btn-verify-secondary.active {
            background: #475569;
            color: #ffffff;
        }

        .verify-actions {
            display: flex;
            gap: 6px;
            margin-top: 8px;
        }
    </style>
@endpush

@section('content')

    {{-- Top Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            {{-- Breadcrumb simpel jika diperlukan --}}
            <span
                class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-3 rounded-pill mb-2">
                {{ $user->name }}
            </span>
        </div>
        <a href="{{ route('admin.perawat.show', $user->id) }}" class="btn btn-sm btn-outline-secondary px-3"
            style="border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali ke Profil
        </a>
    </div>

    <div class="row g-4">

        {{-- 1. SURAT TANDA REGISTRASI (STR) --}}
        <div class="col-12">
            <div class="content-card">
                <div class="section-title">
                    <i class="bi bi-card-heading text-success"></i>
                    Surat Tanda Registrasi (STR)
                </div>

                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nomor Registrasi</th>
                                <th>Nama Dokumen</th>
                                <th>Lembaga Penerbit</th>
                                <th>Periode Berlaku</th>
                                <th>Status</th>
                                <th class="text-end">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($str as $item)
                                @include('admin.perawat._row_certificate', ['item' => $item])
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small fst-italic">
                                        Tidak ada data STR.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 2. SURAT IZIN PRAKTIK (SIP) --}}
        <div class="col-12">
            <div class="content-card">
                <div class="section-title">
                    <i class="bi bi-file-earmark-medical text-primary"></i>
                    Surat Izin Praktik (SIP)
                </div>

                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nomor SIP</th>
                                <th>Nama Dokumen</th>
                                <th>Faskes / Lembaga</th>
                                <th>Periode Berlaku</th>
                                <th>Status</th>
                                <th class="text-end">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sip as $item)
                                @include('admin.perawat._row_certificate', ['item' => $item])
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small fst-italic">
                                        Tidak ada data SIP.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 3. LISENSI & SERTIFIKAT --}}
        <div class="col-12">
            <div class="content-card">
                <div class="section-title">
                    <i class="bi bi-award text-warning"></i>
                    Kredensialing Lainnya
                </div>

                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Nama Sertifikat</th>
                                <th>Penerbit</th>
                                <th>Periode Berlaku</th>
                                <th>Status</th>
                                <th class="text-end">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lisensi as $item)
                                @include('admin.perawat._row_certificate', ['item' => $item])
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small fst-italic">
                                        Tidak ada data Lisensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 4. DATA TAMBAHAN --}}
        <div class="col-12">
            <div class="content-card">
                <div class="section-title">
                    <i class="bi bi-folder2-open text-info"></i>
                    Data Tambahan
                </div>

                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Nomor</th>
                                <th>Nama Dokumen</th>
                                <th>Periode Berlaku</th>
                                <th>Status</th>
                                <th class="text-end">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataTambahan as $item)
                                @php
                                    // Logika status khusus data tambahan (karena mungkin null expirednya)
                                    $statusHtml = '<span class="badge-soft badge-soft-secondary">Non-Expired</span>';

                                    if ($item->tgl_expired) {
                                        $daysLeft = \Carbon\Carbon::now()->diffInDays(
                                            \Carbon\Carbon::parse($item->tgl_expired),
                                            false,
                                        );
                                        if ($daysLeft < 0) {
                                            $statusHtml =
                                                '<span class="badge-soft badge-soft-danger"><i class="bi bi-x-circle"></i> Expired</span>';
                                        } elseif ($daysLeft <= 30) {
                                            $statusHtml =
                                                '<span class="badge-soft badge-soft-warning"><i class="bi bi-exclamation-circle"></i> Sisa ' .
                                                $daysLeft .
                                                ' hari</span>';
                                        } else {
                                            $statusHtml =
                                                '<span class="badge-soft badge-soft-success"><i class="bi bi-check-circle"></i> Aktif</span>';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2">
                                            {{ strtoupper($item->jenis) }}
                                        </span>
                                    </td>
                                    <td class="font-monospace">{{ $item->nomor ?? '-' }}</td>
                                    <td class="fw-bold">{{ $item->nama }}</td>
                                    <td>
                                        @if ($item->tgl_expired)
                                            <div class="d-flex flex-column" style="font-size: 11px;">
                                                <span class="text-muted">Terbit:
                                                    {{ \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') }}</span>
                                                <span
                                                    class="{{ \Carbon\Carbon::parse($item->tgl_expired)->isPast() ? 'text-danger' : 'text-dark' }}">
                                                    Exp: {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d M Y') }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {!! $statusHtml !!}
                                        <div class="mt-2 mb-2 d-flex align-items-center gap-2">
                                            <span class="text-muted" style="font-size: 11px;">Verifikasi:</span>
                                            <span
                                                class="badge-soft badge-soft-secondary status-kelayakan-{{ $item->id }}">
                                                <span
                                                    class="fw-bold text-uppercase kelayakan-label">{{ $item->kelayakan ?? 'pending' }}</span>
                                            </span>
                                        </div>
                                        <div class="verify-actions">
                                            {{-- Form Layak --}}
                                            <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <input type="hidden" name="tipe" value="tambahan">
                                                <input type="hidden" name="kelayakan" value="layak">
                                                <button type="submit" class="btn-verify btn-verify-success"
                                                    title="Set Layak">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>

                                            {{-- Form Tidak Layak --}}
                                            <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <input type="hidden" name="tipe" value="tambahan">
                                                <input type="hidden" name="kelayakan" value="tidak_layak">
                                                <button type="submit" class="btn-verify btn-verify-danger"
                                                    title="Set Tidak Layak">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>

                                            {{-- Form Pending/Reset --}}
                                            <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <input type="hidden" name="tipe" value="tambahan">
                                                <input type="hidden" name="kelayakan" value="pending">
                                                <button type="submit" class="btn-verify btn-verify-secondary"
                                                    title="Reset ke Pending">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                            {{-- Approve Lifetime (hanya muncul jika is_lifetime dan belum approved) --}}
                                            @if ($item->is_lifetime && !$item->lifetime_approved)
                                                <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                                    <input type="hidden" name="tipe" value="tambahan">
                                                    <input type="hidden" name="kelayakan" value="layak">
                                                    <input type="hidden" name="approve_lifetime" value="1">
                                                    <button type="submit" class="btn-verify btn-verify-success"
                                                        title="Approve Lifetime">
                                                        <i class="bi bi-award"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if ($item->file_path)
                                            <a href="{{ Storage::url($item->file_path) }}" target="_blank"
                                                class="btn-file">
                                                <i class="bi bi-file-earmark-text"></i> File
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small fst-italic">
                                        Tidak ada data tambahan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
