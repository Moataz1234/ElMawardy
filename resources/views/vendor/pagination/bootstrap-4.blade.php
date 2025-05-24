@if ($paginator->hasPages())
    <nav class="d-flex justify-items-center justify-content-between">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.previous')</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.next')</span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <p class="small text-muted">
                    {!! __('Showing') !!}
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="fw-semibold">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <ul class="pagination pagination-sm mb-0" style="--bs-pagination-padding-x: 0.75rem; --bs-pagination-padding-y: 0.375rem; --bs-pagination-font-size: 0.875rem; --bs-pagination-border-radius: 6px;">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 6px; margin: 0 2px;">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" style="border: 1px solid #e9ecef; color: #667eea; font-weight: 500; border-radius: 6px; margin: 0 2px; transition: all 0.15s ease-in-out;" onmouseover="this.style.background='#667eea'; this.style.borderColor='#667eea'; this.style.color='white'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 2px 4px rgba(102, 126, 234, 0.25)';" onmouseout="this.style.background=''; this.style.borderColor='#e9ecef'; this.style.color='#667eea'; this.style.transform=''; this.style.boxShadow='';">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link" style="border: 1px solid #e9ecef; color: #6c757d; border-radius: 6px; margin: 0 2px;">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page"><span class="page-link" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-color: #667eea; color: white; box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3); border-radius: 6px; margin: 0 2px;">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $url }}" style="border: 1px solid #e9ecef; color: #667eea; font-weight: 500; border-radius: 6px; margin: 0 2px; transition: all 0.15s ease-in-out;" onmouseover="this.style.background='#667eea'; this.style.borderColor='#667eea'; this.style.color='white'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 2px 4px rgba(102, 126, 234, 0.25)';" onmouseout="this.style.background=''; this.style.borderColor='#e9ecef'; this.style.color='#667eea'; this.style.transform=''; this.style.boxShadow='';">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" style="border: 1px solid #e9ecef; color: #667eea; font-weight: 500; border-radius: 6px; margin: 0 2px; transition: all 0.15s ease-in-out;" onmouseover="this.style.background='#667eea'; this.style.borderColor='#667eea'; this.style.color='white'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 2px 4px rgba(102, 126, 234, 0.25)';" onmouseout="this.style.background=''; this.style.borderColor='#e9ecef'; this.style.color='#667eea'; this.style.transform=''; this.style.boxShadow='';">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 6px; margin: 0 2px;">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
