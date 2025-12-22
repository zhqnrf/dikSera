@extends('layouts.app')

@section('title', 'Tambah Lisensi – DIKSERA')

@push('styles')
    {{-- Load CSS Choices --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --input-border: #cbd5e1;
            /* Warna Tema Create: Biru */
            --accent-color: #2563eb;
            --accent-hover: #1d4ed8;
            --accent-light: #eff6ff;
            --accent-border: #dbeafe;
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

        /* --- Inputs Styling (COMPACT) --- */
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
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
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
            padding: 4px 8px !important;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .choices.is-focused .choices__inner {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .choices__list--multiple .choices__item {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            font-size: 0.85rem;
        }

        .choices__list--dropdown .choices__item {
            font-size: 0.9rem;
            padding: 8px 12px;
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
        .btn-submit {
            background-color: var(--accent-color);
            color: white;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background-color: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
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
                        <h1 class="page-title">Tambah Lisensi Baru</h1>
                        <p class="text-muted small mb-0">Formulir administrasi data lisensi perawat.</p>
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

                    <form action="{{ route('admin.lisensi.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            {{-- 1. Aturan Perpanjangan --}}
                            <div class="col-12">
                                <div class="metode-wrapper">
                                    <div class="d-flex gap-3 align-items-center">
                                        <i class="bi bi-sliders text-primary fs-5"></i>
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
                                                        class="form-select border-primary fw-bold text-dark form-select-sm"
                                                        required>
                                                        <option value="pg_only"
                                                            {{ old('metode_perpanjangan') == 'pg_only' ? 'selected' : '' }}>
                                                            Hanya Ujian Tulis
                                                        </option>
                                                        <option value="pg_interview"
                                                            {{ old('metode_perpanjangan') == 'pg_interview' ? 'selected' : '' }}>
                                                            Ujian Tulis + Wawancara
                                                        </option>
                                                        <option value="interview_only"
                                                            {{ old('metode_perpanjangan') == 'interview_only' ? 'selected' : '' }}>
                                                            Hanya Wawancara
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Pilih Perawat (Multi Select) --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Pilih Perawat (Bisa Banyak) <span
                                            class="required-star">*</span></label>

                                    {{-- Tombol Aksi --}}
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-select-all">
                                            <i class="bi bi-check-all"></i> Pilih Semua
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn-reset-all">
                                            <i class="bi bi-x-circle"></i> Reset
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-1">
                                    <select name="user_ids[]" id="choice-users" class="form-select" multiple required>
                                        {{-- Option kosong untuk placeholder --}}
                                        <option value="">Cari Nama Perawat...</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ collect(old('user_ids'))->contains($user->id) ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->unit_kerja ?? 'Unit Tidak Ada' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text">Nomor lisensi akan digenerate otomatis berurutan untuk setiap perawat
                                    yang dipilih.</div>
                            </div>

                            <div class="col-12">
                                <hr class="border-light m-0">
                            </div>

                            {{-- BAGIAN BARU: Detail Bidang & KFK --}}
                            <div class="col-md-6">
                                <label class="form-label">Bidang Keahlian <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                    <input type="text" name="bidang" class="form-control" value="{{ old('bidang') }}"
                                        placeholder="Contoh: Keperawatan Anak" required>
                                </div>
                            </div>

                            {{-- PERUBAHAN DI SINI: Jenjang KFK Multi Select --}}
                            <div class="col-md-6">
                                <label class="form-label">Jenjang KFK (PK) <span class="required-star">*</span></label>
                                <div class="input-group">
                                    {{-- Catatan: Icon input-group-text mungkin perlu dihilangkan jika mengganggu tampilan Choices,
                                         atau dipindahkan ke luar jika menggunakan multi-select --}}
                                    <span class="input-group-text"><i class="bi bi-bar-chart-steps"></i></span>

                                    {{-- name="kfk[]" (array) dan multiple="multiple" --}}
                                    <select name="kfk[]" id="choice-kfk" class="form-select" multiple required>
                                        <option value="">Pilih Jenjang KFK...</option>
                                        @php
                                            $kfks = [
                                                    'Pra PK',
                                                    'Pra BK',
                                                    'PK 1', 'PK 1.5', 'PK 2', 'PK 2.5', 'PK 3', 'PK 3.5', 'PK 4', 'PK 4.5', 'PK 5',
                                                    'BK 1', 'BK 1.5', 'BK 2', 'BK 2.5', 'BK 3', 'BK 3.5', 'BK 4', 'BK 4.5', 'BK 5'
                                            ];
                                        @endphp
                                        @foreach ($kfks as $kfk)
                                            {{-- Gunakan collect() atau in_array() karena old('kfk') sekarang adalah array --}}
                                            <option value="{{ $kfk }}"
                                                {{ collect(old('kfk'))->contains($kfk) ? 'selected' : '' }}>
                                                {{ $kfk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Tanggal Pelaksanaan --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="tgl_mulai" class="form-control"
                                        value="{{ old('tgl_mulai') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai Diselenggarakan <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tgl_diselenggarakan" class="form-control"
                                        value="{{ old('tgl_diselenggarakan') }}" required>
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
                                        value="{{ old('nama') }}" placeholder="Contoh: STR, SIP" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Lembaga Penerbit <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="lembaga" class="form-control"
                                        value="{{ old('lembaga') }}" placeholder="Contoh: Kemenkes RI" required>
                                </div>
                            </div>

                            {{-- 4. Tanggal --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Terbit <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tgl_terbit" class="form-control"
                                        value="{{ old('tgl_terbit') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Expired <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-x"></i></span>
                                    <input type="date" name="tgl_expired" class="form-control"
                                        value="{{ old('tgl_expired') }}" required>
                                </div>
                            </div>

                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-4 pt-2">
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-save2 me-1"></i> Simpan Data Lisensi
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
            // --- 1. SETUP CHOICES UNTUK USER (PERAWAT) ---
            const elementUser = document.getElementById('choice-users');
            const btnSelectAll = document.getElementById('btn-select-all');
            const btnResetAll = document.getElementById('btn-reset-all');

            // ID dari PHP (untuk Select All User)
            const allUserIds = {!! json_encode($users->pluck('id')->map(fn($id) => (string) $id)) !!};

            const choicesUser = new Choices(elementUser, {
                removeItemButton: true,
                searchEnabled: true,
                placeholderValue: 'Cari dan pilih perawat...',
                noResultsText: 'Tidak ada perawat ditemukan',
                itemSelectText: 'Tekan untuk memilih',
                shouldSort: false,
            });

            // Logika Select All User
            btnSelectAll.addEventListener('click', function(e) {
                e.preventDefault();
                choicesUser.removeActiveItems();
                choicesUser.setChoiceByValue(allUserIds);
            });

            // Logika Reset All User
            btnResetAll.addEventListener('click', function(e) {
                e.preventDefault();
                choicesUser.removeActiveItems();
            });

            // --- 2. SETUP CHOICES UNTUK KFK (JENJANG PK) ---
            const elementKfk = document.getElementById('choice-kfk');

            const choicesKfk = new Choices(elementKfk, {
                removeItemButton: true,
                searchEnabled: false, // Biasanya KFK sedikit opsinya, search tidak wajib
                placeholderValue: 'Pilih Jenjang KFK...',
                itemSelectText: 'Tekan untuk memilih',
                shouldSort: false,
                // Tambahan styling agar konsisten
                classNames: {
                    containerInner: 'choices__inner',
                    input: 'choices__input',
                }
            });

        });
    </script>
@endpush
