@extends('layouts.app')

@section('title','Dashboard Perawat - DIK SERA')

@section('sidebar')
    <div class="sidebar-title">Perawat</div>
    <div class="sidebar-menu">
        <a href="{{ route('perawat.dashboard') }}" class="active">
            <i class="bi bi-house"></i><span>Dashboard</span>
        </a>
        {{-- nanti bisa tambah menu lain untuk perawat --}}
    </div>
@endsection

@section('content')
<div class="card-glass mb-3">
    <div class="card-glass-inner">
        <div class="section-title">Hai, {{ $user->name }}</div>
        <p class="text-muted small mb-3">
            Selamat datang di DIK SERA — pantau sertifikat kompetensi dan pengembanganmu di sini.
        </p>

        <div class="row g-3">
            <div class="col-md-4 col-6">
                <div class="p-3 rounded-3 border bg-white h-100">
                    <div class="text-muted small mb-1">Total Sertifikat</div>
                    <div class="fs-4 fw-semibold">{{ $total }}</div>
                    <div class="text-muted small"><i class="bi bi-file-earmark-medical"></i> dimiliki</div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="p-3 rounded-3 border bg-white h-100">
                    <div class="text-muted small mb-1">Sertifikat Aktif</div>
                    <div class="fs-4 fw-semibold">{{ $aktif }}</div>
                    <div class="text-muted small"><i class="bi bi-check-circle"></i> masih berlaku</div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="p-3 rounded-3 border bg-white h-100">
                    <div class="text-muted small mb-1">Akan Habis ≤ 3 Bulan</div>
                    <div class="fs-4 fw-semibold">{{ $akanHabis }}</div>
                    <div class="text-muted small"><i class="bi bi-exclamation-triangle"></i> butuh perpanjangan</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- kalau nanti mau, di bawah bisa ditambah tabel list sertifikat milik perawat --}}
@endsection
