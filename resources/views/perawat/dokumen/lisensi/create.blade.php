@extends('layouts.app')

@section('content')
    {{-- Styling Khusus (Mengadopsi Style Admin) --}}
    @push('styles')
        <style>
            :root {
                --text-dark: #1e293b;
                --text-gray: #64748b;
                --bg-light: #f1f5f9;
                --input-border: #cbd5e1;
                /* Warna Tema Perawat: Biru Standard */
                --accent-color: #0d6efd;
                --accent-hover: #0b5ed7;
                --accent-light: #e7f1ff;
                --accent-border: #b6d4fe;
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
                box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
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

            /* --- KFK Checkbox Styling (Pills) --- */
            .kfk-wrapper {
                background-color: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 15px;
            }

            .btn-check:checked+.btn-outline-primary {
                background-color: var(--accent-light);
                color: var(--accent-color);
                border-color: var(--accent-color);
                font-weight: 600;
            }

            .btn-outline-primary {
                color: var(--text-gray);
                border-color: var(--input-border);
                font-size: 0.8rem;
                border-radius: 6px;
                background: white;
            }

            .btn-outline-primary:hover {
                background-color: white;
                border-color: var(--accent-color);
                color: var(--accent-color);
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
                box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
                transition: all 0.2s;
            }

            .btn-submit:hover {
                background-color: var(--accent-hover);
                transform: translateY(-1px);
                box-shadow: 0 4px 6px rgba(13, 110, 253, 0.3);
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

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">

                {{-- Header --}}
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Buat Lisensi Baru</h1>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-light text-primary border border-primary-subtle">
                                <i class="bi bi-building me-1"></i> {{ $unit_kerja }}
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

                        <div class="row g-3">

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

                            <div class="col-md-6">
                                <label class="form-label">Metode Perpanjangan <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-sliders"></i></span>
                                    <select name="metode_perpanjangan"
                                        class="form-select @error('metode_perpanjangan') is-invalid @enderror" required>
                                        <option value="">Pilih Metode...</option>
                                        <option value="interview_only"
                                            {{ old('metode_perpanjangan') == 'interview_only' ? 'selected' : '' }}>Interview
                                            Only</option>
                                        <option value="pg_interview"
                                            {{ old('metode_perpanjangan') == 'pg_interview' ? 'selected' : '' }}>PG +
                                            Interview</option>
                                    </select>
                                    @error('metode_perpanjangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="border-light m-0">
                            </div>

                            {{-- 2. KFK Checkboxes (Styled as Pills) --}}
                            <div class="col-12">
                                <label class="form-label">Kompetensi Fungsional (KFK) <span
                                        class="required-star">*</span></label>
                                <div class="kfk-wrapper">
                                    <div class="row g-2">
                                        @php
                                            $kfkOptions = [
                                                'Pra PK',
                                                'Pra BK',
                                                'PK 1',
                                                'PK 1.5',
                                                'PK 2',
                                                'PK 2.5',
                                                'PK 3',
                                                'PK 3.5',
                                                'PK 4',
                                                'PK 4.5',
                                                'PK 5',
                                                'BK 1',
                                                'BK 1.5',
                                                'BK 2',
                                                'BK 2.5',
                                                'BK 3',
                                                'BK 3.5',
                                                'BK 4',
                                                'BK 4.5',
                                                'BK 5',
                                            ];
                                        @endphp
                                        @foreach ($kfkOptions as $kfk)
                                            <div class="col-4 col-md-3 col-lg-2">
                                                <input type="checkbox" class="btn-check" name="kfk[]"
                                                    value="{{ $kfk }}" id="kfk_{{ str_replace(' ', '_', $kfk) }}"
                                                    {{ in_array($kfk, old('kfk', [])) ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 py-1"
                                                    for="kfk_{{ str_replace(' ', '_', $kfk) }}">
                                                    {{ $kfk }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('kfk')
                                        <div class="text-danger mt-2 small"><i class="bi bi-exclamation-circle me-1"></i>
                                            {{ $message }}</div>
                                    @enderror
                                </div>
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
