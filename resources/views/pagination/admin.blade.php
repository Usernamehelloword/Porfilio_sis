@if ($paginator->hasPages())
    <nav class="pagination">
        @if ($paginator->onFirstPage())
            <span class="pg disabled">‹</span>
        @else
            <a class="pg" href="{{ $paginator->previousPageUrl() }}">‹</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pg disabled">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pg is-current">{{ $page }}</span>
                    @else
                        <a class="pg" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pg" href="{{ $paginator->nextPageUrl() }}">›</a>
        @else
            <span class="pg disabled">›</span>
        @endif
    </nav>
@endif
