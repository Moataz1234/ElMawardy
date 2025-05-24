@if ($paginator->hasPages())
    <div class="d-flex justify-content-center align-items-center mt-4">
        <div class="pagination-wrapper d-flex align-items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-btn disabled">
                    <i class="bx bx-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn">
                    <i class="bx bx-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @php
                $start = max(1, $paginator->currentPage() - 2);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
            @endphp

            {{-- First Page --}}
            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}" class="pagination-btn">1</a>
                @if ($start > 2)
                    <span class="pagination-dots">...</span>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $paginator->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endfor

            {{-- Last Page --}}
            @if ($end < $paginator->lastPage())
                @if ($end < $paginator->lastPage() - 1)
                    <span class="pagination-dots">...</span>
                @endif
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="pagination-btn">{{ $paginator->lastPage() }}</a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn">
                    <i class="bx bx-chevron-right"></i>
                </a>
            @else
                <span class="pagination-btn disabled">
                    <i class="bx bx-chevron-right"></i>
                </span>
            @endif
        </div>
        
        {{-- Page Info --}}
        <div class="pagination-info ms-4">
            <small class="text-muted">
                Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            </small>
        </div>
    </div>

    <style>
        .pagination-wrapper {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 8px;
            font-size: 14px;
            font-weight: 500;
            color: #667eea;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }
        
        .pagination-btn:hover:not(.disabled):not(.active) {
            background: #667eea;
            border-color: #667eea;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.25);
        }
        
        .pagination-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }
        
        .pagination-btn.disabled {
            color: #6c757d;
            background: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
        }
        
        .pagination-dots {
            color: #6c757d;
            padding: 0 4px;
            font-weight: bold;
        }
        
        .pagination-info {
            font-size: 13px;
        }
        
        @media (max-width: 576px) {
            .pagination-info {
                margin-left: 0 !important;
                margin-top: 8px;
                text-align: center;
            }
            .d-flex.justify-content-center.align-items-center {
                flex-direction: column;
            }
        }
    </style>
@endif 