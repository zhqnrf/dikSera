@extends('layouts.app')

@section('title', 'Data Keluarga – DIKSERA')

@section('content')
<div class="container py-3">
    <div class="dash-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Data Keluarga</h6>
            <a href="{{ route('perawat.drh') }}" class="btn btn-sm btn-outline-secondary">
                Kembali ke DRH
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 small">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM TAMBAH --}}
        <form action="{{ route('perawat.keluarga.store') }}" method="POST" class="small mb-3">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Hubungan *</label>
                    <select name="hubungan" class="form-select form-select-sm" required>
                        <option value="">- Pilih -</option>
                        <option value="Suami">Suami</option>
                        <option value="Istri">Istri</option>
                        <option value="Anak">Anak</option>
                        <option value="Ayah">Ayah</option>
                        <option value="Ibu">Ibu</option>
                        <option value="Saudara">Saudara</option>
                        <option value="Mertua">Mertua</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        + Tambah
                    </button>
                </div>
            </div>
        </form>

        {{-- TABEL LIST --}}
        <div class="table-responsive small">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;">No</th>
                        <th style="width:15%;">Hubungan</th>
                        <th>Nama Lengkap</th>
                        <th style="width:15%;">Tanggal Lahir</th>
                        <th style="width:20%;">Pekerjaan</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keluarga as $i => $row)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td colspan="4" class="p-0">
                                {{-- Form Edit Inline --}}
                                <form action="{{ route('perawat.keluarga.update', $row->id) }}" method="POST">
                                    @csrf
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td style="width:15%;">
                                                <select name="hubungan" class="form-select form-select-sm">
                                                    <option value="Suami" {{ $row->hubungan == 'Suami' ? 'selected' : '' }}>Suami</option>
                                                    <option value="Istri" {{ $row->hubungan == 'Istri' ? 'selected' : '' }}>Istri</option>
                                                    <option value="Anak" {{ $row->hubungan == 'Anak' ? 'selected' : '' }}>Anak</option>
                                                    <option value="Ayah" {{ $row->hubungan == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                                    <option value="Ibu" {{ $row->hubungan == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                                    <option value="Saudara" {{ $row->hubungan == 'Saudara' ? 'selected' : '' }}>Saudara</option>
                                                    <option value="Mertua" {{ $row->hubungan == 'Mertua' ? 'selected' : '' }}>Mertua</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="nama" value="{{ $row->nama }}" class="form-control form-control-sm">
                                            </td>
                                            <td style="width:15%;">
                                                <input type="date" name="tanggal_lahir" value="{{ $row->tanggal_lahir }}" class="form-control form-control-sm">
                                            </td>
                                            <td style="width:20%;">
                                                <input type="text" name="pekerjaan" value="{{ $row->pekerjaan }}" class="form-control form-control-sm">
                                            </td>
                                        </tr>
                                    </table>
                                {{-- Tombol submit hidden untuk enter key --}}
                                <input type="submit" style="display: none;">
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- Tombol Simpan --}}
                                    <button type="button" onclick="this.closest('tr').querySelector('form').submit()" class="btn btn-sm btn-outline-primary">
                                        Simpan
                                    </button>

                                    {{-- Form Delete --}}
                                    </form>
                                    <form action="{{ route('perawat.keluarga.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data keluarga ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Belum ada data keluarga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
