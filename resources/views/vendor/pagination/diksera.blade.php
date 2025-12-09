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
        display: block;
        padding: 6px 12px;
        font-size: 13px;
        border-radius: 8px;
        border: 1px solid var(--border-soft);
        background: #fff;
        color: var(--text-main);
        text-decoration: none;
        transition: all .2s;
    }

    .pagination-list li a:hover {
        background: var(--blue-soft);
        color: var(--blue-main);
        border-color: var(--blue-main);
    }

    .pagination-list li.active span {
        background: var(--blue-main);
        border-color: var(--blue-main);
        color: #fff;
        font-weight: 600;
    }

    .pagination-list li.disabled span {
        opacity: 0.4;
        cursor: not-allowed;
    }
</style>
