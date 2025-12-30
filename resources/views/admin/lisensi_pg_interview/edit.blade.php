@extends('layouts.app')

@section('title', 'Edit Lisensi (PG + Wawancara)')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --input-border: #cbd5e1;
            /* Warna Tema: Biru Laut (Konsisten dengan Index/Create PG) */
            --accent-color: #0ea5e9;
            --accent-hover: #0284c7;
            --accent-light: #e0f2fe;
            --accent-border: #bae6fd;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 14px;
        }

        /* --- Header Area --- */
        .page-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        /* --- Form Card --- */
        .form-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        /* --- Inputs Styling --- */
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .required-star {
            color: #ef4444;
            font-size: 12px;
            vertical-align: top;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--input-border);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
            line-height: 1.5;
            height: auto;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
            /* Biru Focus */
            outline: none;
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid var(--input-border);
            border-right: none;
            color: var(--text-gray);
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        /* --- Choices JS --- */
        .choices__inner {
            background-color: #fff;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            min-height: 38px;
            padding: 4px 8px !important;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .choices.is-focused .choices__inner {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }

        .choices__list--single {
            padding: 0;
        }

        /* --- Metode Wrapper --- */
        .metode-wrapper {
            background-color: var(--accent-light);
            border: 1px solid var(--accent-border);
            border-left: 4px solid var(--accent-color);
            border-radius: 8px;
            padding: 16px;
        }

        /* --- Buttons --- */
        .btn-submit-edit {
            background-color: var(--accent-color);
            color: white;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2);
            transition: all 0.2s;
        }

        .btn-submit-edit:hover {
            background-color: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(14, 165, 233, 0.3);
            color: white;
        }

        .btn-back {
            background: white;
            border: 1px solid #cbd5e1;
            color: var(--text-gray);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: #f1f5f9;
            color: var(--text-dark);
            border-color: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">

                {{-- Header --}}
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Edit Lisensi (PG + Wawancara)</h1>
                        <p class="text-muted small mb-0">Update informasi lisensi metode gabungan.</p>
                    </div>
                    {{-- ROUTE BACK SPESIFIK --}}
                    <a href="{{ route('admin.lisensi_pg_interview.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="form-card">
                    @if ($errors->any())
                        <div
                            class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mb-4 rounded-2 py-2 px-3 small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ROUTE UPDATE SPESIFIK --}}
                    <form action="{{ route('admin.lisensi_pg_interview.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            {{-- 1. Aturan Perpanjangan (Readonly Badge) --}}
                            <div class="col-12">
                                <div class="metode-wrapper">
                                    <div class="d-flex gap-3 align-items-center">
                                        <i class="bi bi-file-earmark-text fs-5" style="color: var(--accent-color);"></i>
                                        <div class="flex-grow-1">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <label class="form-label text-dark mb-0">Metode Perpanjangan</label>
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        Mode edit dikunci sesuai tipe lisensi.
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    {{-- INPUT HIDDEN --}}
                                                    <input type="hidden" name="metode_perpanjangan" value="pg_interview">
                                                    <span class="badge"
                                                        style="background-color: var(--accent-color); font-size: 12px; padding: 6px 12px;">
                                                        PG + Wawancara
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Pemilik Lisensi --}}
                            <div class="col-12">
                                <label class="form-label">Pemilik Lisensi <span class="required-star">*</span></label>
                                <select name="user_id" id="choice-user-single" class="form-select" required>
                                    <option value="">Pilih Perawat...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id', $data->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->unit_kerja ?? 'Unit Tidak Ada' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <hr class="border-light m-0">
                            </div>

                            {{-- 3. Detail Bidang & KFK (MULTI SELECT EDIT) --}}
                            <div class="col-md-6">
                                <label class="form-label">KFK <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                    <input type="text" name="bidang" class="form-control"
                                        value="{{ old('bidang', $data->bidang) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenjang KFK (PK) <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-bar-chart-steps"></i></span>
                                    <select name="kfk[]" id="choice-kfk-edit" class="form-select" multiple required>
                                        <option value="">Pilih Jenjang KFK...</option>
                                        @php
                                            $kfks = [
                                                // --- BIDAN ---
                                                'Bidan Pra BK',
                                                'Bidan BK 1',
                                                'Bidan BK 1.5',
                                                'Bidan BK 2',
                                                'Bidan BK 2.5',
                                                'Bidan BK 3',
                                                'Bidan BK 3.5',
                                                'Bidan BK 4',
                                                'Bidan BK 4.5',
                                                'Bidan BK 5',

                                                // --- PERAWAT UMUM ---
                                                'Perawat Pra PK',
                                                'Perawat PK 1',
                                                'Perawat PK 1.5',
                                                'Perawat PK 2',
                                                'Perawat PK 2.5',
                                                'Perawat PK 3',
                                                'Perawat PK 3.5',
                                                'Perawat PK 4',
                                                'Perawat PK 4.5',
                                                'Perawat PK 5',

                                                // --- KEPERAWATAN KRITIS (ICU) ---
                                                'Keperawatan Kritis ICU PK 2',
                                                'Keperawatan Kritis ICU PK 2.5',
                                                'Keperawatan Kritis ICU PK 3',
                                                'Keperawatan Kritis ICU PK 3.5',
                                                'Keperawatan Kritis ICU PK 4',
                                                'Keperawatan Kritis ICU PK 4.5',
                                                'Keperawatan Kritis ICU PK 5',

                                                // --- KEPERAWATAN KRITIS (ICVCU) ---
                                                'Keperawatan Kritis ICVCU PK 2',
                                                'Keperawatan Kritis ICVCU PK 2.5',
                                                'Keperawatan Kritis ICVCU PK 3',
                                                'Keperawatan Kritis ICVCU PK 3.5',
                                                'Keperawatan Kritis ICVCU PK 4',
                                                'Keperawatan Kritis ICVCU PK 4.5',
                                                'Keperawatan Kritis ICVCU PK 5',

                                                // --- KEPERAWATAN KRITIS (Gawat Darurat) ---
                                                'Keperawatan Kritis Gawat Darurat PK 2',
                                                'Keperawatan Kritis Gawat Darurat PK 2.5',
                                                'Keperawatan Kritis Gawat Darurat PK 3',
                                                'Keperawatan Kritis Gawat Darurat PK 3.5',
                                                'Keperawatan Kritis Gawat Darurat PK 4',
                                                'Keperawatan Kritis Gawat Darurat PK 4.5',
                                                'Keperawatan Kritis Gawat Darurat PK 5',

                                                // --- KEPERAWATAN KRITIS (Anestesi) ---
                                                'Keperawatan Kritis Anestesi PK 2',
                                                'Keperawatan Kritis Anestesi PK 2.5',
                                                'Keperawatan Kritis Anestesi PK 3',
                                                'Keperawatan Kritis Anestesi PK 3.5',
                                                'Keperawatan Kritis Anestesi PK 4',
                                                'Keperawatan Kritis Anestesi PK 4.5',
                                                'Keperawatan Kritis Anestesi PK 5',

                                                // --- KEPERAWATAN ANAK (PICU) ---
                                                'Keperawatan Anak PICU PK 2',
                                                'Keperawatan Anak PICU PK 2.5',
                                                'Keperawatan Anak PICU PK 3',
                                                'Keperawatan Anak PICU PK 3.5',
                                                'Keperawatan Anak PICU PK 4',
                                                'Keperawatan Anak PICU PK 4.5',
                                                'Keperawatan Anak PICU PK 5',

                                                // --- KEPERAWATAN ANAK (NICU) ---
                                                'Keperawatan Anak NICU PK 2',
                                                'Keperawatan Anak NICU PK 2.5',
                                                'Keperawatan Anak NICU PK 3',
                                                'Keperawatan Anak NICU PK 3.5',
                                                'Keperawatan Anak NICU PK 4',
                                                'Keperawatan Anak NICU PK 4.5',
                                                'Keperawatan Anak NICU PK 5',

                                                // --- KEPERAWATAN ANAK (Neonatus) ---
                                                'Keperawatan Anak Neonatus PK 2',
                                                'Keperawatan Anak Neonatus PK 2.5',
                                                'Keperawatan Anak Neonatus PK 3',
                                                'Keperawatan Anak Neonatus PK 3.5',
                                                'Keperawatan Anak Neonatus PK 4',
                                                'Keperawatan Anak Neonatus PK 4.5',
                                                'Keperawatan Anak Neonatus PK 5',

                                                // --- KEPERAWATAN ANAK (Pediatri) ---
                                                'Keperawatan Anak Pediatri PK 2',
                                                'Keperawatan Anak Pediatri PK 2.5',
                                                'Keperawatan Anak Pediatri PK 3',
                                                'Keperawatan Anak Pediatri PK 3.5',
                                                'Keperawatan Anak Pediatri PK 4',
                                                'Keperawatan Anak Pediatri PK 4.5',
                                                'Keperawatan Anak Pediatri PK 5',

                                                // --- KMB (Interna) ---
                                                'Keperawatan Medikal Bedah Interna PK 2',
                                                'Keperawatan Medikal Bedah Interna PK 2.5',
                                                'Keperawatan Medikal Bedah Interna PK 3',
                                                'Keperawatan Medikal Bedah Interna PK 3.5',
                                                'Keperawatan Medikal Bedah Interna PK 4',
                                                'Keperawatan Medikal Bedah Interna PK 4.5',
                                                'Keperawatan Medikal Bedah Interna PK 5',

                                                // --- KMB (Bedah) ---
                                                'Keperawatan Medikal Bedah Bedah PK 2',
                                                'Keperawatan Medikal Bedah Bedah PK 2.5',
                                                'Keperawatan Medikal Bedah Bedah PK 3',
                                                'Keperawatan Medikal Bedah Bedah PK 3.5',
                                                'Keperawatan Medikal Bedah Bedah PK 4',
                                                'Keperawatan Medikal Bedah Bedah PK 4.5',
                                                'Keperawatan Medikal Bedah Bedah PK 5',

                                                // --- KMB (Kamar Operasi) ---
                                                'Keperawatan Medikal Bedah Kamar Operasi PK 2',
                                                'Keperawatan Medikal Bedah Kamar Operasi PK 2.5',
                                                'Keperawatan Medikal Bedah Kamar Operasi PK 3',
                                                'Keperawatan Medikal Bedah Kamar Operasi PK 3.5',
                                                'Keperawatan Medikal Bedah Kamar Operasi PK 4',
                                                'Keperawatan Medikal Bedah Kamar Operasi PK 4.5',
                                                'Keperawatan Medikal Bedah Kamar Operasi PK 5',

                                                // --- KMB (Isolasi) ---
                                                'Keperawatan Medikal Bedah Isolasi PK 2',
                                                'Keperawatan Medikal Bedah Isolasi PK 2.5',
                                                'Keperawatan Medikal Bedah Isolasi PK 3',
                                                'Keperawatan Medikal Bedah Isolasi PK 3.5',
                                                'Keperawatan Medikal Bedah Isolasi PK 4',
                                                'Keperawatan Medikal Bedah Isolasi PK 4.5',
                                                'Keperawatan Medikal Bedah Isolasi PK 5',
                                            ];

                                            // Logic Ambil Data Lama
                                            $currentKfk = old('kfk', $data->kfk);
                                            if (is_string($currentKfk)) {
                                                $decoded = json_decode($currentKfk, true);
                                                $currentKfk = is_array($decoded) ? $decoded : [];
                                            }
                                            $kfkCollection = collect($currentKfk);
                                        @endphp

                                        @foreach ($kfks as $kfk)
                                            <option value="{{ $kfk }}"
                                                {{ $kfkCollection->contains($kfk) ? 'selected' : '' }}>
                                                {{ $kfk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- 4. Tanggal Pelaksanaan --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="tgl_mulai" class="form-control"
                                        value="{{ old('tgl_mulai', $data->tgl_mulai) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai Diselenggarakan <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tgl_diselenggarakan" class="form-control"
                                        value="{{ old('tgl_diselenggarakan', $data->tgl_diselenggarakan) }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="border-light m-0">
                            </div>

                            {{-- 5. Identitas Lisensi --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Lisensi <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" name="nama" class="form-control"
                                        value="{{ old('nama', $data->nama) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Lembaga Penerbit <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="lembaga" class="form-control"
                                        value="{{ old('lembaga', $data->lembaga) }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nomor Lisensi <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="nomor" class="form-control font-monospace"
                                        value="{{ old('nomor', $data->nomor) }}" required>
                                </div>
                                <div class="text-end text-muted fst-italic mt-1" style="font-size: 11px;">Nomor otomatis,
                                    edit jika perlu.</div>
                            </div>

                            {{-- 6. Tanggal Masa Berlaku --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Terbit <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tgl_terbit" class="form-control"
                                        value="{{ old('tgl_terbit', $data->tgl_terbit) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Expired <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-x"></i></span>
                                    <input type="date" name="tgl_expired" class="form-control"
                                        value="{{ old('tgl_expired', $data->tgl_expired) }}" required>
                                </div>
                            </div>

                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-4 pt-2">
                            <button type="submit" class="btn-submit-edit">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Choice untuk Pemilik Lisensi
            const elementUser = document.getElementById('choice-user-single');
            new Choices(elementUser, {
                searchEnabled: true,
                placeholderValue: 'Cari perawat...',
                noResultsText: 'Tidak ditemukan',
                itemSelectText: '',
                shouldSort: false,
                classNames: {
                    containerInner: 'choices__inner',
                    input: 'choices__input',
                }
            });

            // 2. Inisialisasi Choice untuk KFK
            const elementKfk = document.getElementById('choice-kfk-edit');
            new Choices(elementKfk, {
                removeItemButton: true,
                searchEnabled: false,
                placeholderValue: 'Pilih Jenjang KFK...',
                itemSelectText: '',
                shouldSort: false,
                classNames: {
                    containerInner: 'choices__inner',
                    input: 'choices__input',
                }
            });
        });
    </script>
@endpush
