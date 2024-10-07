<!-- resources/views/diamond_catalog/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    @include("DiamondCatalog.Shared.adminNavBar")
    @include("DiamondCatalog.Shared.sideBar")
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diamond Catalog Items</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ThreeInRow.css') }}" rel="stylesheet">
</head>
<body>
    <div class="row"> 
        @foreach ($catalogItems as $item)
            <div class="item" data-item-id="{{ $item->id }}">
                <div class="container">
                <img src="{{ asset($item->Path) }}" alt="Image">
                 
            </div>
            <div class="Code_name">Code : {{ ($item->CODE) }}</div>
                <button class="show-details" data-image="{{ asset($item->Path) }}"
                    data-title="{{ $item->MODEL }}" 
                    data-kind="{{$item->KIND}}"
                    data-calico-1="{{$item->CALICO_1}}"
                    data-weight-1="{{$item->WEIGHT_1}}"
                    data-calico-2="{{$item->CALICO_2}}"
                    data-number-2="{{$item->NUMBER_2}}"
                    data-weight-2="{{$item->WEIGHT_2}}"
                    data-calico-3="{{$item->CALICO_3}}"
                    data-number-3="{{$item->NUMBER_3}}"
                    data-weight-3="{{$item->WEIGHT_3}}"
                    data-calico-4="{{$item->CALICO_4}}"
                    data-number-4="{{$item->NUMBER_4}}"
                    data-weight-4="{{$item->WEIGHT_4}}"
                    data-calico-5="{{$item->CALICO_5}}"
                    data-number-5="{{$item->NUMBER_5}}"
                    data-weight-5="{{$item->WEIGHT_5}}"
                    data-calico-6="{{$item->CALICO_6}}"
                    data-number-6="{{$item->NUMBER_6}}"
                    data-weight-6="{{$item->WEIGHT_6}}"
                    >Details</button>
                    <button onclick="window.location.href='{{ route('printCertificate', $item->id) }}'" class="print_Cer">Print Certificate</button>                 
                    {{-- <a href="{{ route('certificates.download', ['certificateCode' => $image->certificate_code]) }}" target="_blank">
                        <button>Download Certificate</button>
                    </a> --}}
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
                const kind = button.getAttribute('data-kind');
                const calico1 = button.getAttribute('data-calico-1');
                const weight1 = button.getAttribute('data-weight-1');
                const calico2 = button.getAttribute('data-calico-2');
                const number2 = button.getAttribute('data-number-2');
                const weight2 = button.getAttribute('data-weight-2');
                const calico3 = button.getAttribute('data-calico-3');
                const number3 = button.getAttribute('data-number-3');
                const weight3 = button.getAttribute('data-weight-3');
                const calico4 = button.getAttribute('data-calico-4');
                const number4 = button.getAttribute('data-number-4');
                const weight4 = button.getAttribute('data-weight-4');
                const calico5 = button.getAttribute('data-calico-5');
                const number5 = button.getAttribute('data-number-5');
                const weight5 = button.getAttribute('data-weight-5');
                const calico6 = button.getAttribute('data-calico-6');
                const number6 = button.getAttribute('data-number-6');
                const weight6 = button.getAttribute('data-weight-6');
                const certificatePath = button.getAttribute('data-certificate_path');
            
                document.getElementById('full-screen-img').src = imgSrc;

                // Displaying the data in the image-details section
                document.getElementById('image-details').innerHTML = `
                    <p><span class="title">Model:</span><span class="details1"> ${title}</span></p>
                    <p><span class="title">Kind:</span><span class="details2"> ${kind}</span></p>
                    <p><span class="title">Gold_Color:</span><span class="details3">${calico1}</span></p>
                    <p><span class="title">Gold_Weight:</span><span class="details4"> ${weight1}</span></p>
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>.</th>
                                <th>Calico</th>
                                <th>Number</th>
                                <th>Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                               <tr>
                                <td>2</td>
                                <td>${calico2}</td>
                                <td>${number2}</td>
                                <td>${weight2}</td>
                            </tr>
                            <tr>
                                
                                <td>3</td>
                                <td>${calico3}</td>
                                <td>${number3}</td>
                                <td>${weight3}</td>
                            </tr>
                            <tr>
                                
                                <td>4</td>
                                <td>${calico4}</td>
                                <td>${number4}</td>
                                <td>${weight4}</td>
                            </tr>
                            <tr>
                                
                                <td>5</td>
                                <td>${calico5}</td>
                                <td>${number5}</td>
                                <td>${weight5}</td>
                            </tr>
                            <tr>
                                
                                <td>6</td>
                                <td>${calico6}</td>
                                <td>${number6}</td>
                                <td>${weight6}</td>
                            </tr>
                        </tbody>
                    </table>
                
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
