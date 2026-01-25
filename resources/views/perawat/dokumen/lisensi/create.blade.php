@extends('layouts.app')

@section('title', 'Buat Lisensi Baru')

@push('styles')
    {{-- Load CSS Choices --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --input-border: #cbd5e1;
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
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
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

        /* --- Choices JS Custom Styling --- */
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

        .choices__list--multiple .choices__item {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            font-size: 0.85rem;
        }

        /* --- Buttons --- */
        .btn-submit {
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

        .btn-submit:hover {
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
            <div class="col-md-10 col-lg-9">

                {{-- Header --}}
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Buat Lisensi Baru</h1>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-light text-primary border border-primary-subtle">
                                <i class="bi bi-building me-1"></i> {{ $unit_kerja ?? 'Unit Kerja' }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('perawat.lisensi.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="form-card">
                    <form action="{{ route('perawat.lisensi.store') }}" method="POST">
                        @csrf

                        {{-- Input Hidden untuk Metode (Didapat dari controller) --}}
                        <input type="hidden" name="metode_perpanjangan" value="{{ $metode ?? '' }}">

                        <div class="row g-3">

                            {{-- Info Metode (Visual) --}}
                            @if (isset($metode))
                                <div class="col-12">
                                    <div class="alert alert-soft-primary d-flex align-items-center p-3 mb-0"
                                        style="background-color: var(--accent-light); border: 1px solid var(--accent-border);">
                                        <i class="bi bi-info-circle-fill me-2 fs-5" style="color: var(--accent-color);"></i>
                                        <div>
                                            Anda sedang mengajukan lisensi dengan metode:
                                            <strong>{{ $metode == 'pg_interview' ? 'PG + Wawancara' : 'Hanya Wawancara' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- 1. Informasi Dasar --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Lisensi <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" name="nama"
                                        class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                        required placeholder="Contoh: STR, SIP">
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Lembaga Penerbit <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="lembaga"
                                        class="form-control @error('lembaga') is-invalid @enderror"
                                        value="{{ old('lembaga') }}" required placeholder="Contoh: Kemenkes RI">
                                    @error('lembaga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Bidang <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-diagram-2"></i></span>
                                    <input type="text" name="bidang"
                                        class="form-control @error('bidang') is-invalid @enderror"
                                        value="{{ old('bidang') }}" required placeholder="Contoh: Keperawatan Umum">
                                    @error('bidang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="border-light m-0">
                            </div>

                            {{-- 2. KFK Dropdown (Choices JS) --}}
                            <div class="col-12">
                                <label class="form-label">Kompetensi Fungsional (KFK) <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-bar-chart-steps"></i></span>

                                    {{-- Gunakan name="kfk[]" dan multiple agar sesuai controller --}}
                                    <select name="kfk[]" id="choice-kfk"
                                        class="form-select @error('kfk') is-invalid @enderror" multiple required>
                                        <option value="">Pilih Jenjang KFK...</option>
                                        @php
                                            // Data KFK dikelompokkan agar lebih rapi
                                            $kfkCategories = [
                                                'Kebidanan' => [
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
                                                ],
                                                'Keperawatan Umum' => [
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
                                                ],
                                                'Keperawatan Kritis (ICU)' => [
                                                    'Keperawatan Kritis ICU PK 2',
                                                    'Keperawatan Kritis ICU PK 2.5',
                                                    'Keperawatan Kritis ICU PK 3',
                                                    'Keperawatan Kritis ICU PK 3.5',
                                                    'Keperawatan Kritis ICU PK 4',
                                                    'Keperawatan Kritis ICU PK 4.5',
                                                    'Keperawatan Kritis ICU PK 5',
                                                ],
                                                'Keperawatan Kritis (ICVCU)' => [
                                                    'Keperawatan Kritis ICVCU PK 2',
                                                    'Keperawatan Kritis ICVCU PK 2.5',
                                                    'Keperawatan Kritis ICVCU PK 3',
                                                    'Keperawatan Kritis ICVCU PK 3.5',
                                                    'Keperawatan Kritis ICVCU PK 4',
                                                    'Keperawatan Kritis ICVCU PK 4.5',
                                                    'Keperawatan Kritis ICVCU PK 5',
                                                ],
                                                'Keperawatan Kritis (Gawat Darurat)' => [
                                                    'Keperawatan Kritis Gawat Darurat PK 2',
                                                    'Keperawatan Kritis Gawat Darurat PK 2.5',
                                                    'Keperawatan Kritis Gawat Darurat PK 3',
                                                    'Keperawatan Kritis Gawat Darurat PK 3.5',
                                                    'Keperawatan Kritis Gawat Darurat PK 4',
                                                    'Keperawatan Kritis Gawat Darurat PK 4.5',
                                                    'Keperawatan Kritis Gawat Darurat PK 5',
                                                ],
                                                'Keperawatan Kritis (Anestesi)' => [
                                                    'Keperawatan Kritis Anestesi PK 2',
                                                    'Keperawatan Kritis Anestesi PK 2.5',
                                                    'Keperawatan Kritis Anestesi PK 3',
                                                    'Keperawatan Kritis Anestesi PK 3.5',
                                                    'Keperawatan Kritis Anestesi PK 4',
                                                    'Keperawatan Kritis Anestesi PK 4.5',
                                                    'Keperawatan Kritis Anestesi PK 5',
                                                ],
                                                'Keperawatan Anak (PICU)' => [
                                                    'Keperawatan Anak PICU PK 2',
                                                    'Keperawatan Anak PICU PK 2.5',
                                                    'Keperawatan Anak PICU PK 3',
                                                    'Keperawatan Anak PICU PK 3.5',
                                                    'Keperawatan Anak PICU PK 4',
                                                    'Keperawatan Anak PICU PK 4.5',
                                                    'Keperawatan Anak PICU PK 5',
                                                ],
                                                'Keperawatan Anak (NICU)' => [
                                                    'Keperawatan Anak NICU PK 2',
                                                    'Keperawatan Anak NICU PK 2.5',
                                                    'Keperawatan Anak NICU PK 3',
                                                    'Keperawatan Anak NICU PK 3.5',
                                                    'Keperawatan Anak NICU PK 4',
                                                    'Keperawatan Anak NICU PK 4.5',
                                                    'Keperawatan Anak NICU PK 5',
                                                ],
                                                'Keperawatan Anak (Neonatus)' => [
                                                    'Keperawatan Anak Neonatus PK 2',
                                                    'Keperawatan Anak Neonatus PK 2.5',
                                                    'Keperawatan Anak Neonatus PK 3',
                                                    'Keperawatan Anak Neonatus PK 3.5',
                                                    'Keperawatan Anak Neonatus PK 4',
                                                    'Keperawatan Anak Neonatus PK 4.5',
                                                    'Keperawatan Anak Neonatus PK 5',
                                                ],
                                                'Keperawatan Anak (Pediatri)' => [
                                                    'Keperawatan Anak Pediatri PK 2',
                                                    'Keperawatan Anak Pediatri PK 2.5',
                                                    'Keperawatan Anak Pediatri PK 3',
                                                    'Keperawatan Anak Pediatri PK 3.5',
                                                    'Keperawatan Anak Pediatri PK 4',
                                                    'Keperawatan Anak Pediatri PK 4.5',
                                                    'Keperawatan Anak Pediatri PK 5',
                                                ],
                                                'KMB (Interna)' => [
                                                    'Keperawatan Medikal Bedah Interna PK 2',
                                                    'Keperawatan Medikal Bedah Interna PK 2.5',
                                                    'Keperawatan Medikal Bedah Interna PK 3',
                                                    'Keperawatan Medikal Bedah Interna PK 3.5',
                                                    'Keperawatan Medikal Bedah Interna PK 4',
                                                    'Keperawatan Medikal Bedah Interna PK 4.5',
                                                    'Keperawatan Medikal Bedah Interna PK 5',
                                                ],
                                                'KMB (Bedah)' => [
                                                    'Keperawatan Medikal Bedah Bedah PK 2',
                                                    'Keperawatan Medikal Bedah Bedah PK 2.5',
                                                    'Keperawatan Medikal Bedah Bedah PK 3',
                                                    'Keperawatan Medikal Bedah Bedah PK 3.5',
                                                    'Keperawatan Medikal Bedah Bedah PK 4',
                                                    'Keperawatan Medikal Bedah Bedah PK 4.5',
                                                    'Keperawatan Medikal Bedah Bedah PK 5',
                                                ],
                                                'KMB (Kamar Operasi)' => [
                                                    'Keperawatan Medikal Bedah Kamar Operasi PK 2',
                                                    'Keperawatan Medikal Bedah Kamar Operasi PK 2.5',
                                                    'Keperawatan Medikal Bedah Kamar Operasi PK 3',
                                                    'Keperawatan Medikal Bedah Kamar Operasi PK 3.5',
                                                    'Keperawatan Medikal Bedah Kamar Operasi PK 4',
                                                    'Keperawatan Medikal Bedah Kamar Operasi PK 4.5',
                                                    'Keperawatan Medikal Bedah Kamar Operasi PK 5',
                                                ],
                                                'KMB (Isolasi)' => [
                                                    'Keperawatan Medikal Bedah Isolasi PK 2',
                                                    'Keperawatan Medikal Bedah Isolasi PK 2.5',
                                                    'Keperawatan Medikal Bedah Isolasi PK 3',
                                                    'Keperawatan Medikal Bedah Isolasi PK 3.5',
                                                    'Keperawatan Medikal Bedah Isolasi PK 4',
                                                    'Keperawatan Medikal Bedah Isolasi PK 4.5',
                                                    'Keperawatan Medikal Bedah Isolasi PK 5',
                                                ],
                                            ];
                                        @endphp

                                        @foreach ($kfkCategories as $category => $options)
                                            <optgroup label="{{ $category }}">
                                                @foreach ($options as $kfk)
                                                    <option value="{{ $kfk }}"
                                                        {{ collect(old('kfk'))->contains($kfk) ? 'selected' : '' }}>
                                                        {{ $kfk }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                @error('kfk')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="border-light m-0">
                            </div>

                            {{-- 3. Tanggal --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="tgl_mulai"
                                        class="form-control @error('tgl_mulai') is-invalid @enderror"
                                        value="{{ old('tgl_mulai') }}" required>
                                    @error('tgl_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Diselenggarakan <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tgl_diselenggarakan"
                                        class="form-control @error('tgl_diselenggarakan') is-invalid @enderror"
                                        value="{{ old('tgl_diselenggarakan') }}" required>
                                    @error('tgl_diselenggarakan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Terbit <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>
                                    <input type="date" name="tgl_terbit"
                                        class="form-control @error('tgl_terbit') is-invalid @enderror"
                                        value="{{ old('tgl_terbit') }}" required>
                                    @error('tgl_terbit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Expired <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-x"></i></span>
                                    <input type="date" name="tgl_expired"
                                        class="form-control @error('tgl_expired') is-invalid @enderror"
                                        value="{{ old('tgl_expired') }}" required>
                                    @error('tgl_expired')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="mt-4 pt-2">
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-save me-1"></i> Simpan Lisensi
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Load JS Choices --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup Choices.js untuk KFK
            const elementKfk = document.getElementById('choice-kfk');
            const choicesKfk = new Choices(elementKfk, {
                removeItemButton: true,
                searchEnabled: true,
                placeholderValue: 'Cari dan pilih Jenjang KFK...',
                noResultsText: 'KFK tidak ditemukan',
                itemSelectText: 'Tekan untuk memilih',
                shouldSort: false, // Agar urutan sesuai array PHP, tidak di-sort alfabet otomatis
                classNames: {
                    containerInner: 'choices__inner',
                    input: 'choices__input',
                }
            });
        });
    </script>
@endpush
