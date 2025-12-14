@extends('layouts.app')

@section('title', 'Mengerjakan Ujian: ' . $form->judul)

@push('styles')
    <style>
        /* Sticky Header untuk Timer & Navigasi */
        .exam-sticky-header {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Styling Soal */
        .question-card {
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            margin-bottom: 24px;
            transition: all 0.2s;
        }

        .question-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .question-number {
            width: 35px;
            height: 35px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        /* Styling Radio Button Custom */
        .option-label {
            display: block;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
        }

        .option-label:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        /* Saat radio checked */
        .btn-check:checked+.option-label {
            background-color: #eff6ff;
            border-color: #2563eb;
            color: #1e40af;
            box-shadow: 0 0 0 1px #2563eb;
        }
    </style>
@endpush

@section('content')

    {{-- 1. Sticky Header (Info & Timer) --}}
    <div class="exam-sticky-header py-3 mb-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-truncate" style="max-width: 300px;">{{ $form->judul }}</h6>
                    <small class="text-muted">Total {{ $questions->count() }} Soal</small>
                </div>

                {{-- Timer Countdown --}}
                <div
                    class="bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-stopwatch"></i>
                    <span id="countdown-timer">--:--:--</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <form action="{{ route('perawat.ujian.submit', $form->slug) }}" method="POST" id="examForm">
                    @csrf

                    @forelse($questions as $index => $soal)
                        <div class="card question-card bg-white shadow-sm p-4">
                            {{-- Header Soal --}}
                            <div class="d-flex gap-3 mb-3">
                                <div class="question-number">{{ $index + 1 }}</div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark lh-base mb-0">
                                        {{ $soal->pertanyaan }}
                                    </h6>
                                </div>
                            </div>

                            {{-- Opsi Jawaban --}}
                            <div class="d-flex flex-column gap-2 ms-md-5">
                                @php
                                    $opsi = $soal->opsi_jawaban;
                                    $shuffledOpsi = collect($opsi)->shuffle();
                                @endphp

                                @foreach ($shuffledOpsi as $key => $value)
                                    <div>
                                        <input type="radio" class="btn-check" name="answers[{{ $soal->id }}]"
                                            id="q_{{ $soal->id }}_{{ $key }}" value="{{ $key }}"
                                            autocomplete="off">

                                        <label class="option-label d-flex align-items-start gap-2"
                                            for="q_{{ $soal->id }}_{{ $key }}">
                                            <div class="flex-grow-1">{{ $value }}</div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i> Belum ada soal yang dimasukkan ke ujian ini.
                        </div>
                    @endforelse

                    @if ($questions->isNotEmpty())
                        <div class="d-grid gap-2 mt-5 mb-5">
                            <button type="submit" class="btn btn-primary btn-lg shadow fw-bold p-3"
                                onclick="return confirm('Apakah Anda yakin ingin mengumpulkan jawaban? Aksi ini tidak dapat dibatalkan.')">
                                <i class="bi bi-send-fill me-2"></i> Kumpulkan Jawaban
                            </button>
                        </div>
                    @endif
                </form>

            </div>
        </div>
    </div>

    {{-- Script Timer Sederhana --}}
    <script>
        // Set waktu selesai dari server
        var countDownDate = new Date("{{ $form->waktu_selesai->format('M d, Y H:i:s') }}").getTime();

        var x = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown-timer").innerHTML = hours + "j " + minutes + "m " + seconds + "d ";

            if (distance < 0) {
                clearInterval(x);
                document.getElementById("countdown-timer").innerHTML = "WAKTU HABIS";
                document.getElementById("examForm").submit();
            }
        }, 1000);
    </script>
@endsection
