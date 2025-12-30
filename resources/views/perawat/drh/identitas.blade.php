@extends('layouts.app')

@section('title', 'Edit Identitas – DIKSERA')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                {{-- Header Page --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Edit Profil</h4>
                        <p class="text-muted small mb-0">Perbarui data identitas dan riwayat hidup perawat.</p>
                    </div>
                    <a href="{{ route('perawat.drh') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                            <h6 class="mb-0 fw-bold">Gagal Menyimpan Data</h6>
                        </div>
                        <ul class="mb-0 small ps-4 mt-2">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perawat.identitas.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- Method POST sesuai route --}}

                    <div class="dash-card shadow-sm mb-4">
                        <div class="card-body p-4">

                            {{-- SECTION A: DATA PRIBADI --}}
                            <div class="section-header mb-4">
                                <i class="bi bi-person-badge me-2"></i>A. Data Pribadi & Identitas
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label">Nama Lengkap (Beserta Gelar) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" class="form-control"
                                        value="{{ old('nama_lengkap', $profile->nama_lengkap ?? $user->name) }}"
                                        placeholder="Contoh: Ns. Siti Aminah, S.Kep">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipe Perawat <span class="text-danger">*</span></label>
                                    <select name="type_perawat[]" id="type-perawat" class="form-select" multiple>
                                        <option value="" disabled>Pilih Tipe Perawat...</option>
                                        @php
                                            $typeList = [
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
                                        @endphp

                                        @php
                                            $selectedValue = old('type_perawat', $profile->type_perawat ?? []);
                                            // Normalize old value to array — accept array, comma/string, or JSON
                                            if (is_string($selectedValue)) {
                                                $decoded = json_decode($selectedValue, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $selectedValue = $decoded;
                                                } elseif (strpos($selectedValue, ',') !== false) {
                                                    $selectedValue = array_map('trim', explode(',', $selectedValue));
                                                } else {
                                                    $selectedValue = $selectedValue ? [$selectedValue] : [];
                                                }
                                            } elseif (!is_array($selectedValue)) {
                                                $selectedValue = $selectedValue ? [$selectedValue] : [];
                                            }
                                        @endphp

                                        @foreach ($typeList as $type)
                                            <option value="{{ $type }}"
                                                {{ in_array($type, $selectedValue) ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">NIK (KTP)</label>
                                    <input type="text" name="nik" class="form-control"
                                        value="{{ old('nik', $profile->nik ?? '') }}" placeholder="16 digit angka">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control"
                                        value="{{ old('tempat_lahir', $profile->tempat_lahir ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control"
                                        value="{{ old('tanggal_lahir', $profile->tanggal_lahir ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-select">
                                        <option value="">Pilih...</option>
                                        <option value="L"
                                            {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="P"
                                            {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Agama</label>
                                    <select name="agama" class="form-select">
                                        <option value="">Pilih...</option>
                                        @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                                            <option value="{{ $agm }}"
                                                {{ old('agama', $profile->agama ?? '') == $agm ? 'selected' : '' }}>
                                                {{ $agm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status Perkawinan</label>
                                    <select name="status_perkawinan" class="form-select">
                                        <option value="">Pilih...</option>
                                        @foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sts)
                                            <option value="{{ $sts }}"
                                                {{ old('status_perkawinan', $profile->status_perkawinan ?? '') == $sts ? 'selected' : '' }}>
                                                {{ $sts }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Aliran Kepercayaan <small
                                            class="text-muted">(Opsional)</small></label>
                                    <input type="text" name="aliran_kepercayaan" class="form-control"
                                        value="{{ old('aliran_kepercayaan', $profile->aliran_kepercayaan ?? '') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Hobi / Kegemaran</label>
                                    <input type="text" name="hobby" class="form-control"
                                        value="{{ old('hobby', $profile->hobby ?? '') }}"
                                        placeholder="Contoh: Membaca Jurnal, Jogging, Renang">
                                </div>
                            </div>

                            {{-- SECTION B: KONTAK --}}
                            <div class="section-header mb-4 mt-5">
                                <i class="bi bi-geo-alt-fill me-2"></i>B. Kontak & Domisili
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nomor HP (WhatsApp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted">+62</span>
                                        <input type="text" name="no_hp" class="form-control"
                                            value="{{ old('no_hp', $profile->no_hp ?? '') }}" placeholder="812xxxx">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kota Domisili</label>
                                    <input type="text" name="kota" class="form-control"
                                        value="{{ old('kota', $profile->kota ?? '') }}"
                                        placeholder="Contoh: Jakarta Selatan">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="alamat" rows="2" class="form-control" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan">{{ old('alamat', $profile->alamat ?? '') }}</textarea>
                                </div>
                            </div>

                            {{-- SECTION C: FISIK --}}
                            <div class="section-header mb-4 mt-5">
                                <i class="bi bi-heart-pulse-fill me-2"></i>C. Keterangan Fisik
                            </div>
                            <div class="bg-light p-3 rounded-3 mb-4">
                                <div class="row g-3">
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted">Tinggi (cm)</label>
                                        <input type="number" name="tinggi_badan" class="form-control"
                                            value="{{ old('tinggi_badan', $profile->tinggi_badan ?? '') }}">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted">Berat (kg)</label>
                                        <input type="number" name="berat_badan" class="form-control"
                                            value="{{ old('berat_badan', $profile->berat_badan ?? '') }}">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted">Gol. Darah</label>
                                        <select name="golongan_darah" class="form-select">
                                            <option value="">-</option>
                                            @foreach (['A', 'B', 'AB', 'O'] as $gd)
                                                <option value="{{ $gd }}"
                                                    {{ old('golongan_darah', $profile->golongan_darah ?? '') == $gd ? 'selected' : '' }}>
                                                    {{ $gd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted">Warna Kulit</label>
                                        <input type="text" name="warna_kulit" class="form-control"
                                            value="{{ old('warna_kulit', $profile->warna_kulit ?? '') }}"
                                            placeholder="Sawo Matang">
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small text-muted">Rambut</label>
                                        <input type="text" name="rambut" class="form-control"
                                            value="{{ old('rambut', $profile->rambut ?? '') }}"
                                            placeholder="Hitam, Ikal">
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small text-muted">Bentuk Muka</label>
                                        <input type="text" name="bentuk_muka" class="form-control"
                                            value="{{ old('bentuk_muka', $profile->bentuk_muka ?? '') }}"
                                            placeholder="Oval">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small text-muted">Cacat Tubuh</label>
                                        <input type="text" name="cacat_tubuh" class="form-control"
                                            value="{{ old('cacat_tubuh', $profile->cacat_tubuh ?? '') }}"
                                            placeholder="-">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Ciri Khas</label>
                                        <input type="text" name="ciri_khas" class="form-control"
                                            value="{{ old('ciri_khas', $profile->ciri_khas ?? '') }}"
                                            placeholder="Contoh: Tahi lalat di pipi kanan">
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION D: KEPEGAWAIAN --}}
                            <div class="section-header mb-4 mt-5">
                                <i class="bi bi-briefcase-fill me-2"></i>D. Data Kepegawaian
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Jabatan Saat Ini</label>
                                    <input type="text" name="jabatan" class="form-control"
                                        value="{{ old('jabatan', $profile->jabatan ?? '') }}"
                                        placeholder="Contoh: Perawat Ahli Muda">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Pangkat</label>
                                    <input type="text" name="pangkat" class="form-control"
                                        value="{{ old('pangkat', $profile->pangkat ?? '') }}" placeholder="Penata Muda">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Golongan</label>
                                    <input type="text" name="golongan" class="form-control"
                                        value="{{ old('golongan', $profile->golongan ?? '') }}" placeholder="III/a">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NIP</label>
                                    <input type="text" name="nip" class="form-control"
                                        value="{{ old('nip', $profile->nip ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NIRP</label>
                                    <input type="text" name="nirp" class="form-control"
                                        value="{{ old('nirp', $profile->nirp ?? '') }}">
                                </div>
                            </div>

                            {{-- SECTION E: FOTO --}}
                            <div class="section-header mb-4 mt-5">
                                <i class="bi bi-camera-fill me-2"></i>E. Foto Profil
                            </div>
                            <div class="row align-items-center bg-light border rounded-3 p-3 mx-1">
                                <div class="col-auto">
                                    @if ($profile && $profile->foto_3x4)
                                        <img src="{{ asset('storage/' . $profile->foto_3x4) }}"
                                            class="avatar-preview shadow-sm">
                                    @else
                                        <div class="avatar-placeholder shadow-sm">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="form-label fw-bold">Upload Foto Terbaru (3x4)</label>
                                    <input type="file" name="foto_3x4" class="form-control">
                                    <div class="form-text mt-1">
                                        Format: JPG, JPEG, PNG. Ukuran maksimal 2MB. Disarankan latar belakang merah/biru.
                                    </div>
                                </div>
                            </div>

                        </div> {{-- End Card Body --}}

                        <div class="card-footer bg-white p-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('perawat.drh') }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                            </button>
                        </div>

                    </div> {{-- End Dash Card --}}
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Choices('#type-perawat', {
                searchEnabled: true,
                searchPlaceholderValue: 'Ketik untuk mencari tipe perawat...',
                itemSelectText: '',
                shouldSort: false,
                allowHTML: false,
                removeItemButton: true,
                maxItemCount: 5
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

    <style>
        /* Card Styling */
        .dash-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Section Header Styling */
        .section-header {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-header i {
            color: #3b82f6;
            /* Warna Primary Bootstrap */
        }

        /* Form Control Styling */
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        textarea.form-control {
            resize: vertical;
        }

        /* Avatar Preview Styling */
        .avatar-preview {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #fff;
        }

        .avatar-placeholder {
            width: 100px;
            height: 120px;
            background-color: #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            border: 3px solid #fff;
        }
    </style>
@endpush
