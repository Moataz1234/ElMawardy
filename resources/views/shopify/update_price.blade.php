<form action="/update-prices" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="excel_files[]" multiple required>
    <button type="submit">Upload</button>
</form>