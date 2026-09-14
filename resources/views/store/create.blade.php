
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj prodavnicu</title>
    @vite(['resources/css/createStore.css'])
</head>
<body>

<div class="container">
    <h1>Dodaj prodavnicu</h1>

    <form method="POST" action="{{ route('store.store') }}">
        @csrf

        <div class="form-group">
            <label>Naziv prodavnice</label>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Opis</label>
            <textarea name="description">{{ old('description') }}</textarea>
            @error('description')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="actions">
            <a href="{{ route('dashboard') }}" class="btn btn-cancel">Otkazi</a>
            <button type="submit" class="btn btn-submit">Sacuvaj</button>
        </div>
    </form>
</div>

</body>
</html>
