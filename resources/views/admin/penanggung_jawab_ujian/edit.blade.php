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
    </style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.penanggung-jawab.index') }}" class="btn btn-sm btn-outline-secondary px-3"
                style="border-radius: 8px;">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('admin.penanggung-jawab.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="content-card">
                <h5 class="mb-4 fw-bold text-dark border-bottom pb-3">Edit Penanggung Jawab</h5>

                <div class="mb-4">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"
                              style="border-color: #e2e8f0;"><i class="bi bi-person"></i></span>
                        <input type="text" name="nama" value="{{ old('nama', $item->nama) }}"
                            class="form-control form-control-custom border-start-0 ps-0" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"
                              style="border-color: #e2e8f0;"><i class="bi bi-briefcase"></i></span>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $item->jabatan) }}"
                            class="form-control form-control-custom border-start-0 ps-0" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">No. WhatsApp / HP <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"
                              style="border-color: #e2e8f0;"><i class="bi bi-whatsapp"></i></span>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $item->no_hp) }}"
                            class="form-control form-control-custom border-start-0 ps-0" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px; background-color: #0d6efd; border-color: #0d6efd;">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
