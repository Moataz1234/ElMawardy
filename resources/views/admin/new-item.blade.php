<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Data Entry Form</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('layouts.navigation')

        <link href="{{ url('css/style.css') }}" rel="stylesheet">
     
    </head>
<body class="new-item-body">
    <p class="new-item-header">Data Entry Form</p>
   
  
</div>
    <!-- Display success message -->
    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form class="new-item-form" action="{{ route('new-item.store') }}" method="POST">
        @csrf

        <!-- KIND -->
        <div class="row" data-index="1">

        <div>
            <label for="kind">Kind:</label>
            <select name="kind" id="kind">
                <option value="" disabled selected>Select kind</option>
                <option value="Pendent" {{ old('kind') == 'type3' ? 'selected' : '' }}>Pendent</option>
                <option value="Bracelet" {{ old('kind') == 'Bracelet' ? 'selected' : '' }}>Bracelet </option>
                <option value="Brooch" {{ old('kind') == 'type2' ? 'selected' : '' }}>Brooch</option>
                <option value="Coupling" {{ old('kind') == 'type3' ? 'selected' : '' }}>Coupling</option>
                <option value="Earing" {{ old('kind') == 'type1' ? 'selected' : '' }}>Earing</option>
                <option value="Necklace" {{ old('kind') == 'type2' ? 'selected' : '' }}>Necklace</option>
                <option value="Ring" {{ old('kind') == 'type1' ? 'selected' : '' }}>Ring </option>
                <option value="Tie-Klip" {{ old('kind') == 'type3' ? 'selected' : '' }}>Tie-Klip</option>
                <option value="Stone" {{ old('kind') == 'type2' ? 'selected' : '' }}>Stone</option>
            </select>           
             @error('kind') <span>{{ $message }}</span> @enderror
        </div>

        <!-- CALICO[1], WEIGHT[1] -->
        <div>
            <label for="calico1">Calico [1]:</label>
            <select name="calico1" id="calico1">
                <option value="" disabled selected>Select Calico1</option>
                <option value="G" {{ old('calico1') == 'type1' ? 'selected' : '' }}>G </option>
                <option value="G.9" {{ old('calico1') == 'type2' ? 'selected' : '' }}>G.9</option>
                <option value="G/0" {{ old('calico1') == 'type3' ? 'selected' : '' }}>G/0</option>
                <option value="G/M" {{ old('calico1') == 'type1' ? 'selected' : '' }}>G/M</option>
                <option value="G/14" {{ old('calico1') == 'type2' ? 'selected' : '' }}>G/14</option>
                <option value="H" {{ old('calico1') == 'type3' ? 'selected' : '' }}>H</option>
                <option value="IT.G" {{ old('calico1') == 'type1' ? 'selected' : '' }}>IT.G </option>
                <option value="IT.G/M" {{ old('calico1') == 'type2' ? 'selected' : '' }}>IT.G/M</option>

                <option value="R.G" {{ old('calico1') == 'type2' ? 'selected' : '' }}>R.G</option>
                <option value="R.G/M" {{ old('calico1') == 'type3' ? 'selected' : '' }}>R.G/M</option>
                <option value="W.G" {{ old('calico1') == 'type3' ? 'selected' : '' }}>W.G</option>
                    <option value="W.G/0" {{ old('calico1') == 'type1' ? 'selected' : '' }}>W.G/0 </option>
                <option value="W.G/M" {{ old('calico1') == 'type2' ? 'selected' : '' }}>W.G/M</option>
                <option value="0" {{ old('calico1') == 'type3' ? 'selected' : '' }}>0</option>
                
            </select>                @error('calico1') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="weight1">Weight [1]:</label>
            <input type="number" step="0.01" name="weight1" id="weight1" value="{{ old('weight1') }}">
            @error('weight1') <span>{{ $message }}</span> @enderror
        </div>
        </div>

        <!-- Repeat for all other fields -->
        <!-- CALICO[2], NUMBER[2], WEIGHT[2] -->
        <div class="row" data-index="2">

        <div>
            <label for="calico2">Calico [2]:</label>
            <input type="text" name="calico2" id="calico2" value="{{ old('calico2') }}">
            @error('calico2') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="number2">Number [2]:</label>
            <input type="number" name="number2" id="number2" value="{{ old('number2') }}">
            @error('number2') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="weight2">Weight [2]:</label>
            <input type="number" step="0.01" name="weight2" id="weight2" value="{{ old('weight2') }}">
            @error('weight2') <span>{{ $message }}</span> @enderror
        </div>
        </div>

        <div class="row" data-index="3">
        <div>
            <label for="calico3">Calico [3]:</label>
            <input type="text" name="calico3" id="calico3" value="{{ old('calico3') }}">
            @error('calico3') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="number3">Number [3]:</label>
            <input type="number" name="number3" id="number3" value="{{ old('number3') }}">
            @error('number3') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="weight3">Weight [3]:</label>
            <input type="number" step="0.01" name="weight3" id="weight3" value="{{ old('weight3') }}">
            @error('weight3') <span>{{ $message }}</span> @enderror
        </div>
        </div>
        <div id="form-rows">
            <div class="row" data-index="4">
        <div class="field">
            <label for="calico4">Calico [4]:</label>
            <input type="text" name="calico4" id="calico4" value="{{ old('calico4') }}">
            @error('calico4') <span>{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <label for="number4">Number [4]:</label>
            <input type="number" name="number4" id="number4" value="{{ old('number4') }}">
            @error('number4') <span>{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <label for="weight4">Weight [4]:</label>
            <input type="number" step="0.01" name="weight4" id="weight4" value="{{ old('weight4') }}">
            @error('weight4') <span>{{ $message }}</span> @enderror
        </div>
            </div>
        </div>
        <button type="button" class="add-row">+ Add Row</button>

        <!-- STA, MODEL, WORKSHOP, TARKEEB -->
        <div class="row" data-index="13">
        <div>
            <label for="sta">STATUS</label>
            <input type="text" name="sta" id="sta" value="{{ old('sta') }}">
            @error('sta') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="model">Model:</label>
            <input type="text" name="model" id="model" value="{{ old('model') }}">
            @error('model') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="workshop">Workshop:</label>
            <input type="number" step="0.01" name="workshop" id="workshop" value="{{ old('workshop') }}">
            @error('workshop') <span>{{ $message }}</span> @enderror
        </div>
        </div>

        <!-- GELA, BANUE, DATE -->
        <div class="row" data-index="14">
        <div>
            <label for="gela">Gela:</label>
            <input type="number" step="0.01" name="gela" id="gela" value="{{ old('gela') }}">
            @error('gela') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="banue">Banue:</label>
            <input type="number" step="0.01" name="banue" id="banue" value="{{ old('banue') }}">
            @error('banue') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="date">Date:</label>
            <input type="date" name="date" id="date" value="{{ old('date') }}">
            @error('date') <span>{{ $message }}</span> @enderror
        </div>
        </div>
        <button type="submit">Submit</button>
    </form>
    <script>
        document.querySelector('.add-row').addEventListener('click', function () {
            // Get the container holding all the rows
            const rowsContainer = document.getElementById('form-rows');
            // Get the current number of rows
            const rowCount = rowsContainer.children.length;
            // Calculate the new row index, starting from 4
            const newIndex = rowCount + 4;

            // Create the new row HTML
            const newRow = `
            <div class="row" data-index="${newIndex}">
                <div>
                    <label for="calico${newIndex}">Calico [${newIndex}]:</label>
                    <input type="text" name="calico${newIndex}" id="calico${newIndex}">
                </div>

                <div>
                    <label for="number${newIndex}">Number [${newIndex}]:</label>
                    <input type="number" name="number${newIndex}" id="number${newIndex}">
                </div>

                <div>
                    <label for="weight${newIndex}">Weight [${newIndex}]:</label>
                    <input type="number" step="0.01" name="weight${newIndex}" id="weight${newIndex}">
                </div>
            </div>`;

            // Append the new row to the rows container
            rowsContainer.insertAdjacentHTML('beforeend', newRow);
        });
            // Function to calculate workshop value based on weight1 * 14
    // document.getElementById('weight1').addEventListener('input', function () {
    //     const weight1Value = parseFloat(this.value);
    //     if (!isNaN(weight1Value)) {
    //         const workshopValue = weight1Value * 14;
    //         document.getElementById('workshop').value = workshopValue.toFixed(2); // Set workshop value
    //     } else {
    //         document.getElementById('workshop').value = ''; // Clear workshop if input is invalid
    //     }
    // });
    document.getElementById('kind').addEventListener('change', function () {
        const selectedKind = this.value;
        const modelField = document.getElementById('model');

        if (selectedKind === 'Pendent') {
            modelField.value = '01/';
        }
        else if (selectedKind === 'Bracelet') {
            modelField.value = '02/';
        }
        else if (selectedKind === 'Necklace') {
            modelField.value = '05/';
        }
        else if (selectedKind === 'Earing') {
            modelField.value = '04/';
        }
        else if (selectedKind === 'Ring') {
            modelField.value = '07/';
        }
        else if (selectedKind === 'Brooch') {
            modelField.value = '08/';
        }
        else if (selectedKind === 'Coupling') {
            modelField.value = '09/';
        }
        else if (selectedKind === 'Tie-Klip') {
            modelField.value = '014/';
        }
        else {
            modelField.value = ''; // Clear the model field for other kinds
        }
    });

    </script>
</body>
</html>
