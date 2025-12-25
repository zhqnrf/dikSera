@extends('layouts.app')

@section('title', 'Edit Penanggung Jawab – Admin DIKSERA')

@push('styles')
    <style>
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft, #e2e8f0);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 32px;
        }

        .form-control-custom {
            border-radius: 8px;
            border: 1px solid var(--border-soft, #e2e8f0);
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control-custom:focus {
            border-color: var(--blue-main, #0d6efd);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        select.form-control-custom {
            appearance: auto;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">

            {{-- Tombol Kembali --}}
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('admin.penanggung-jawab.index') }}" class="btn btn-sm btn-outline-secondary px-3"
                    style="border-radius: 8px;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.penanggung-jawab.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Wajib untuk Edit --}}

                <div class="content-card">
                    {{-- AREA PESAN ERROR --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h5 class="mb-4 fw-bold text-dark border-bottom pb-3">Edit Penanggung Jawab</h5>

                    {{-- NAMA --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control form-control-custom"
                            value="{{ old('nama', $item->nama) }}" required>
                    </div>

                    {{-- EMAIL --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Email (Untuk Login)</label>
                        <input type="email" name="email" class="form-control form-control-custom"
                            value="{{ old('email', $item->user->email ?? '') }}" required>
                    </div>

                    {{-- KOLOM TIPE PETUGAS SUDAH DIHAPUS --}}

                    {{-- JABATAN --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan" class="form-control form-control-custom" required>
                            <option value="">Pilih Jabatan</option>
                            <option value="Pewawancara"
                                {{ old('jabatan', $item->jabatan) == 'Pewawancara' ? 'selected' : '' }}>Pewawancara</option>
                            <option value="PJ" {{ old('jabatan', $item->jabatan) == 'PJ' ? 'selected' : '' }}>PJ
                            </option>
                        </select>
                    </div>

                    {{-- PASSWORD (OPSIONAL UNTUK EDIT) --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="text" name="password" class="form-control form-control-custom"
                            placeholder="Kosongkan jika password tidak diubah">
                        <small class="text-muted" style="font-size: 11px;">*Isi hanya jika ingin mengganti password</small>
                    </div>

                    {{-- NO HP --}}
                    <div class="form-group mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control form-control-custom"
                            value="{{ old('no_hp', $item->no_hp) }}">
                    </div>

                    {{-- TOMBOL --}}
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <a href="{{ route('admin.penanggung-jawab.index') }}" class="btn btn-light px-4"
                            style="border-radius: 8px;">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"
                            style="border-radius: 8px; background-color: #0d6efd; border-color: #0d6efd;">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
