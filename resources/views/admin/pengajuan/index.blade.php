@extends('layouts.app')

@section('title', 'Admin - Approval Perpanjangan')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --input-height: 38px; /* Tinggi input compact */
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            font-size: 13.5px; /* Font compact */
            padding-bottom: 100px; /* Space untuk floating bar */
        }

        /* --- Page Header --- */
        .page-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }
        .page-subtitle {
            font-size: 0.85rem;
            color: var(--text-gray);
            margin-top: 2px;
            margin-bottom: 0;
        }

        /* --- Filter Card --- */
        .filter-card {
            background: white;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .filter-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-gray);
            text-transform: uppercase;
            margin-bottom: 4px;
            display: block;
        }

        /* Input & Choices Alignment */
        .form-control {
            height: var(--input-height);
            border-radius: 6px;
            font-size: 0.9rem;
            border-color: #cbd5e1;
        }
        .choices__inner {
            min-height: var(--input-height) !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            background: white;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            display: flex; align-items: center;
        }
        .choices { margin-bottom: 0; }
        .choices__list--single { padding: 0; }
        
        /* --- Table Styling --- */
        .table-card {
            background: white;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: 0 2px 4px -1px rgba(0,0,0,0.05);
        }
        .table-custom th {
            background: #f8fafc;
            color: var(--text-gray);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            padding: 10px 16px;
            border-bottom: 1px solid var(--border-color);
        }
        .table-custom td {
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tbody tr:hover { background-color: #f8fafc; }

        /* --- Components --- */
        .avatar-initial {
            width: 30px; height: 30px;
            background-color: #eff6ff; color: var(--primary-blue);
            font-weight: 700; font-size: 11px;
            border-radius: 6px; display: flex; align-items: center; justify-content: center;
            border: 1px solid #dbeafe; flex-shrink: 0; margin-right: 10px;
        }

        /* Status Badge */
        .status-badge {
            padding: 3px 8px; border-radius: 6px;
            font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .st-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .st-info { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
        .st-success { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .st-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        /* Action Buttons (Individual Row) */
        .btn-icon {
            width: 28px; height: 28px;
            border-radius: 6px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid transparent; transition: all 0.2s; font-size: 0.85rem;
            cursor: pointer;
        }
        .btn-view { color: #64748b; background: transparent; border: 1px solid #e2e8f0; }
        .btn-view:hover { background: #f1f5f9; color: #0f172a; }

        .btn-act-check { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .btn-act-check:hover { background: #15803d; color: white; border-color: #15803d; }

        .btn-act-x { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .btn-act-x:hover { background: #991b1b; color: white; border-color: #991b1b; }

        .btn-act-input { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
        .btn-act-input:hover { background: #0369a1; color: white; border-color: #0369a1; }

        /* --- Floating Bulk Action Bar --- */
        .bulk-action-bar {
            position: fixed; bottom: 30px; left: 50%;
            transform: translateX(-50%) translateY(150%);
            background: #1e293b; color: white;
            padding: 10px 20px; border-radius: 50px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
            z-index: 1050; display: flex; align-items: center; gap: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: 95%; width: max-content;
        }
        .bulk-action-bar.active { transform: translateX(-50%) translateY(0); }
        
        .bulk-separator { width: 1px; height: 20px; background: #475569; margin: 0 5px; }
    </style>
@endpush

@section('content')
    <div class="container py-4">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Approval Perpanjangan</h1>
                <p class="page-subtitle">Manajemen validasi lisensi dan sertifikat perawat.</p>
            </div>
            {{-- Statistik Sederhana --}}
            <div class="d-none d-md-flex gap-3">
                <div class="px-3 py-1 bg-white border rounded-3 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-hourglass-split text-warning"></i>
                    <span class="small fw-bold">{{ $pengajuan->where('status', 'pending')->count() }} Pending</span>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success mb-4 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        {{-- FILTER SECTION --}}
        <div class="filter-card">
            <form action="{{ route('admin.pengajuan.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label">Cari Data</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama / Email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <label class="filter-label">Status</label>
                        <select name="status" id="choice-status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="method_selected" {{ request('status') == 'method_selected' ? 'selected' : '' }}>Sedang Ujian</option>
                            <option value="exam_passed" {{ request('status') == 'exam_passed' ? 'selected' : '' }}>Lulus Ujian</option>
                            <option value="interview_scheduled" {{ request('status') == 'interview_scheduled' ? 'selected' : '' }}>Jadwal Wawancara</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <label class="filter-label">Jenis Sertifikat</label>
                        <select name="sertifikat" id="choice-sertifikat">
                            <option value="">Semua Jenis</option>
                            @foreach ($listSertifikat as $sertif)
                                <option value="{{ $sertif }}" {{ request('sertifikat') == $sertif ? 'selected' : '' }}>{{ $sertif }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="filter-label">Status Ujian</label>
                        <select name="ujian" id="choice-ujian">
                            <option value="">Semua</option>
                            <option value="sudah" {{ request('ujian') == 'sudah' ? 'selected' : '' }}>Sudah Ada Nilai</option>
                            <option value="belum" {{ request('ujian') == 'belum' ? 'selected' : '' }}>Belum Ujian</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold btn-sm" style="height: 38px;">Filter</button>
                        @if (request()->anyFilled(['search', 'status', 'sertifikat', 'ujian']))
                            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-light border btn-sm d-flex align-items-center justify-content-center" style="height: 38px; width: 38px;" title="Reset">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLE SECTION --}}
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input" style="cursor: pointer;">
                            </th>
                            <th>Perawat</th>
                            <th>Sertifikat</th>
                            <th>Status</th>
                            <th>Metode</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                            <tr>
                                <td class="text-center">
                                    {{-- Checkbox hanya muncul jika status memungkinkan bulk action --}}
                                    @if (in_array($item->status, ['pending', 'method_selected', 'interview_scheduled']))
                                        <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="form-check-input check-item" style="cursor: pointer;">
                                    @else
                                        <input type="checkbox" disabled class="form-check-input opacity-25">
                                    @endif
                                </td>
                                
                                {{-- Kolom Perawat (Avatar) --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php $initials = collect(explode(' ', $item->user->name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join(''); @endphp
                                        <div class="avatar-initial">{{ $initials }}</div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->user->name }}</div>
                                            <div class="text-muted small" style="font-size: 11px;">{{ $item->user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td><span class="text-dark">{{ $item->lisensiLama->nama ?? '-' }}</span></td>

                                <td>
                                    @if ($item->status == 'pending')
                                        <span class="status-badge st-pending"><i class="bi bi-clock"></i> Menunggu</span>
                                    @elseif($item->status == 'method_selected')
                                        <span class="status-badge st-info"><i class="bi bi-pencil-square"></i> Sedang Ujian</span>
                                    @elseif($item->status == 'exam_passed')
                                        <span class="status-badge st-info"><i class="bi bi-check-circle"></i> Lulus Ujian</span>
                                    @elseif($item->status == 'interview_scheduled')
                                        <span class="status-badge st-info"><i class="bi bi-calendar-event"></i> Wawancara</span>
                                    @elseif($item->status == 'completed')
                                        <span class="status-badge st-success"><i class="bi bi-check-all"></i> Selesai</span>
                                    @elseif($item->status == 'rejected')
                                        <span class="status-badge st-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="text-muted small">
                                        {{ $item->metode == 'pg_only' ? 'Hanya PG' : ($item->metode ? 'PG + Wawancara' : '-') }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    {{-- INDIVIDUAL ACTIONS GROUP (DIPERTAHANKAN) --}}
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.pengajuan.show', $item->id) }}" class="btn-icon btn-view" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if ($item->status == 'pending')
                                            <form action="{{ route('admin.pengajuan.approve', $item->id) }}" method="POST">
                                                @csrf 
                                                <button class="btn-icon btn-act-check" onclick="return confirm('Setujui Pengajuan?')" title="Setujui">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        
                                        @elseif ($item->status == 'method_selected' && $item->user->examResult)
                                            <a href="{{ route('admin.pengajuan.approve_score', $item->id) }}" class="btn-icon btn-act-check" 
                                               onclick="return confirm('Verifikasi Nilai?')" title="Acc Nilai">
                                                <i class="bi bi-check-all"></i>
                                            </a>
                                        
                                        @elseif ($item->status == 'interview_scheduled' && $item->jadwalWawancara)
                                            @php $jadwal = $item->jadwalWawancara; @endphp
                                            
                                            @if ($jadwal->status == 'pending')
                                                <a href="{{ route('admin.pengajuan_wawancara.approve', $jadwal->id) }}" 
                                                   class="btn-icon btn-act-check" onclick="return confirm('Setujui Jadwal?')" title="Acc Jadwal">
                                                    <i class="bi bi-calendar-check"></i>
                                                </a>
                                                {{-- Modal Trigger Reject --}}
                                                <button type="button" class="btn-icon btn-act-x" data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal{{ $jadwal->id }}" title="Tolak Jadwal">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>

                                                {{-- Modal Inline --}}
                                                <div class="modal fade text-start" id="rejectModal{{ $jadwal->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                                        <form action="{{ route('admin.pengajuan_wawancara.reject', $jadwal->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header py-2 border-0"><h6 class="modal-title fw-bold">Tolak Jadwal</h6></div>
                                                                <div class="modal-body py-0">
                                                                    <textarea name="alasan" class="form-control form-control-sm" rows="3" required placeholder="Alasan..."></textarea>
                                                                </div>
                                                                <div class="modal-footer border-0 pt-2">
                                                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-sm btn-danger">Kirim</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            
                                            @elseif($jadwal->status == 'approved')
                                                <a href="{{ route('admin.pengajuan_wawancara.penilaian', $jadwal->id) }}" 
                                                   class="btn-icon btn-act-input" title="Input Nilai">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 opacity-25"></i>
                                    <div class="mt-2 small">Tidak ada data pengajuan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $pengajuan->withQueryString()->links('vendor.pagination.diksera') }}
        </div>
    </div>

    {{-- === FLOATING BULK ACTION BAR (COLLECTIVE ACTION TETAP TERPISAH) === --}}
    <div class="bulk-action-bar" id="bulkActionBar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-dark rounded-pill px-2 py-1" id="selectedCountBadge">0</span>
            <span class="small fw-bold">Terpilih</span>
        </div>
        
        <div class="bulk-separator"></div>

        <div class="d-flex gap-2">
            {{-- Tombol 1: Pending --}}
            <form id="formBulkApprove" action="{{ route('admin.pengajuan.bulk_approve') }}" method="POST">
                @csrf <div id="bulkApproveInputs"></div>
                <button type="button" class="btn btn-success btn-sm fw-bold rounded-pill px-3"
                    onclick="submitBulk('formBulkApprove', 'Setujui semua pengajuan?')">
                    <i class="bi bi-check-lg me-1"></i> Acc Pending
                </button>
            </form>

            {{-- Tombol 2: Nilai --}}
            <form id="formBulkScore" action="{{ route('admin.pengajuan.bulk_approve_score') }}" method="POST">
                @csrf <div id="bulkScoreInputs"></div>
                <button type="button" class="btn btn-warning text-dark btn-sm fw-bold rounded-pill px-3"
                    onclick="submitBulk('formBulkScore', 'Verifikasi nilai?')">
                    <i class="bi bi-check-all me-1"></i> Acc Nilai
                </button>
            </form>

            {{-- Tombol 3: Jadwal --}}
            <form id="formBulkInterview" action="{{ route('admin.pengajuan.bulk_approve_interview') }}" method="POST">
                @csrf <div id="bulkInterviewInputs"></div>
                <button type="button" class="btn btn-info text-white btn-sm fw-bold rounded-pill px-3"
                    onclick="submitBulk('formBulkInterview', 'Setujui jadwal?')">
                    <i class="bi bi-calendar-check me-1"></i> Acc Jadwal
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Init Choices
            const config = { searchEnabled: true, itemSelectText: '', shouldSort: false, placeholder: true };
            new Choices('#choice-status', { ...config, searchEnabled: false });
            new Choices('#choice-sertifikat', config);
            new Choices('#choice-ujian', { ...config, searchEnabled: false });

            // Bulk Logic
            const checkAll = document.getElementById('checkAll');
            const checkItems = document.querySelectorAll('.check-item');
            const bulkActionBar = document.getElementById('bulkActionBar');
            const selectedCountBadge = document.getElementById('selectedCountBadge');

            function updateBulkBar() {
                const checked = document.querySelectorAll('.check-item:checked');
                const count = checked.length;
                selectedCountBadge.innerText = count;
                if (count > 0) bulkActionBar.classList.add('active');
                else bulkActionBar.classList.remove('active');
            }

            checkAll.addEventListener('change', function() {
                checkItems.forEach(item => { if (!item.disabled) item.checked = this.checked; });
                updateBulkBar();
            });

            checkItems.forEach(item => item.addEventListener('change', updateBulkBar));
        });

        function submitBulk(formId, msg) {
            if (!confirm(msg)) return;
            const form = document.getElementById(formId);
            
            // Logic container
            let containerId = 'bulkApproveInputs'; 
            if (formId === 'formBulkScore') containerId = 'bulkScoreInputs';
            if (formId === 'formBulkInterview') containerId = 'bulkInterviewInputs';

            const container = document.getElementById(containerId);
            container.innerHTML = '';
            const checkedItems = document.querySelectorAll('.check-item:checked');

            if(checkedItems.length === 0) { alert('Pilih data dulu!'); return; }
            
            checkedItems.forEach(item => {
                const i = document.createElement('input');
                i.type = 'hidden'; i.name = 'ids[]'; i.value = item.value;
                container.appendChild(i);
            });
            form.submit();
        }
    </script>
@endpush