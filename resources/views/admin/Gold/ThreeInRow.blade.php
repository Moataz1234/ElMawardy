<!DOCTYPE html>
<html lang="en">
<head>
    @include('dashboard')

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('CSS/first_page.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/ThreeInRow.css') }}">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8kNq7/8z2zVw5U5NAuTp6WVsMSXJ1pO9aX1l" crossorigin="anonymous">
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">

    <title>Document</title>
</head>
<body>

<div class="product-grid">
    @foreach($catalogItems as $catalogItem)
        <div class="product-card">
            <div class="product-image" style="background-image: url('{{ asset( $catalogItem->link)  }} ');" alt="Image" ></div>
            <div class="product-name">{{ $catalogItem->model }}</div>
            <div class="button-container">
                <button class="show-details" data-image="{{ asset($catalogItem->link) }}"
                    data-title="{{ $catalogItem->model }}" 
                    data-weight="{{$catalogItem->weight}}"
                    data-source="{{$catalogItem->source}}"
                    data-metal-purity="{{$catalogItem->metal_purity}}"
                    data-kind="{{$catalogItem->kind}}">
                    Details</button>
                {{-- <button class="btn print-btn">Print Certificate</button> --}}
            </div>
        </div>
    @endforeach
</div>
   {{ $catalogItems->links('pagination::bootstrap-4') }}
   <div class="full-screen-image">
                
    <img id="full-screen-img" src="" alt="Full Screen Image">
    <p id="image-details"></p>
    <button class="closeBtn" id="close-full-screen">Close</button>
</div>
</div>
</div>

   
    <script>
        document.querySelectorAll('.show-details').forEach(button => {
            button.addEventListener('click', () => {
                const imgSrc = button.getAttribute('data-image');
                const title = button.getAttribute('data-title');
                const weight = button.getAttribute('data-weight');
                const source = button.getAttribute('data-source');
                const metalPurity = button.getAttribute('data-metal-purity');
                const kind = button.getAttribute('data-kind');
                document.getElementById('full-screen-img').src = imgSrc;
            //Details how it display
                document.getElementById('image-details').innerHTML = `
                    <p><span class="title">ModelName:</span><span class="details1"> ${title}</span></p>
                    <p><span class="title"> Weight:</span><span class="details2"> ${weight}</span></p>
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
