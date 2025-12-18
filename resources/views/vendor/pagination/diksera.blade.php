@if ($paginator->hasPages())
    <nav class="pagination-wrapper mt-4">
        <ul class="pagination-list">

            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <li class="disabled"><span>‹ Prev</span></li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Prev</a>
                </li>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Tanda ... --}}
                @if (is_string($element))
                    <li class="disabled"><span>{{ $element }}</span></li>
                @endif

                {{-- Link halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next">Next ›</a>
                </li>
            @else
                <li class="disabled"><span>Next ›</span></li>
            @endif

        </ul>
    </nav>
@endif

<style>
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .pagination-list {
        display: flex;
        list-style: none;
        gap: 6px;
        padding: 0;
        margin: 0;
    }

    .pagination-list li a,
    .pagination-list li span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        /* Menyesuaikan border-color UI sebelumnya */
        background: #fff;
        color: #64748b;
        /* Menyesuaikan text-muted UI sebelumnya */
        text-decoration: none;
        transition: all .2s;
    }

    .pagination-list li a:hover {
        background: #eff6ff;
        /* Soft blue */
        color: #2563eb;
        /* Primary blue */
        border-color: #2563eb;
    }

    .pagination-list li.active span {
        background: #2563eb;
        /* Primary blue dari Bank Soal */
        border-color: #2563eb;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    .pagination-list li.disabled span {
        background: #f8fafc;
        color: #cbd5e1;
        border-color: #f1f5f9;
        cursor: not-allowed;
    }

    /* Khusus untuk teks Prev/Next agar sedikit lebih lebar */
    .pagination-list li:first-child a,
    .pagination-list li:first-child span,
    .pagination-list li:last-child a,
    .pagination-list li:last-child span {
        padding: 0 16px;
    }
</style>
