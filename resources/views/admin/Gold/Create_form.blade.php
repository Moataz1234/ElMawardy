<!DOCTYPE html>
 <html lang="en">
 <head>
    @include('components.navbar')
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Create Gold Item</title>
     <link href="{{ asset('css/app.css') }}" rel="stylesheet">
     <link href="{{ asset('css/style.css') }}" rel="stylesheet">
     <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
     <link href="{{ asset('css/form.css') }}" rel="stylesheet">
 </head>
 <body>
    <form class="custom-form" action="{{ route('gold-items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="shop_id">Shop:</label>
                <select name="shop_id" id="shop_id" required>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- <div class="form-group">
                <label for="link">Upload Image:</label>
                <input type="file" name="link" id="link" accept="image/*">
            </div> --}}
            <div class="form-group">
                <label for="kind">Gold Color:</label>
                <select name="kind" id="kind" required>
                    @foreach($kinds as $kind)
                        <option value="{{ $kind }}">{{ $kind }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="model">Model:</label>
                <input list="models" name="model" id="model" required>
                <datalist id="models">
                    @foreach($models as $model)
                        <option value="{{ $model->model }}"></option>
                    @endforeach
                </datalist>
            </div>
            
            <div class="form-group">
                <label for="gold_color">Gold Color:</label>
                <select name="gold_color" id="gold_color" required>
                    @foreach($goldColors as $color)
                        <option value="{{ $color }}">{{ $color }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="metal_type">Metal Type:</label>
                <select name="metal_type" id="metal_type" required>
                    @foreach($metalTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="metal_purity">Metal Purity:</label>
                <select name="metal_purity" id="metal_purity" required>
                    @foreach($metalPurities as $purity)
                        <option value="{{ $purity }}">{{ $purity }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" value="1" required>
            </div>
            <div class="form-group">
                <label for="weight">Weight:</label>
                <input type="number" step="0.01" name="weight" id="weight" required>
            </div>
            {{-- <div class="form-group">
                <label for="source">Source:</label>
                <input type="text" name="source" id="source" required>
            </div> --}}
        </div>
        <button type="submit">Create Gold Item</button>
    </form>
 </body>
 </html>
