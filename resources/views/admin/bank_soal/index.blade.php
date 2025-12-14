@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Bank Soal</h4>

            <a href="{{ route('admin.bank-soal.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Soal
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Pertanyaan</th>
                            <th>Kategori</th>
                            <th>Kunci</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($soals as $soal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <div class="fw-bold text-truncate" style="max-width:400px">
                                        {{ $soal->pertanyaan }}
                                    </div>
                                    <small class="text-muted">
                                        A: {{ Str::limit($soal->opsi_jawaban['a'], 20) }} |
                                        B: {{ Str::limit($soal->opsi_jawaban['b'], 20) }} ...
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $soal->kategori }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-success text-uppercase">
                                        {{ $soal->kunci_jawaban }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('admin.bank-soal.edit', $soal->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('admin.bank-soal.delete', $soal->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus soal ini?');">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Belum ada soal
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
