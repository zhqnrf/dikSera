@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Atur Soal Ujian</h5>
            <p class="text-muted mb-0">Form: <strong>{{ $form->judul }}</strong></p>
        </div>
        <a href="{{ route('admin.form.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <form action="{{ route('admin.form.simpan-soal', $form->id) }}" method="POST">
        @csrf
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold">Pilih Soal dari Bank Soal</span>
                <span class="badge bg-primary">{{ $allSoals->count() }} Soal Tersedia</span>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="50" class="text-center">
                                    <input type="checkbox" class="form-check-input" id="checkAll">
                                </th>
                                <th>Pertanyaan</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allSoals as $soal)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="soal_ids[]" value="{{ $soal->id }}" 
                                        class="form-check-input soal-checkbox"
                                        {{ in_array($soal->id, $existingSoalIds) ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $soal->pertanyaan }}</div>
                                    <small class="text-muted d-block mt-1">
                                        Kunci: <span class="fw-bold text-success text-uppercase">{{ $soal->kunci_jawaban }}</span>
                                    </small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $soal->kategori }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">Bank soal masih kosong.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white p-3 text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i> Simpan Konfigurasi Soal
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Script simple untuk Check All
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.soal-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection