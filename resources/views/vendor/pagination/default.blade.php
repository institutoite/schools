@if ($paginator->hasPages())
    <nav>
        <div style="overflow-x:auto; width:100%; max-width:100vw;">
            <ul class="pagination d-flex flex-row flex-nowrap justify-content-center align-items-center gap-1" style="font-size: 11px; white-space: nowrap; display: flex; flex-direction: row; flex-wrap: nowrap;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="Anterior">
                    <span class="page-link btn btn-outline-secondary btn-sm px-1 py-0" aria-hidden="true">&laquo; Anterior</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link btn btn-outline-primary btn-sm px-1 py-0" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">&laquo; Anterior</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link btn btn-outline-secondary btn-sm px-1 py-0">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link btn btn-primary btn-sm text-white px-1 py-0">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link btn btn-outline-primary btn-sm px-1 py-0" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link btn btn-outline-primary btn-sm px-1 py-0" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente">Siguiente &raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Siguiente">
                    <span class="page-link btn btn-outline-secondary btn-sm px-1 py-0" aria-hidden="true">Siguiente &raquo;</span>
                </li>
            @endif
            </ul>
        </div>
        <div class="pagination-info text-xs mt-2 text-secondary-600">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </div>
    </nav>
@endif
