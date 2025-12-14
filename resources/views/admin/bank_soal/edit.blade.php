@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Edit Soal</h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.bank-soal.update', $soal->id) }}" method="POST">
                    @csrf

                    {{-- Pertanyaan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pertanyaan</label>
                        <textarea name="pertanyaan" class="form-control" rows="3" required>{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                    </div>

                    {{-- Kategori --}}
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" class="form-select">
                                @foreach (['Umum', 'Gawat Darurat', 'Maternitas', 'Anak'] as $kategori)
                                    <option value="{{ $kategori }}"
                                        {{ old('kategori', $soal->kategori) == $kategori ? 'selected' : '' }}>
                                        {{ $kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Opsi Jawaban --}}
                    <div class="row g-3">
                        @foreach (['a', 'b', 'c', 'd', 'e'] as $key)
                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-text fw-bold text-uppercase bg-light" style="width:45px;">
                                        {{ $key }}
                                    </span>

                                    <input type="text" name="opsi[{{ $key }}]" class="form-control" required
                                        value="{{ old('opsi.' . $key, $soal->opsi_jawaban[$key] ?? '') }}">

                                    <div class="input-group-text bg-white">
                                        <input class="form-check-input mt-0" type="radio" name="kunci_jawaban"
                                            value="{{ $key }}"
                                            {{ old('kunci_jawaban', $soal->kunci_jawaban) == $key ? 'checked' : '' }}>
                                        <label class="ms-2 small text-muted">Benar</label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Action --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.bank-soal.index') }}" class="btn btn-light">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update Soal
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
