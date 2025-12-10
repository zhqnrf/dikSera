@php
    $now = \Carbon\Carbon::now();
    $expired = \Carbon\Carbon::parse($item->tgl_expired);
    $daysLeft = $now->diffInDays($expired, false);

    if ($daysLeft < 0) {
        $badgeClass = 'badge-soft-danger';
        $icon = 'bi-x-circle';
        $text = 'Expired (' . abs($daysLeft) . ' hari)';
    } elseif ($daysLeft <= 90) {
        // Warning 3 bulan sebelum
        $badgeClass = 'badge-soft-warning';
        $icon = 'bi-exclamation-circle';
        $text = 'Exp: ' . $daysLeft . ' hari lagi';
    } else {
        $badgeClass = 'badge-soft-success';
        $icon = 'bi-check-circle';
        $text = 'Aktif';
    }
@endphp

<tr>
    <td class="font-monospace text-dark">{{ $item->nomor }}</td>
    <td class="fw-bold text-dark">{{ $item->nama }}</td>
    <td class="text-muted">{{ $item->lembaga }}</td>
    <td>
        <div class="d-flex flex-column" style="font-size: 11px;">
            <span class="text-muted">Mulai: {{ \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') }}</span>
            <span class="{{ $daysLeft < 0 ? 'text-danger fw-bold' : 'text-dark' }}">
                Akhir: {{ $expired->format('d M Y') }}
            </span>
        </div>
    </td>
    <td>
        <span class="badge-soft {{ $badgeClass }}">
            <i class="bi {{ $icon }}"></i> {{ $text }}
        </span>
    </td>
    <td class="text-end">
        @if ($item->file_path)
            <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="btn-file">
                <i class="bi bi-file-earmark-pdf text-danger"></i> Lihat
            </a>
        @else
            <span class="text-muted small opacity-50">Kosong</span>
        @endif
    </td>
</tr>