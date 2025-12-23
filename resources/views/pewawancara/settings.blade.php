@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@push('styles')
    <style>
        /* Menggunakan style dari referensi Admin agar tampilan seragam */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft, #e2e8f0);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 32px 24px;
            height: 100%;
        }

        /* Telegram Brand Colors */
        :root {
            --telegram-color: #24A1DE;
            --telegram-bg: #f2faff;
        }

        /* Icon Box Styles */
        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }

        .icon-box.telegram {
            background-color: var(--telegram-bg);
            color: var(--telegram-color);
        }

        .icon-box.success {
            background-color: #dcfce7;
            color: #166534;
        }

        /* OTP Code Display */
        .otp-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .otp-code {
            font-family: 'Courier New', monospace;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 4px;
            color: #1e293b;
            display: block;
        }

        .otp-timer {
            font-size: 12px;
            color: #64748b;
        }

        /* Steps Container */
        .step-container {
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Pengaturan Akun</h4>
                <span class="text-muted small">Kelola notifikasi dan koneksi akun Anda.</span>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="content-card text-center">

                    {{-- KONDISI 1: SUDAH TERHUBUNG --}}
                    @if ($user->telegram_chat_id)
                        <div class="py-4 animate__animated animate__fadeIn">
                            <div class="icon-box success mb-4">
                                <i class="bi bi-check-lg"></i>
                            </div>

                            <h4 class="fw-bold mb-2 text-dark">Terhubung!</h4>
                            <p class="text-muted mb-4 small px-4">
                                Akun Anda sudah terhubung dengan Telegram.<br>
                                Notifikasi jadwal akan dikirim secara otomatis.
                            </p>

                            {{-- Info ID Chat (Opsional) --}}
                            <div class="mb-4">
                                <span class="badge bg-light text-dark border fw-normal font-monospace px-3 py-2">
                                    ID: {{ $user->telegram_chat_id }}
                                </span>
                            </div>

                            <form action="{{ route('pewawancara.telegram.unlink') }}" method="POST"
                                onsubmit="return confirm('Yakin ingin memutus sambungan Telegram? Anda tidak akan menerima notifikasi lagi.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger px-4 shadow-sm"
                                    style="border-radius: 8px;">
                                    <i class="bi bi-link-45deg me-1"></i> Putuskan Sambungan
                                </button>
                            </form>
                        </div>

                        {{-- KONDISI 2: BELUM TERHUBUNG --}}
                    @else
                        {{-- STEP 1: TOMBOL MULAI --}}
                        <div id="step-start" class="step-container py-3">
                            <div class="icon-box telegram mb-3">
                                <i class="bi bi-telegram"></i>
                            </div>
                            <h5 class="fw-bold mb-2 text-dark">Hubungkan Telegram</h5>
                            <p class="text-muted small mb-4">
                                Dapatkan notifikasi jadwal wawancara & ujian secara realtime langsung ke aplikasi Telegram
                                Anda.
                            </p>
                            <button id="btn-generate" class="btn btn-primary px-4 py-2 w-100 shadow-sm"
                                style="background-color: var(--telegram-color); border:none; border-radius: 8px;">
                                <i class="bi bi-link me-2"></i> Buat Kode Koneksi
                            </button>
                        </div>

                        {{-- STEP 2: TAMPILKAN KODE (Hidden by default) --}}
                        <div id="step-verify" class="step-container py-3" style="display: none;">
                            <div class="mb-3">
                                <span
                                    class="badge bg-warning text-dark bg-opacity-25 text-opacity-75 px-3 py-2 rounded-pill small">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi
                                </span>
                            </div>

                            <p class="small text-muted mb-0">Salin & kirim kode berikut ke Bot Telegram kami:</p>

                            {{-- Area Kode OTP --}}
                            <div class="otp-box">
                                <span id="display-code" class="otp-code">---</span>
                                <div class="mt-2 border-top pt-2">
                                    <span class="otp-timer">Berlaku sampai: <span id="display-time"
                                            class="fw-bold text-dark">-</span></span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                {{-- Ganti @NamaBotAnda dengan username bot telegram aslimu --}}
                                <a href="https://t.me/NamaBotRSUD_bot" target="_blank" class="btn btn-primary shadow-sm"
                                    style="background-color: var(--telegram-color); border:none; border-radius: 8px;">
                                    <i class="bi bi-telegram me-2"></i> Buka Bot & Kirim Kode
                                </a>

                                <button onclick="location.reload()" class="btn btn-light text-muted border"
                                    style="border-radius: 8px;">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Cek Status Koneksi
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btnGenerate = document.getElementById('btn-generate');
                const stepStart = document.getElementById('step-start');
                const stepVerify = document.getElementById('step-verify');
                const displayCode = document.getElementById('display-code');
                const displayTime = document.getElementById('display-time');

                if (btnGenerate) {
                    btnGenerate.addEventListener('click', function() {
                        // 1. Ubah tombol jadi loading state
                        const originalText = btnGenerate.innerHTML;
                        btnGenerate.innerHTML =
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
                        btnGenerate.disabled = true;

                        // 2. Request AJAX ke Controller
                        fetch("{{ route('pewawancara.telegram.generate-code') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // 3. Sukses: Pindah ke Step 2
                                    stepStart.style.display = 'none';
                                    stepVerify.style.display = 'block';

                                    // Tampilkan Data
                                    displayCode.innerText = data.code;
                                    displayTime.innerText = data.expires_at;

                                    // Mulai polling status otomatis (Opsional)
                                    startPollingStatus();
                                } else {
                                    // Gagal
                                    alert('Gagal membuat kode: ' + (data.message ||
                                        'Error tidak diketahui'));
                                    resetBtn();
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Terjadi kesalahan koneksi.');
                                resetBtn();
                            });

                        function resetBtn() {
                            btnGenerate.innerHTML = originalText;
                            btnGenerate.disabled = false;
                        }
                    });
                }

                function startPollingStatus() {
                    let checks = 0;
                    const maxChecks = 60; // Stop checking after 5 minutes (60 * 5s)

                    const interval = setInterval(() => {
                        checks++;
                        if (checks > maxChecks) clearInterval(interval);
                    }, 5000);
                }
            });
        </script>
    @endpush
@endsection
