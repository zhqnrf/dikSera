@extends('layouts.app')

@section('title', 'Ujian Selesai')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">

                {{-- Icon Sukses Animasi --}}
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success display-1"></i>
                </div>

                <h2 class="fw-bold mb-3">Jawaban Terkirim!</h2>
                <p class="text-muted mb-4">
                    Terima kasih telah mengikuti ujian <strong>{{ $form->judul }}</strong>.
                    Jawaban Anda telah berhasil kami simpan ke dalam sistem.
                </p>

                {{-- Kartu Hasil Nilai --}}
                <div class="card border-0 shadow-sm bg-light mb-4 overflow-hidden">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Hasil Ujian Anda</h6>

                        <div class="display-3 fw-bold {{ $result->total_nilai >= 70 ? 'text-success' : 'text-danger' }}">
                            {{ $result->total_nilai }}
                        </div>
                        <div class="small text-muted mb-3">Skor Akhir (0-100)</div>

                        <div class="row g-2 justify-content-center border-top pt-3 mt-3">
                            <div class="col-4 border-end">
                                <div class="fw-bold text-success">{{ $result->total_benar }}</div>
                                <div style="font-size: 10px;">Benar</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-danger">{{ $result->total_salah }}</div>
                                <div style="font-size: 10px;">Salah</div>
                            </div>
                        </div>
                    </div>
                    {{-- Info KKM (Opsional) --}}
                    @if ($result->total_nilai < 70)
                        <div class="card-footer bg-danger bg-opacity-10 text-danger border-0 small py-2">
                            <i class="bi bi-info-circle me-1"></i> Nilai di bawah standar kelulusan (70).
                        </div>
                    @else
                        <div class="card-footer bg-success bg-opacity-10 text-success border-0 small py-2">
                            <i class="bi bi-star-fill me-1"></i> Selamat! Anda Lulus.
                        </div>
                    @endif
                </div>

                <a href="{{ route('perawat.ujian.index') }}" class="btn btn-primary px-5 rounded-pill shadow-sm">
                    Kembali ke Dashboard
                </a>

            </div>
        </div>
    </div>
@endsection
