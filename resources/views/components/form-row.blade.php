<div class="row" data-index="{{ $index }}">
    <div>
        <label for="calico{{ $index }}">Calico [{{ $index }}]:</label>
        <input type="text" name="calico{{ $index }}" id="calico{{ $index }}" value="{{ old('calico' . $index) }}">
        @error('calico' . $index) <span>{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="number{{ $index }}">Number [{{ $index }}]:</label>
        <input type="number" name="number{{ $index }}" id="number{{ $index }}" value="{{ old('number' . $index) }}">
        @error('number' . $index) <span>{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="weight{{ $index }}">Weight [{{ $index }}]:</label>
        <input type="number" step="0.01" name="weight{{ $index }}" id="weight{{ $index }}" value="{{ old('weight' . $index) }}">
        @error('weight' . $index) <span>{{ $message }}</span> @enderror
    </div>
</div>
