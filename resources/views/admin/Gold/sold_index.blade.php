@extends('layouts.sold_table')

@section('content')
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-button active" data-tab="items">Gold Items</button>
            <button class="tab-button" data-tab="pounds">Gold Pounds</button>
        </div>

        <div class="tab-content" id="items-tab">
            @include('profile.partials.admin.Sold_list')
        </div>

        <div class="tab-content" id="pounds-tab" style="display: none;">
            @include('profile.partials.admin.pounds_sold_list')
        </div>
    </div>

    <style>
        .tabs-container {
            padding: 20px;
        }
        .tabs {
            margin-bottom: 20px;
        }
        .tab-button {
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            background: #f0f0f0;
            cursor: pointer;
        }
        .tab-button.active {
            background: #007bff;
            color: white;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all tab contents
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.style.display = 'none';
                    });
                    
                    // Show selected tab content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').style.display = 'block';
                });
            });
        });
    </script>
@endsection

@push('modals')
    @include('profile.partials._image_modal')
@endpush

@push('scripts')
{{-- <script>
    const sellRouteUrl = "{{ route('shop-items.bulkSellForm') }}";
    const transferRouteUrl = "{{ route('shop-items.bulkTransferForm') }}";
</script> --}}
<script src="{{ asset('js/modal.js') }}"></script>
<script src="{{ asset('js/checkbox-selection.js') }}"></script>
@endpush
