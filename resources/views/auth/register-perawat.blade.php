@extends('layouts.auth', [
    'title' => 'Registrasi Perawat – DIKSERA',
    'fullWidth' => true
])
@section('content')
<div class="text-center mb-3">
    <div class="logo-big mx-auto">
        <img src="{{ asset('icon.png') }}" alt="DIKSERA">
    </div>
    <div class="auth-title">Registrasi Perawat</div>
    <div class="auth-subtitle">
        Lengkapi akun dan Daftar Riwayat Hidup (DRH) sesuai format.
    </div>
    <div style="font-size:11px;margin-top:4px;">
        <a href="{{ route('landing') }}">← Kembali ke halaman utama</a>
    </div>
</div>

{{-- ALERT ERROR VALIDASI SERVER --}}
@if ($errors->any())
    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:12px;">
        <strong>Terjadi kesalahan pada isian Anda:</strong>
        <ul class="mb-0 mt-1" style="padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ route('register.perawat.process') }}"
      id="registerForm"
      enctype="multipart/form-data">
    @csrf

    {{-- STEP INDICATOR --}}
    <div class="mb-3">
        <div class="step-dots-wrapper d-flex justify-content-between flex-wrap">
            <div class="step-dot step-dot-active" data-step="1">
                <span class="step-dot-index">1</span>
                <span class="step-dot-label">Akun &amp; Identitas</span>
            </div>
            <div class="step-dot" data-step="2">
                <span class="step-dot-index">2</span>
                <span class="step-dot-label">Alamat &amp; Badan</span>
            </div>
            <div class="step-dot" data-step="3">
                <span class="step-dot-index">3</span>
                <span class="step-dot-label">Pendidikan &amp; Kerja</span>
            </div>
            <div class="step-dot" data-step="4">
                <span class="step-dot-index">4</span>
                <span class="step-dot-label">Keluarga &amp; Organisasi</span>
            </div>
        </div>
    </div>

    {{-- STEP 1 – I. KETERANGAN PERORANGAN (IDENTITAS + JABATAN) --}}
    <div class="step-pane" data-step="1">
        <div class="step-title">I. Keterangan Perorangan – Akun &amp; Identitas</div>
        <div class="row g-3">
            <div class="col-lg-7">
                {{-- 2. Nama --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Nama Lengkap *</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           data-required="true"
                           class="form-control form-control-sm form-control-light @error('name') is-invalid @enderror">
                </div>

                {{-- 8. Email --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">E-mail *</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           data-required="true"
                           class="form-control form-control-sm form-control-light @error('email') is-invalid @enderror">
                </div>

                {{-- Password --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Password *</label>
                    <div class="password-wrapper">
                        <input id="password_reg"
                               type="password"
                               name="password"
                               data-required="true"
                               class="form-control form-control-sm form-control-light @error('password') is-invalid @enderror">
                        <button type="button" class="toggle-password-btn" data-target="password_reg">Lihat</button>
                    </div>
                    <small class="text-muted" style="font-size:11px;">Min. 6 karakter.</small>
                </div>

                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Konfirmasi Password *</label>
                    <div class="password-wrapper">
                        <input id="password_reg_confirmation"
                               type="password"
                               name="password_confirmation"
                               data-required="true"
                               class="form-control form-control-sm form-control-light">
                        <button type="button" class="toggle-password-btn" data-target="password_reg_confirmation">Lihat</button>
                    </div>
                </div>

                {{-- 12. Kegemaran / Hobby --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Kegemaran / Hobby</label>
                    <input type="text"
                           name="hobby"
                           value="{{ old('hobby') }}"
                           class="form-control form-control-sm form-control-light @error('hobby') is-invalid @enderror">
                </div>
            </div>

            <div class="col-lg-5">
                {{-- 1. NIK --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Nomor Induk Kependudukan</label>
                    <input type="text"
                           name="nik"
                           value="{{ old('nik') }}"
                           class="form-control form-control-sm form-control-light @error('nik') is-invalid @enderror">
                </div>

                {{-- NIP & NIRP --}}
                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">NIP</label>
                        <input type="text"
                               name="nip"
                               value="{{ old('nip') }}"
                               class="form-control form-control-sm form-control-light @error('nip') is-invalid @enderror">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">NIRP</label>
                        <input type="text"
                               name="nirp"
                               value="{{ old('nirp') }}"
                               class="form-control form-control-sm form-control-light @error('nirp') is-invalid @enderror">
                    </div>
                </div>

                {{-- 3 & 4. Kab/Kota Lahir + Tanggal Lahir --}}
                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Kabupaten / Kota Lahir</label>
                        <input type="text"
                               name="tempat_lahir"
                               value="{{ old('tempat_lahir') }}"
                               class="form-control form-control-sm form-control-light @error('tempat_lahir') is-invalid @enderror">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Tanggal Lahir</label>
                        <input type="date"
                               name="tanggal_lahir"
                               value="{{ old('tanggal_lahir') }}"
                               class="form-control form-control-sm form-control-light @error('tanggal_lahir') is-invalid @enderror">
                    </div>
                </div>

                {{-- 5 / 6 / 7 – Jenis Kelamin, Agama, Status Kawin, Aliran Kepercayaan --}}
                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                                class="form-select form-select-sm form-control-light @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Agama</label>
                        <select name="agama"
                                class="form-select form-select-sm form-control-light @error('agama') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @php
                                $agamaList = ['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'];
                            @endphp
                            @foreach($agamaList as $ag)
                                <option value="{{ $ag }}" {{ old('agama')==$ag?'selected':'' }}>{{ $ag }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Aliran Kepercayaan</label>
                        <select name="aliran_kepercayaan"
                                class="form-select form-select-sm form-control-light @error('aliran_kepercayaan') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @php
                                $kepercayaanList = [
                                    'Tidak / Bukan Penghayat',
                                    'Penghayat Kepercayaan terhadap Tuhan YME',
                                    'Lainnya',
                                ];
                            @endphp
                            @foreach($kepercayaanList as $kp)
                                <option value="{{ $kp }}" {{ old('aliran_kepercayaan')==$kp?'selected':'' }}>
                                    {{ $kp }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Status Perkawinan</label>
                        <select name="status_perkawinan"
                                class="form-select form-select-sm form-control-light @error('status_perkawinan') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @php
                                $statusList = ['Belum Menikah','Menikah','Cerai Hidup','Cerai Mati'];
                            @endphp
                            @foreach($statusList as $st)
                                <option value="{{ $st }}" {{ old('status_perkawinan')==$st?'selected':'' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jabatan / Pangkat / Golongan --}}
                <div class="mb-2 row g-2">
                    <div class="col-6 col-md-6">
                        <label class="form-label small text-muted mb-1">Jabatan</label>
                        <input type="text"
                               name="jabatan"
                               value="{{ old('jabatan') }}"
                               class="form-control form-control-sm form-control-light @error('jabatan') is-invalid @enderror">
                    </div>
                    <div class="col-3 col-md-3">
                        <label class="form-label small text-muted mb-1">Pangkat</label>
                        <input type="text"
                               name="pangkat"
                               value="{{ old('pangkat') }}"
                               class="form-control form-control-sm form-control-light @error('pangkat') is-invalid @enderror">
                    </div>
                    <div class="col-3 col-md-3">
                        <label class="form-label small text-muted mb-1">Golongan</label>
                        <input type="text"
                               name="golongan"
                               value="{{ old('golongan') }}"
                               class="form-control form-control-sm form-control-light @error('golongan') is-invalid @enderror">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 2 – ALAMAT, BADAN, FOTO --}}
    <div class="step-pane d-none" data-step="2">
        <div class="step-title">I. Keterangan Perorangan – Alamat &amp; Keterangan Badan</div>
        <div class="row g-3">
            <div class="col-lg-7">
                {{-- 9. No HP (WA Aktif) --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Nomor Handphone (Aktif WA) *</label>
                    <input type="text"
                           name="no_hp"
                           value="{{ old('no_hp') }}"
                           data-required="true"
                           class="form-control form-control-sm form-control-light @error('no_hp') is-invalid @enderror">
                </div>

                {{-- 10.a Jalan --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Alamat – Jalan *</label>
                    <input type="text"
                           name="alamat_jalan"
                           value="{{ old('alamat_jalan') }}"
                           data-required="true"
                           class="form-control form-control-sm form-control-light @error('alamat_jalan') is-invalid @enderror">
                </div>

                {{-- 10.b & 10.c Kelurahan & Kecamatan --}}
                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Kelurahan *</label>
                        <input type="text"
                               name="alamat_kelurahan"
                               value="{{ old('alamat_kelurahan') }}"
                               data-required="true"
                               class="form-control form-control-sm form-control-light @error('alamat_kelurahan') is-invalid @enderror">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Kecamatan *</label>
                        <input type="text"
                               name="alamat_kecamatan"
                               value="{{ old('alamat_kecamatan') }}"
                               data-required="true"
                               class="form-control form-control-sm form-control-light @error('alamat_kecamatan') is-invalid @enderror">
                    </div>
                </div>

                {{-- 10.d & 10.e Kab/Kota & Provinsi --}}
                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Kabupaten / Kota *</label>
                        <input type="text"
                               name="alamat_kabkota"
                               value="{{ old('alamat_kabkota') }}"
                               data-required="true"
                               class="form-control form-control-sm form-control-light @error('alamat_kabkota') is-invalid @enderror">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Provinsi *</label>
                        <input type="text"
                               name="alamat_provinsi"
                               value="{{ old('alamat_provinsi') }}"
                               data-required="true"
                               class="form-control form-control-sm form-control-light @error('alamat_provinsi') is-invalid @enderror">
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                {{-- 11. Keterangan Badan --}}
                <div class="mb-2 row g-2">
                    <div class="col-4">
                        <label class="form-label small text-muted mb-1">Tinggi (cm)</label>
                        <input type="number"
                               name="tinggi_badan"
                               value="{{ old('tinggi_badan') }}"
                               class="form-control form-control-sm form-control-light @error('tinggi_badan') is-invalid @enderror">
                    </div>
                    <div class="col-4">
                        <label class="form-label small text-muted mb-1">Berat Badan</label>
                        <input type="number"
                               name="berat_badan"
                               value="{{ old('berat_badan') }}"
                               class="form-control form-control-sm form-control-light @error('berat_badan') is-invalid @enderror">
                    </div>
                    <div class="col-4">
                        <label class="form-label small text-muted mb-1">Gol. Darah</label>
                        <select name="golongan_darah"
                                class="form-select form-select-sm form-control-light @error('golongan_darah') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @php
                                $goldarList = ['A','B','AB','O','Tidak Tahu'];
                            @endphp
                            @foreach($goldarList as $gd)
                                <option value="{{ $gd }}" {{ old('golongan_darah')==$gd?'selected':'' }}>
                                    {{ $gd }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Rambut</label>
                        <select name="rambut"
                                class="form-select form-select-sm form-control-light @error('rambut') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @php
                                $rambutList = ['Pendek','Sedang','Panjang','Beruban','Lainnya'];
                            @endphp
                            @foreach($rambutList as $rb)
                                <option value="{{ $rb }}" {{ old('rambut')==$rb?'selected':'' }}>{{ $rb }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Bentuk Muka</label>
                        <select name="bentuk_muka"
                                class="form-select form-select-sm form-control-light @error('bentuk_muka') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @php
                                $mukaList = ['Oval','Bulat','Persegi','Hati','Lainnya'];
                            @endphp
                            @foreach($mukaList as $mk)
                                <option value="{{ $mk }}" {{ old('bentuk_muka')==$mk?'selected':'' }}>{{ $mk }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Warna Kulit</label>
                        <select name="warna_kulit"
                                class="form-select form-select-sm form-control-light @error('warna_kulit') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @php
                                $kulitList = ['Putih','Kuning Langsat','Sawo Matang','Gelap','Lainnya'];
                            @endphp
                            @foreach($kulitList as $kl)
                                <option value="{{ $kl }}" {{ old('warna_kulit')==$kl?'selected':'' }}>{{ $kl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Ciri Khas</label>
                        <input type="text"
                               name="ciri_khas"
                               value="{{ old('ciri_khas') }}"
                               class="form-control form-control-sm form-control-light @error('ciri_khas') is-invalid @enderror">
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Cacat Tubuh</label>
                    <select name="cacat_tubuh"
                            class="form-select form-select-sm form-control-light @error('cacat_tubuh') is-invalid @enderror">
                        <option value="">-- Pilih --</option>
                        @php
                            $cacatList = ['Tidak Ada','Ada'];
                        @endphp
                        @foreach($cacatList as $ct)
                            <option value="{{ $ct }}" {{ old('cacat_tubuh')==$ct?'selected':'' }}>{{ $ct }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Foto 3x4 --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Foto 3x4 (jpg/png)</label>
                    <div>
                        <input type="file"
                               name="foto_3x4"
                               class="form-control form-control-sm @error('foto_3x4') is-invalid @enderror"
                               accept="image/*"
                               data-show-filename="true"
                               data-show-preview="true">
                        <small class="text-muted file-name" style="font-size:10px;"></small>
                        <div class="mt-2">
                            <img class="img-preview"
                                 src="#"
                                 alt="Preview Foto 3x4"
                                 style="max-height:130px;border-radius:8px;display:none;border:1px solid #e5e7eb;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 3 – II & III: Pendidikan / Kursus / Riwayat Pekerjaan --}}
    <div class="step-pane d-none" data-step="3">
        <div class="step-title">II. Pendidikan &amp; Kursus · III. Riwayat Pekerjaan</div>

        {{-- Pendidikan (II.1) --}}
        <div class="sub-section-head">1. Pendidikan di Dalam dan Luar Negeri</div>
        <div id="pendidikan-wrapper" class="mb-2">
            <div class="row g-1 mb-1 pendidikan-row dynamic-row">
                <div class="col-6 col-md-2">
                    <input type="text" name="pendidikan_jenjang[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Tingkat">
                </div>
                <div class="col-6 col-md-3">
                    <input type="text" name="pendidikan_nama[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Nama Sekolah / PT">
                </div>
                <div class="col-6 col-md-2">
                    <input type="text" name="pendidikan_akreditasi[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Akreditasi">
                </div>
                <div class="col-6 col-md-2">
                    <input type="text" name="pendidikan_tempat[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Tempat">
                </div>
                <div class="col-12 col-md-3 d-flex flex-column gap-1">
                    <input type="file"
                           name="pendidikan_file[]"
                           class="form-control form-control-sm form-control-light"
                           accept=".pdf,image/*"
                           data-show-filename="true"
                           data-show-preview="true">
                    <small class="text-muted file-name" style="font-size:10px;">STTB/Ijazah (pdf/jpg)</small>
                    <img class="img-preview"
                         src="#"
                         alt="Preview"
                         style="max-height:60px;border-radius:6px;display:none;border:1px solid #e5e7eb;">
                    <button type="button"
                            class="btn btn-link text-danger p-0 align-self-start remove-row-btn"
                            style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-pendidikan">+ Tambah Pendidikan</button>

        {{-- Kursus / Latihan (II.2) --}}
        <div class="sub-section-head mt-2">2. Kursus / Latihan di Dalam dan Luar Negeri</div>
        <div id="pelatihan-wrapper" class="mb-2">
            <div class="row g-1 mb-1 pelatihan-row dynamic-row">
                <div class="col-12 col-md-3">
                    <input type="text" name="pelatihan_nama[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Nama Kursus / Latihan">
                </div>
                <div class="col-6 col-md-2">
                    <input type="text" name="pelatihan_durasi[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Lamanya">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="pelatihan_mulai[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Tgl Mulai">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="pelatihan_selesai[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Tgl Selesai">
                </div>
                <div class="col-6 col-md-3 d-flex flex-column gap-1">
                    <input type="file"
                           name="pelatihan_file[]"
                           class="form-control form-control-sm form-control-light"
                           accept=".pdf,image/*"
                           data-show-filename="true"
                           data-show-preview="true">
                    <small class="text-muted file-name" style="font-size:10px;">Sertifikat (pdf/jpg)</small>
                    <img class="img-preview"
                         src="#"
                         alt="Preview"
                         style="max-height:60px;border-radius:6px;display:none;border:1px solid #e5e7eb;">
                    <button type="button"
                            class="btn btn-link text-danger p-0 align-self-start remove-row-btn"
                            style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-pelatihan">+ Tambah Kursus / Pelatihan</button>

        {{-- Riwayat Pekerjaan (III) --}}
        <div class="sub-section-head mt-2">III. Riwayat Pekerjaan</div>
        <div id="pekerjaan-wrapper" class="mb-2">
            <div class="row g-1 mb-1 pekerjaan-row dynamic-row">
                <div class="col-12 col-md-3">
                    <input type="text" name="pekerjaan_instansi[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Instansi / Perusahaan">
                </div>
                <div class="col-12 col-md-3">
                    <input type="text" name="pekerjaan_jabatan[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Jabatan">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="pekerjaan_mulai[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Mulai">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="pekerjaan_selesai[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Selesai">
                </div>
                <div class="col-12 col-md-2 d-flex flex-column gap-1">
                    <input type="file"
                           name="pekerjaan_file[]"
                           class="form-control form-control-sm form-control-light"
                           accept=".pdf,image/*"
                           data-show-filename="true"
                           data-show-preview="true">
                    <small class="text-muted file-name" style="font-size:10px;">SK Pengangkatan</small>
                    <img class="img-preview"
                         src="#"
                         alt="Preview"
                         style="max-height:60px;border-radius:6px;display:none;border:1px solid #e5e7eb;">
                    <button type="button"
                            class="btn btn-link text-danger p-0 align-self-start remove-row-btn"
                            style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-pekerjaan">+ Tambah Riwayat Pekerjaan</button>
    </div>

    {{-- STEP 4 – IV Tanda Jasa, V Keluarga, VI Organisasi --}}
    <div class="step-pane d-none" data-step="4">
        <div class="step-title">IV. Tanda Jasa · V. Riwayat Keluarga · VI. Organisasi</div>

        {{-- Tanda Jasa / Penghargaan --}}
        <div class="sub-section-head">IV. Tanda Jasa / Penghargaan</div>
        <div id="tandajasa-wrapper" class="mb-2">
            <div class="row g-1 mb-1 tandajasa-row dynamic-row">
                <div class="col-12 col-md-3">
                    <input type="text" name="tandajasa_nama[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Nama Penghargaan">
                </div>
                <div class="col-12 col-md-3">
                    <input type="text" name="tandajasa_instansi[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Negara / Instansi Pemberi">
                </div>
                <div class="col-6 col-md-2">
                    <input type="text" name="tandajasa_tahun[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Tahun">
                </div>
                <div class="col-6 col-md-4 d-flex flex-column gap-1">
                    <input type="file"
                           name="tandajasa_file[]"
                           class="form-control form-control-sm form-control-light"
                           accept=".pdf,image/*"
                           data-show-filename="true"
                           data-show-preview="true">
                    <small class="text-muted file-name" style="font-size:10px;">SK / Piagam</small>
                    <img class="img-preview"
                         src="#"
                         alt="Preview"
                         style="max-height:60px;border-radius:6px;display:none;border:1px solid #e5e7eb;">
                    <button type="button"
                            class="btn btn-link text-danger p-0 align-self-start remove-row-btn"
                            style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-tandajasa">+ Tambah Tanda Jasa</button>

        {{-- Riwayat Keluarga --}}
        <div class="sub-section-head mt-2">V. Riwayat Keluarga (Istri/Suami, Anak, Orang Tua, Saudara, Mertua)</div>
        <div id="keluarga-wrapper" class="mb-2">
            <div class="row g-1 mb-1 keluarga-row dynamic-row">
                <div class="col-12 col-md-2">
                    <input type="text" name="keluarga_hubungan[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Hubungan">
                </div>
                <div class="col-12 col-md-3">
                    <input type="text" name="keluarga_nama[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Nama">
                </div>
                <div class="col-12 col-md-3">
                    <input type="text" name="keluarga_ttl[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="TTL">
                </div>
                <div class="col-12 col-md-4 d-flex align-items-start gap-1">
                    <input type="text" name="keluarga_pekerjaan[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Pekerjaan / Instansi">
                    <button type="button"
                            class="btn btn-link text-danger p-0 remove-row-btn"
                            style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-keluarga">+ Tambah Anggota Keluarga</button>

        {{-- Organisasi --}}
        <div class="sub-section-head mt-2">VI. Keterangan Organisasi</div>
        <div id="organisasi-wrapper" class="mb-2">
            <div class="row g-1 mb-1 organisasi-row dynamic-row">
                <div class="col-12 col-md-3">
                    <input type="text" name="organisasi_nama[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Nama Organisasi">
                </div>
                <div class="col-12 col-md-3">
                    <input type="text" name="organisasi_jabatan[]"
                           class="form-control form-control-sm form-control-light"
                           placeholder="Jabatan di Organisasi">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="organisasi_mulai[]"
                           class="form-control form-control-sm form-control-light">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="organisasi_selesai[]"
                           class="form-control form-control-sm form-control-light">
                </div>
                <div class="col-12 col-md-2 d-flex flex-column gap-1">
                    <input type="file"
                           name="organisasi_file[]"
                           class="form-control form-control-sm form-control-light"
                           accept=".pdf,image/*"
                           data-show-filename="true"
                           data-show-preview="true">
                    <small class="text-muted file-name" style="font-size:10px;">Kartu / SK</small>
                    <img class="img-preview"
                         src="#"
                         alt="Preview"
                         style="max-height:60px;border-radius:6px;display:none;border:1px solid #e5e7eb;">
                    <button type="button"
                            class="btn btn-link text-danger p-0 align-self-start remove-row-btn"
                            style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>

        <p class="small text-muted mt-1">
            Pastikan data sudah terisi dengan benar sebelum menekan tombol <strong>Daftar</strong>.
        </p>
    </div>

    {{-- STEP NAV BUTTONS --}}
    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPrev">Sebelumnya</button>
        <div>
            <button type="button" class="btn btn-sm btn-solid-blue" id="btnNext">Berikutnya</button>
            <button type="submit" class="btn btn-sm btn-solid-blue d-none" id="btnSubmit">Daftar</button>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    .step-dots-wrapper{
        font-size:11px;
        gap:6px;
        background: linear-gradient(135deg,#eef2ff,#e0ecff);
        border-radius: 999px;
        padding: 6px 6px;
        border: 1px solid rgba(148,163,184,0.6);
        box-shadow: 0 8px 18px rgba(15,23,42,0.06);
    }
    .step-dot {
        flex: 1 1 0;
        min-width: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 4px;
        border-radius: 999px;
        background: rgba(255,255,255,0.85);
        color: #4b5563;
        font-weight: 500;
        border: 1px solid rgba(209,213,219,0.9);
        transition: background 0.2s ease, box-shadow 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        white-space: nowrap;
    }
    .step-dot-index{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:18px;
        height:18px;
        border-radius:999px;
        background:#e5edff;
        font-size:11px;
        font-weight:600;
        color:#1d4ed8;
    }
    .step-dot-label{
        overflow:hidden;
        text-overflow:ellipsis;
    }
    .step-dot-active {
        background: linear-gradient(135deg,#2563eb,#60a5fa);
        color: #f9fafb;
        box-shadow: 0 8px 20px rgba(37,99,235,0.45);
        border-color: transparent;
    }
    .step-dot-active .step-dot-index{
        background:rgba(248,250,252,0.95);
        color:#1d4ed8;
    }

    .step-pane {
        position: relative;
        border-radius: 18px;
        background: linear-gradient(145deg,#ffffff,#f3f6ff);
        border: 1px solid rgba(209,213,219,0.9);
        padding: 16px 18px;
        box-shadow: 0 10px 24px rgba(15,23,42,0.07);
        margin-bottom: 4px;
        overflow: hidden;
    }
    .step-pane::before{
        content:"";
        position:absolute;
        top:0;
        left:0;
        right:0;
        height:4px;
        background: linear-gradient(90deg,rgba(37,99,235,0.15),transparent);
    }

    .step-title {
        position: relative;
        font-size: 13px;
        font-weight: 600;
        color: #1d4ed8;
        margin-bottom: 10px;
        padding-left: 10px;
    }
    .step-title::before{
        content:"";
        position:absolute;
        left:0;
        top:50%;
        transform:translateY(-50%);
        width:4px;
        height:16px;
        border-radius:999px;
        background: linear-gradient(180deg,#2563eb,#38bdf8);
    }

    .sub-section-head {
        font-size: 12px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
        padding-left: 6px;
        border-left: 3px solid #2563eb;
    }

    @media (max-width: 576px){
        .step-dots-wrapper{
            gap:4px !important;
            padding:4px 4px;
            border-radius:14px;
        }
        .step-dot{
            flex: 1 1 calc(50% - 4px);
            font-size:10px;
            padding:5px 3px;
        }
        .step-dot-label{
            max-width:80px;
        }
        .step-pane{
            padding:10px 10px;
        }
    }
    .auth-card .form-control-sm,
    .auth-card .form-select-sm {
        min-height: 42px;              /* tinggi lebih nyaman */
        padding: 0.4rem 0.75rem;       /* jarak dalam lebih lega */
        font-size: 13px;               /* teks sedikit lebih besar */
        border-radius: 10px;           /* biar tetap soft */
    }

    .auth-card .form-label {
        font-size: 12px;
    }

    /* jarak antar field */
    .auth-card .mb-2 {
        margin-bottom: 0.6rem !important;
    }

    /* di mobile, jangan terlalu sempit */
    @media (max-width: 576px){
        .auth-card .form-control-sm,
        .auth-card .form-select-sm {
            min-height: 44px;
            font-size: 13px;
        }
    }

</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentStep = 1;
    const maxStep = 4;

    const stepDots  = document.querySelectorAll('.step-dot');
    const stepPanes = document.querySelectorAll('.step-pane');
    const btnPrev   = document.getElementById('btnPrev');
    const btnNext   = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');

    function renderSteps() {
        stepPanes.forEach(pane => {
            pane.classList.toggle('d-none', parseInt(pane.dataset.step) !== currentStep);
        });
        stepDots.forEach(dot => {
            dot.classList.toggle('step-dot-active', parseInt(dot.dataset.step) === currentStep);
        });

        if (btnPrev) btnPrev.disabled = currentStep === 1;
        if (btnNext) btnNext.classList.toggle('d-none', currentStep === maxStep);
        if (btnSubmit) btnSubmit.classList.toggle('d-none', currentStep !== maxStep);
    }

    function hasEmptyRequired(step) {
        const pane = document.querySelector('.step-pane[data-step="' + step + '"]');
        if (!pane) return false;

        let empty = false;
        const requiredFields = pane.querySelectorAll('[data-required="true"]');
        requiredFields.forEach(el => {
            const val = (el.value || '').trim();
            if (!val) {
                empty = true;
                el.classList.add('is-invalid');
            } else {
                el.classList.remove('is-invalid');
            }
        });

        if (empty && typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap',
                text: 'Silakan isi semua data wajib pada langkah ini terlebih dahulu.',
            });
        }

        return empty;
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                renderSteps();
            }
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', function () {
            if (hasEmptyRequired(currentStep)) {
                return;
            }
            if (currentStep < maxStep) {
                currentStep++;
                renderSteps();
            }
        });
    }

    // klik step dot buat loncat (optional, tapi enak)
    stepDots.forEach(dot => {
        dot.addEventListener('click', function () {
            const targetStep = parseInt(this.dataset.step);
            if (!targetStep) return;

            // jangan biarkan lompat kalau step current masih ada required kosong
            if (targetStep > currentStep && hasEmptyRequired(currentStep)) {
                return;
            }

            currentStep = targetStep;
            renderSteps();
        });
    });

    // Show/hide password
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
        });
    });

    // Preview nama file & preview gambar
    function bindFilePreview(input) {
        const parent = input.parentElement;
        const label = parent ? parent.querySelector('.file-name') : null;
        const imgEl = parent ? parent.querySelector('.img-preview') : null;

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];

            if (label && this.dataset.showFilename === 'true') {
                label.textContent = file ? file.name : '';
            }

            if (imgEl && this.dataset.showPreview === 'true') {
                if (file && file.type && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imgEl.src = e.target.result;
                        imgEl.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imgEl.src = '#';
                    imgEl.style.display = 'none';
                }
            }
        });
    }

    function initFileInputs(root) {
        (root || document).querySelectorAll('input[type="file"]').forEach(input => {
            bindFilePreview(input);
        });
    }

    initFileInputs(document);

    // Helper clone row
    function cloneRow(wrapperId, rowClass) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const row = wrapper.querySelector('.' + rowClass);
        if (!row) return;

        const clone = row.cloneNode(true);

        clone.querySelectorAll('input').forEach(i => {
            if (i.type === 'file') {
                i.value = null;
            } else {
                i.value = '';
            }
        });
        clone.querySelectorAll('.file-name').forEach(fn => fn.textContent = '');
        clone.querySelectorAll('.img-preview').forEach(img => {
            img.src = '#';
            img.style.display = 'none';
        });

        wrapper.appendChild(clone);
        initFileInputs(clone);
    }

    // ADD buttons
    const addPendidikanBtn = document.getElementById('add-pendidikan');
    if (addPendidikanBtn) {
        addPendidikanBtn.addEventListener('click', function () {
            cloneRow('pendidikan-wrapper', 'pendidikan-row');
        });
    }

    const addPelatihanBtn = document.getElementById('add-pelatihan');
    if (addPelatihanBtn) {
        addPelatihanBtn.addEventListener('click', function () {
            cloneRow('pelatihan-wrapper', 'pelatihan-row');
        });
    }

    const addPekerjaanBtn = document.getElementById('add-pekerjaan');
    if (addPekerjaanBtn) {
        addPekerjaanBtn.addEventListener('click', function () {
            cloneRow('pekerjaan-wrapper', 'pekerjaan-row');
        });
    }

    const addKeluargaBtn = document.getElementById('add-keluarga');
    if (addKeluargaBtn) {
        addKeluargaBtn.addEventListener('click', function () {
            cloneRow('keluarga-wrapper', 'keluarga-row');
        });
    }

    const addOrganisasiBtn = document.getElementById('add-organisasi');
    if (addOrganisasiBtn) {
        addOrganisasiBtn.addEventListener('click', function () {
            cloneRow('organisasi-wrapper', 'organisasi-row');
        });
    }

    const addTandaJasaBtn = document.getElementById('add-tandajasa');
    if (addTandaJasaBtn) {
        addTandaJasaBtn.addEventListener('click', function () {
            cloneRow('tandajasa-wrapper', 'tandajasa-row');
        });
    }

    // Hapus row – guard minimal 1 baris
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row-btn');
        if (!btn) return;

        const row = btn.closest('.dynamic-row');
        if (!row) return;

        let wrapper = row.closest('[id$="-wrapper"]');
        if (!wrapper) wrapper = row.parentElement;

        const rows = wrapper.querySelectorAll('.dynamic-row');

        if (rows.length <= 1) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak dapat dihapus',
                    text: 'Minimal satu baris data harus tersedia.',
                });
            }
            return;
        }

        row.remove();
    });

    // AUTO PINDAH STEP KE FIELD ERROR PERTAMA (DARI SERVER)
    @if($errors->any())
    const errorKeys = @json($errors->keys());
    (function jumpToFirstError() {
        let firstField = null;

        errorKeys.some(function (key) {
            let el = document.querySelector('[name="' + key + '"]');
            if (!el) el = document.querySelector('[name^="' + key + '["]');
            if (el) { firstField = el; return true; }
            return false;
        });

        if (firstField) {
            const pane = firstField.closest('.step-pane');
            if (pane && pane.dataset.step) {
                currentStep = parseInt(pane.dataset.step);
                renderSteps();
            }
            setTimeout(function () {
                firstField.scrollIntoView({behavior: 'smooth', block: 'center'});
                firstField.focus();
                firstField.classList.add('is-invalid');
            }, 200);
        }
    })();
    @endif

    renderSteps();
});
</script>
@endpush
