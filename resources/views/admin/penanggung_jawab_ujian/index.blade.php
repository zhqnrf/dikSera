@extends('layouts.app')

@section('title', 'Penanggung Jawab Ujian – Admin DIKSERA')

@push('styles')
    <style>
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft, #e2e8f0);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 32px;
        }
        .btn-custom-primary {
            background-color: var(--blue-main, #0d6efd);
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            border: none;
        }
        .table-custom th {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
        }
        .table-custom td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">Penanggung Jawab Ujian</h4>
            <a href="{{ route('admin.penanggung-jawab.create') }}" class="btn btn-custom-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Baru
            </a>
        </div>

        <div class="content-card">
            @if(session('success'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success mb-4 rounded-3">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>No. HP</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $item->nama }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->jabatan }}</span></td>
                            <td>{{ $item->no_hp }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.penanggung-jawab.edit', $item->id) }}"
                                   class="btn btn-sm btn-outline-primary me-1" style="border-radius: 6px;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.penanggung-jawab.destroy', $item->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x display-6 d-block mb-2 opacity-25"></i>
                                Belum ada data penanggung jawab.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
