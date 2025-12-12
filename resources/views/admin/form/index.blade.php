@extends('layouts.app')

@php
    $pageTitle = 'Manajemen Form';
    $pageSubtitle = 'Kelola tautan survei, presensi, dan pengumpulan data lainnya.';
@endphp

@section('title', 'Manajemen Form – Admin DIKSERA')

@push('styles')
    <style>
        /* Card Container */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }

        /* Custom Table */
        .table-custom th {
            background-color: var(--blue-soft-2);
            color: var(--text-main);
            font-weight: 600;
            font-size: 12px;
            border-bottom: 2px solid #dbeafe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            vertical-align: middle;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 12px 16px;
            border-bottom: 1px solid var(--blue-soft-2);
            font-size: 13px;
            color: var(--text-main);
        }

        /* Status Badges (Soft Color) */
        .badge-soft {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-soft-success {
            background: #dcfce7;
            color: #166534;
        }

        /* Publish */
        .badge-soft-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Closed */
        .badge-soft-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        /* Draft */
        .badge-soft-info {
            background: #e0f2fe;
            color: #075985;
        }

        /* Target */

        /* Action Buttons */
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Search Input */
        .search-input {
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            font-size: 13px;
            padding-left: 12px;
        }

        .search-input:focus {
            border-color: var(--blue-main);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }
    </style>
@endpush

@section('content')

    <div class="content-card">

        {{-- Header Tools: Title & Actions --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">

            {{-- Search Bar (Placeholder) --}}
            <form action="" method="GET" class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm search-input" placeholder="Cari judul form..."
                    style="width: 240px;">
                <button class="btn btn-sm btn-light border" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            {{-- Create Button --}}
            <a href="{{ route('admin.form.create') }}" class="btn btn-sm btn-primary px-3 shadow-sm"
                style="border-radius: 8px;">
                <i class="bi bi-plus-lg me-1"></i> Buat Form Baru
            </a>
        </div>

        {{-- Table Content --}}
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Informasi Form</th>
                        <th>Target & Jadwal</th>
                        <th>Status</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($forms as $form)
                        @php
                            $map = [
                                'publish' => [
                                    'class' => 'badge-soft-success',
                                    'icon' => 'bi-check-circle',
                                    'label' => 'Published',
                                ],
                                'closed' => [
                                    'class' => 'badge-soft-danger',
                                    'icon' => 'bi-x-circle',
                                    'label' => 'Closed',
                                ],
                            ];

                            $statusConfig = $map[$form->status] ?? [
                                'class' => 'badge-soft-secondary',
                                'icon' => 'bi-pencil-square',
                                'label' => 'Draft',
                            ];
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-start gap-2">
                                    <div class="bg-light rounded p-2 text-primary">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $form->judul }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 250px;">
                                            {{ $form->deskripsi ?? 'Tidak ada deskripsi' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    {{-- Target Badge --}}
                                    <div>
                                        <span class="badge-soft badge-soft-info py-1" style="font-size: 10px;">
                                            <i class="bi bi-people"></i> {{ ucfirst($form->target_peserta) }}
                                        </span>
                                    </div>
                                    {{-- Jadwal --}}
                                    <div class="text-muted" style="font-size: 11px;">
                                        <div><i class="bi bi-calendar-event me-1"></i>
                                            {{ $form->waktu_mulai->format('d M/y H:i') }}</div>
                                        <div><i class="bi bi-arrow-right me-1"></i>
                                            {{ $form->waktu_selesai->format('d M/y H:i') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft {{ $statusConfig['class'] }}">
                                    <i class="bi {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['label'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">

                                    {{-- Dropdown Settings Status --}}
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-outline-secondary" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false" title="Ubah Status">
                                            <i class="bi bi-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu shadow border-0" style="font-size: 13px;">
                                            <li>
                                                <h6 class="dropdown-header">Ubah Status</h6>
                                            </li>

                                            {{-- Publish --}}
                                            <li>
                                                <form action="{{ route('admin.form.update-status', $form->id) }}"
                                                    method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" name="status" value="publish"
                                                        class="dropdown-item d-flex align-items-center gap-2 {{ $form->status == 'publish' ? 'active' : '' }}">
                                                        <i class="bi bi-check-circle text-success"></i> Publish
                                                    </button>
                                                </form>
                                            </li>

                                            {{-- Draft --}}
                                            <li>
                                                <form action="{{ route('admin.form.update-status', $form->id) }}"
                                                    method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" name="status" value="draft"
                                                        class="dropdown-item d-flex align-items-center gap-2 {{ $form->status == 'draft' ? 'active' : '' }}">
                                                        <i class="bi bi-pencil-square text-secondary"></i> Set Draft
                                                    </button>
                                                </form>
                                            </li>

                                            {{-- Close --}}
                                            <li>
                                                <form action="{{ route('admin.form.update-status', $form->id) }}"
                                                    method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" name="status" value="closed"
                                                        class="dropdown-item d-flex align-items-center gap-2 {{ $form->status == 'closed' ? 'active' : '' }}">
                                                        <i class="bi bi-x-circle text-danger"></i> Close Form
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Edit --}}
                                    <a href="#" class="btn btn-icon btn-outline-warning" title="Edit Detail"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="#" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus form ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-outline-danger" title="Hapus"
                                            data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-clipboard-x display-6 opacity-25"></i>
                                </div>
                                <span class="text-muted small">Belum ada data form yang dibuat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
