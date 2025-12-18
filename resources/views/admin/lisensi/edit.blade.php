@extends('layouts.app')

@section('title', 'Edit Lisensi – DIKSERA')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --input-border: #cbd5e1;
            --accent-color: #f59e0b;
            /* Orange */
            --accent-hover: #d97706;
            --accent-light: #fffbeb;
            --accent-border: #fcd34d;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 14px;
            /* Base font size lebih kecil */
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
            /* Lebih proporsional */
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
            /* Padding dikurangi */
        }

        /* --- Inputs Styling (COMPACT) --- */
        .form-label {
            font-size: 0.85rem;
            /* 13-14px */
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
            /* Padding lebih kecil */
            font-size: 0.9rem;
            /* Font input pas */
            line-height: 1.5;
            height: auto;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
            outline: none;
        }

        /* Input Group Icons */
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

        /* --- Choices JS (Compact) --- */
        .choices__inner {
            background-color: #fff;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            min-height: 38px;
            /* Tinggi disamakan dengan input biasa */
            padding: 4px 8px !important;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .choices.is-focused .choices__inner {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
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
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
            transition: all 0.2s;
        }

        .btn-submit-edit:hover {
            background-color: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
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
    <div class="container py-4"> {{-- Padding container dikurangi --}}
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">

                {{-- Header --}}
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Edit Data Lisensi</h1>
                        <p class="text-muted small mb-0">Update informasi dan aturan perpanjangan.</p>
                    </div>
                    <a href="{{ route('admin.lisensi.index') }}" class="btn-back">
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

                    <form action="{{ route('admin.lisensi.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3"> {{-- Gap antar elemen diperkecil (g-3) --}}

                            {{-- 1. Pemilik Lisensi --}}
                            <div class="col-12">
                                <label class="form-label">Pemilik Lisensi <span class="required-star">*</span></label>
                                <select name="user_id" id="choice-user-single" class="form-select" required>
                                    <option value="">Pilih Perawat...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id', $data->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Aturan Perpanjangan --}}
                            <div class="col-12">
                                <div class="metode-wrapper">
                                    <div class="d-flex gap-3 align-items-center">
                                        <i class="bi bi-sliders text-warning fs-5"></i>
                                        <div class="flex-grow-1">
                                            <div class="row align-items-center">
                                                <div class="col-md-7">
                                                    <label class="form-label text-dark mb-0">Metode Perpanjangan <span
                                                            class="required-star">*</span></label>
                                                    <div class="text-muted" style="font-size: 11px;">Pilih cara evaluasi
                                                        untuk lisensi ini.</div>
                                                </div>
                                                <div class="col-md-5">
                                                    <select name="metode_perpanjangan"
                                                        class="form-select border-warning fw-bold text-dark form-select-sm"
                                                        required>
                                                        <option value="pg_only"
                                                            {{ old('metode_perpanjangan', $data->metode_perpanjangan) == 'pg_only' ? 'selected' : '' }}>
                                                            Hanya Ujian Tulis
                                                        </option>
                                                        <option value="pg_interview"
                                                            {{ old('metode_perpanjangan', $data->metode_perpanjangan) == 'pg_interview' ? 'selected' : '' }}>
                                                            Ujian Tulis + Wawancara
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="border-light m-0">
                            </div>

                            {{-- 3. Identitas Lisensi --}}
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

                            {{-- 4. Tanggal --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Terbit <span class="required-star">*</span></label>
                                <input type="date" name="tgl_terbit" class="form-control"
                                    value="{{ old('tgl_terbit', $data->tgl_terbit) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Expired <span class="required-star">*</span></label>
                                <input type="date" name="tgl_expired" class="form-control"
                                    value="{{ old('tgl_expired', $data->tgl_expired) }}" required>
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
            const element = document.getElementById('choice-user-single');
            const choices = new Choices(element, {
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
        });
    </script>
@endpush
