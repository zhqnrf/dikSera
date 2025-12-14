@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Tambah Soal Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.bank-soal.store') }}" method="POST">
                    @csrf

                    {{-- Pertanyaan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pertanyaan</label>
                        <textarea name="pertanyaan" class="form-control" rows="3" required placeholder="Tulis soal di sini...">{{ old('pertanyaan') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" class="form-select">
                                <option value="Umum">Keperawatan Umum</option>
                                <option value="Gawat Darurat">Gawat Darurat</option>
                                <option value="Maternitas">Maternitas</option>
                                <option value="Anak">Anak</option>
                            </select>
                        </div>
                    </div>

                    {{-- Opsi Jawaban A-E --}}
                    <div class="row g-3">
                        @foreach (['a', 'b', 'c', 'd', 'e'] as $key)
                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-text fw-bold text-uppercase bg-light"
                                        style="width: 45px;">{{ $key }}</span>
                                    <input type="text" name="opsi[{{ $key }}]" class="form-control"
                                        placeholder="Jawaban opsi {{ strtoupper($key) }}" required
                                        value="{{ old('opsi.' . $key) }}">

                                    {{-- Radio Button untuk Kunci Jawaban --}}
                                    <div class="input-group-text bg-white">
                                        <input class="form-check-input mt-0" type="radio" name="kunci_jawaban"
                                            value="{{ $key }}" required
                                            {{ old('kunci_jawaban') == $key ? 'checked' : '' }}>
                                        <label class="ms-2 small text-muted">Benar</label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.bank-soal.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
