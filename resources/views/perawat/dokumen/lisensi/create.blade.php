@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Buat Lisensi Baru</h4>
                        <small class="text-muted">Unit Kerja Saat Ini: <strong>{{ $unit_kerja }}</strong></small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('perawat.lisensi.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lisensi <span class="text-danger">*</span></label>
                                    <input type="text" name="nama"
                                        class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                        required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lembaga <span class="text-danger">*</span></label>
                                    <input type="text" name="lembaga"
                                        class="form-control @error('lembaga') is-invalid @enderror"
                                        value="{{ old('lembaga') }}" required>
                                    @error('lembaga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bidang <span class="text-danger">*</span></label>
                                    <input type="text" name="bidang"
                                        class="form-control @error('bidang') is-invalid @enderror"
                                        value="{{ old('bidang') }}" required>
                                    @error('bidang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Metode Perpanjangan <span class="text-danger">*</span></label>
                                    <select name="metode_perpanjangan"
                                        class="form-select @error('metode_perpanjangan') is-invalid @enderror" required>
                                        <option value="">Pilih Metode</option>
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

                                <div class="col-12 mb-3">
                                    <label class="form-label">Kompetensi Fungsional Keperawatan (KFK) <span
                                            class="text-danger">*</span></label>
                                    <div class="row">
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
                                            <div class="col-md-3 col-sm-4 col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="kfk[]"
                                                        value="{{ $kfk }}"
                                                        id="kfk_{{ str_replace(' ', '_', $kfk) }}"
                                                        {{ in_array($kfk, old('kfk', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="kfk_{{ str_replace(' ', '_', $kfk) }}">
                                                        {{ $kfk }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('kfk')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_mulai"
                                        class="form-control @error('tgl_mulai') is-invalid @enderror"
                                        value="{{ old('tgl_mulai') }}" required>
                                    @error('tgl_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Diselenggarakan <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tgl_diselenggarakan"
                                        class="form-control @error('tgl_diselenggarakan') is-invalid @enderror"
                                        value="{{ old('tgl_diselenggarakan') }}" required>
                                    @error('tgl_diselenggarakan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Terbit <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_terbit"
                                        class="form-control @error('tgl_terbit') is-invalid @enderror"
                                        value="{{ old('tgl_terbit') }}" required>
                                    @error('tgl_terbit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Expired <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_expired"
                                        class="form-control @error('tgl_expired') is-invalid @enderror"
                                        value="{{ old('tgl_expired') }}" required>
                                    @error('tgl_expired')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Lisensi
                                </button>
                                <a href="{{ route('perawat.lisensi.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
