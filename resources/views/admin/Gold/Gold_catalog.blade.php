        <!-- resources/views/images/index.blade.php -->
        <!DOCTYPE html>
        <html lang="en">
        <head>
            @include("GoldCatalog.Shared.adminNavBar")
            @include("GoldCatalog.Shared.sideBar")
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Catalog Items</title>
            <link href="{{ asset('css/app.css') }}" rel="stylesheet">
            <link href="{{ asset('css/Gold/three_view.css') }}" rel="stylesheet">
            </head>
            <body>
                <div class="row"> 
                    @foreach ($catalogItems as $item)
                        <div class="item" data-item-id="{{ $item->id }}">
                            <img src="{{ asset($item->Path) }}" alt="Image">
                            <button class="show-details" data-image="{{ asset($item->Path) }}"
                                data-title="{{ $item->FileName }}" 
                                data-average-weight="{{$item->Average_weight}}"
                                data-source="{{$item->Source}}"
                                data-metal-purity="{{$item->Metal_purity}}"
                                data-kind="{{$item->Kind}}">Details</button>
                        </div>
                    @endforeach
                </div> 
                
                @php
                $paginationLinks =  $catalogItems->links('pagination::bootstrap-4');
            @endphp
              {{$paginationLinks}}
                <div class="full-screen-image">
                
                        <img id="full-screen-img" src="" alt="Full Screen Image">
                        <p id="image-details"></p>
                        <button class="closeBtn" id="close-full-screen">Close</button>
                </div>

                <script>
                    document.querySelectorAll('.show-details').forEach(button => {
                        button.addEventListener('click', () => {
                            const imgSrc = button.getAttribute('data-image');
                            const title = button.getAttribute('data-title');
                            const averageWeight = button.getAttribute('data-average-weight');
                            const source = button.getAttribute('data-source');
                            const metalPurity = button.getAttribute('data-metal-purity');
                            const kind = button.getAttribute('data-kind');
                            document.getElementById('full-screen-img').src = imgSrc;
                        //Details how it display
                            document.getElementById('image-details').innerHTML = `
                                <p><span class="title">ModelName:</span><span class="details1"> ${title}</span></p>
                                <p><span class="title">Average Weight:</span><span class="details2"> ${averageWeight}</span></p>
                                <p><span class="title">Source:</span><span class="details3">${source}</span></p>
                                <p><span class="title">Metal Purity:</span><span class="details4"> ${metalPurity}</span></p>
                                <p><span class="title">Kind:</span><span class="details5"> ${kind}</span></p>
                            `;
                            document.querySelector('.full-screen-image').style.display = 'flex';
                        });
                    });
                
                    document.getElementById('close-full-screen').addEventListener('click', () => {
                        document.querySelector('.full-screen-image').style.display = 'none';
                    });
                </script>
                </body>
        </html>                