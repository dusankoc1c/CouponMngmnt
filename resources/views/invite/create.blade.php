<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Pozovi korisnika</title>
    @vite(['resources/css/auth.css'])
</head>
<body>

<div class="auth-container">
    <h1>Pozovi korisnika</h1>

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('invite.store') }}">
        @csrf

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label>Value Limit</label>
            <input type="number" step="0.1" min="0" name="value_limit" value="{{ old('value_limit') }}">
        </div>

        <button type="submit">Posalji invite</button>
    </form>
</div>

</body>
</html>
