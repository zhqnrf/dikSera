@extends('layouts.app')

@section('title', 'Data Perawat – Admin DIKSERA')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fab fa-telegram-plane"></i> Integrasi Telegram</h5>
                    </div>
                    <div class="card-body">
                        @if (auth()->user()->telegram_chat_id)
                            <div class="alert alert-success text-center">
                                <h4>✅ Terhubung</h4>
                                <p>Akun ini sudah terhubung dengan Telegram.</p>
                            </div>

                            <div class="d-flex justify-content-between">
                                <form action="{{ route('admin.telegram.test') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-info text-white">
                                        <i class="fas fa-paper-plane"></i> Tes Notifikasi
                                    </button>
                                </form>

                                <form action="{{ route('admin.telegram.unlink') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-unlink"></i> Putuskan Koneksi
                                    </button>
                                </form>
                            </div>
                        @else
                            <div id="step-start">
                                <p>Hubungkan akun untuk menerima notifikasi sistem secara realtime.</p>
                                <button class="btn btn-primary w-100" onclick="generateCode()">
                                    Hubungkan Telegram Saya
                                </button>
                            </div>

                            <div id="step-verify" style="display: none;" class="text-center mt-3">
                                <div class="alert alert-warning">
                                    <small>Kirim kode di bawah ini ke bot Telegram kami:</small>
                                    <h2 class="font-weight-bold my-2" id="display-code">...</h2>
                                    <small>Berlaku sampai: <span id="display-time"></span></small>
                                </div>

                                <a href="#" id="link-bot" target="_blank" class="btn btn-success mb-3">
                                    <i class="fab fa-telegram"></i> Buka Bot Telegram
                                </a>

                                <p class="text-muted text-sm">Setelah mengirim kode, silakan refresh halaman ini.</p>
                                <button onclick="location.reload()" class="btn btn-secondary btn-sm">Cek Status</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generateCode() {
            fetch("{{ route('admin.telegram.generate') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('step-start').style.display = 'none';
                        document.getElementById('step-verify').style.display = 'block';
                        document.getElementById('display-code').innerText = data.code;
                        document.getElementById('display-time').innerText = data.expires_at;
                        document.getElementById('link-bot').href = "https://t.me/" + data.bot_username;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
