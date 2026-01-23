@extends('layouts.app')

@section('title', 'Penilaian Wawancara')

@push('styles')
    {{-- Paste CSS Style yang sama seperti sebelumnya di sini --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ... CSS SAMA PERSIS SEPERTI SEBELUMNYA ... */
        :root {
            --primary-color: #2563eb;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-body: #f8fafc;
            --border-color: #e2e8f0;
            --success-bg: #ecfdf5;
            --success-border: #10b981;
            --success-text: #047857;
            --danger-bg: #fef2f2;
            --danger-border: #ef4444;
            --danger-text: #b91c1c;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        .page-header {
            margin-bottom: 24px;
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

        .form-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
        }

        .ticket-header {
            background: #f1f5f9;
            padding: 24px 32px;
            border-bottom: 1px dashed #cbd5e1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .info-block {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .info-icon {
            width: 48px;
            height: 48px;
            background: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-gray);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .form-body {
            padding: 32px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-icon {
            color: var(--primary-color);
        }

        .decision-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .decision-item {
            position: relative;
        }

        .decision-item input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .decision-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #fff;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
            color: var(--text-gray);
        }

        .decision-box i {
            font-size: 2rem;
            margin-bottom: 8px;
            transition: transform 0.3s;
        }

        .decision-title {
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .decision-desc {
            font-size: 0.8rem;
            font-weight: 400;
            opacity: 0.8;
        }

        .decision-box:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .decision-item input:checked+.decision-box {
            transform: translateY(-2px);
        }

        /* Valid / Lulus Color */
        .decision-item input[value="lulus"]:checked+.decision-box,
        .decision-item input[value="valid"]:checked+.decision-box {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        /* Invalid / Tidak Lulus Color */
        .decision-item input[value="tidak_lulus"]:checked+.decision-box,
        .decision-item input[value="tidak_valid"]:checked+.decision-box {
            background: var(--danger-bg);
            border-color: var(--danger-border);
            color: var(--danger-text);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-save {
            background: var(--primary-color);
            color: #fff;
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            font-size: 1rem;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
            transition: all 0.2s;
        }

        .btn-save:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.4);
        }

        /* Styles Khusus Kredensial */
        .checklist-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .check-item {
            background: #fff;
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: 0.2s;
        }

        .check-item:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .check-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            accent-color: var(--primary-color);
            cursor: pointer;
        }

        .check-label {
            cursor: pointer;
            width: 100%;
            font-size: 0.95rem;
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            background: #f8fafc;
            transition: 0.2s;
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background: #eff6ff;
        }
    </style>
@endpush

@section('content')
    {{-- DETEKSI LOGIKA BERDASARKAN METODE DARI DB --}}
    @php
        // Gunakan $jadwal->pengajuan sesuai nama relasi di Controller & Model
        // Tambahkan '?? null' untuk keamanan jika data kosong
        $metode = $jadwal->pengajuan->metode ?? '';
        $isKredensial = $metode === 'interview_only';
    @endphp

    <div class="container py-5">
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    {{ $isKredensial ? 'Asesmen Kredensialing' : 'Penilaian Wawancara' }}
                </h1>
                <p class="text-muted small mb-0 mt-1">
                    {{ $isKredensial ? 'Validasi kompetensi dan berkas.' : 'Hasil akhir seleksi wawancara kompetensi.' }}
                </p>
            </div>
            <a href="{{ route('pewawancara.antrian') }}" class="btn btn-light border fw-bold shadow-sm"
                style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('pewawancara.penilaian.store', $jadwal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-card">
                <div class="ticket-header">
                    <div class="info-block border-end border-light pe-4">
                        <div class="info-icon text-primary"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <div class="info-label">Peserta</div>
                            {{-- Sesuaikan relasi user --}}
                            <div class="info-value">{{ $jadwal->pengajuanSertifikat->user->name ?? 'User' }}</div>
                        </div>
                    </div>
                    <div class="info-block ps-4">
                        <div class="info-icon text-info"><i class="bi bi-calendar-event"></i></div>
                        <div>
                            <div class="info-label">Metode</div>
                            <div class="info-value">
                                @if ($isKredensial)
                                    <span class="badge bg-secondary">Kredensial (Interview Only)</span>
                                @else
                                    <span class="badge bg-primary">Uji Kompetensi (PG + Interview)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-body">

                    {{-- === TAMPILAN KHUSUS KREDENSIALING === --}}
                    @if ($isKredensial)
                        <div class="section-header">
                            <i class="bi bi-list-check section-icon"></i> A. Daftar Tilik Kompetensi (Checklist)
                        </div>

                        <div class="checklist-grid">
                            {{-- Generate 20 Checkbox --}}
                            @for ($i = 1; $i <= 20; $i++)
                                <div class="check-item">
                                    <input type="checkbox" name="poin_penilaian[]" value="{{ $i }}"
                                        id="poin_{{ $i }}">
                                    <label for="poin_{{ $i }}" class="check-label">
                                        Poin Kompetensi Keperawatan #{{ $i }}
                                    </label>
                                </div>
                            @endfor
                        </div>

                        <div class="section-header mt-4">
                            <i class="bi bi-cloud-upload section-icon"></i> B. Upload Dokumen Hasil
                        </div>

                        <div class="mb-5">
                            <div class="upload-area">
                                <i class="bi bi-file-earmark-text fs-1 text-muted mb-2"></i>
                                <p class="mb-2 fw-bold">Upload Berkas Penilaian (Word/PDF)</p>
                                <input type="file" name="file_hasil" class="form-control" accept=".doc,.docx,.pdf"
                                    required>
                                <small class="text-muted d-block mt-2">File ini akan dikirim ke peserta.</small>
                            </div>
                            @error('file_hasil')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- === TAMPILAN KHUSUS UJI KOMPETENSI === --}}
                    @else
                        <div class="section-header">
                            <i class="bi bi-pencil-square section-icon"></i> A. Catatan Pewawancara
                        </div>

                        <div class="mb-5">
                            <textarea name="catatan" class="form-control p-3" rows="6"
                                placeholder="Tuliskan catatan hasil wawancara di sini..."
                                style="background: #f8fafc; border-color: #cbd5e1; border-radius: 12px;">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    @endif


                    {{-- === KEPUTUSAN AKHIR (Dynamic Label) === --}}
                    <div class="section-header">
                        <i class="bi bi-gavel section-icon"></i>
                        {{ $isKredensial ? 'C. Hasil Validasi' : 'B. Keputusan Akhir' }}
                    </div>

                    <div class="decision-grid mb-5">
                        {{-- OPSI POSITIF --}}
                        <div class="decision-item">
                            <input type="radio" name="keputusan" value="{{ $isKredensial ? 'valid' : 'lulus' }}"
                                id="dec_pass" required
                                {{ old('keputusan') == ($isKredensial ? 'valid' : 'lulus') ? 'checked' : '' }}>

                            <label for="dec_pass" class="decision-box">
                                <i class="bi bi-check-circle-fill"></i>
                                <div class="decision-title">{{ $isKredensial ? 'VALID' : 'LULUS' }}</div>
                                <div class="decision-desc">
                                    {{ $isKredensial ? 'Berkas & Kompetensi Sesuai' : 'Direkomendasikan Lulus' }}
                                </div>
                            </label>
                        </div>

                        {{-- OPSI NEGATIF --}}
                        <div class="decision-item">
                            <input type="radio" name="keputusan"
                                value="{{ $isKredensial ? 'tidak_valid' : 'tidak_lulus' }}" id="dec_fail" required
                                {{ old('keputusan') == ($isKredensial ? 'tidak_valid' : 'tidak_lulus') ? 'checked' : '' }}>

                            <label for="dec_fail" class="decision-box">
                                <i class="bi bi-x-circle-fill"></i>
                                <div class="decision-title">{{ $isKredensial ? 'TIDAK VALID' : 'TIDAK LULUS' }}</div>
                                <div class="decision-desc">
                                    {{ $isKredensial ? 'Perlu Revisi / Tidak Lengkap' : 'Belum Memenuhi Syarat' }}
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn-save"
                            onclick="return confirm('Apakah Anda yakin ingin menyimpan hasil ini?')">
                            <i class="bi bi-save2 me-2"></i> Simpan Hasil
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection
