{{-- Paginación personalizada - CSS puro, sin Bootstrap --}}
@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Botón Anterior --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span>&laquo; Anterior</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}">&laquo; Anterior</a></li>
        @endif

        {{-- Números de página --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled"><span>{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span>{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Botón Siguiente --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}">Siguiente &raquo;</a></li>
        @else
            <li class="page-item disabled"><span>Siguiente &raquo;</span></li>
        @endif
    </ul>
@endif
