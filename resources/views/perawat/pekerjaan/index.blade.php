@extends('layouts.app')

@section('title', 'Riwayat Pekerjaan – DIKSERA')

@section('content')
<div class="container py-3">
    <div class="dash-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Riwayat Pekerjaan</h6>
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
        <form action="{{ route('perawat.pekerjaan.store') }}" method="POST" enctype="multipart/form-data" class="small mb-3">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Nama Instansi *</label>
                    <input type="text" name="nama_instansi" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jabatan *</label>
                    <input type="text" name="jabatan" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Mulai</label>
                    <input type="number" name="tahun_mulai" class="form-control form-control-sm" placeholder="2018">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Selesai</label>
                    <input type="number" name="tahun_selesai" class="form-control form-control-sm" placeholder="2020">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control form-control-sm" placeholder="Opsional">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dokumen</label>
                    <input type="file" name="dokumen" class="form-control form-control-sm">
                </div>
            </div>
            <div class="mt-2 d-flex justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary">
                    + Tambah Pekerjaan
                </button>
            </div>
        </form>

        {{-- TABEL LIST --}}
        <div class="table-responsive small">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Nama Instansi</th>
                        <th>Jabatan</th>
                        <th style="width:100px;">Mulai</th>
                        <th style="width:100px;">Selesai</th>
                        <th>Keterangan</th>
                        <th>Dokumen</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pekerjaan as $i => $row)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            {{-- Form Update Inline --}}
                            <form action="{{ route('perawat.pekerjaan.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                {{-- Gunakan hidden fields atau bungkus form di luar td jika ingin lebih rapi,
                                     tapi metode pendidikan sebelumnya membungkus per input --}}
                            </form>

                            {{-- Agar sesuai template pendidikan user, kita masukkan form per baris --}}
                            <td colspan="7" class="p-0">
                                <form action="{{ route('perawat.pekerjaan.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td style="width: 20%;">
                                                <input type="text" name="nama_instansi" value="{{ $row->nama_instansi }}" class="form-control form-control-sm">
                                            </td>
                                            <td style="width: 20%;">
                                                <input type="text" name="jabatan" value="{{ $row->jabatan }}" class="form-control form-control-sm">
                                            </td>
                                            <td style="width: 10%;">
                                                <input type="number" name="tahun_mulai" value="{{ $row->tahun_mulai }}" class="form-control form-control-sm">
                                            </td>
                                            <td style="width: 10%;">
                                                <input type="number" name="tahun_selesai" value="{{ $row->tahun_selesai }}" class="form-control form-control-sm">
                                            </td>
                                            <td style="width: 20%;">
                                                <input type="text" name="keterangan" value="{{ $row->keterangan }}" class="form-control form-control-sm">
                                            </td>
                                            <td style="width: 20%;">
                                                 <div class="d-flex flex-column gap-1">
                                                    <input type="file" name="dokumen" class="form-control form-control-sm">
                                                    @if($row->dokumen_path)
                                                        <a href="{{ asset('storage/'.$row->dokumen_path) }}" target="_blank" class="text-decoration-none">
                                                            <i class="bi bi-file-earmark-text"></i> Lihat File
                                                        </a>
                                                    @else
                                                        <span class="text-muted text-xs">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                {{-- Tombol Simpan (Hidden submit button for enter key functionality if needed) --}}
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- Tombol Save milik form di atas --}}
                                    <button type="button" onclick="this.closest('tr').querySelector('form').submit()" class="btn btn-sm btn-outline-primary">
                                        Simpan
                                    </button>

                                    {{-- Form Delete Terpisah --}}
                                </form>
                                <form action="{{ route('perawat.pekerjaan.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data pekerjaan ini?');">
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
                            <td colspan="8" class="text-center text-muted py-3">
                                Belum ada riwayat pekerjaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
