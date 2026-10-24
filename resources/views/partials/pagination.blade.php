{{-- Pagination sederhana. Pemakaian: @include('partials.pagination', ['p' => $books]) --}}
@if ($p->hasPages())
    <div class="pager">
        @if ($p->onFirstPage()) <span class="off">&laquo; Sebelumnya</span> @else <a href="{{ $p->previousPageUrl() }}">&laquo; Sebelumnya</a> @endif
        <span class="cur">{{ $p->currentPage() }} / {{ $p->lastPage() }}</span>
        @if ($p->hasMorePages()) <a href="{{ $p->nextPageUrl() }}">Berikutnya &raquo;</a> @else <span class="off">Berikutnya &raquo;</span> @endif
    </div>
@endif
