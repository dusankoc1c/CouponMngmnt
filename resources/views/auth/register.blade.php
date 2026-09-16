<!DOCTYPE html>
<html>
<head>
    <title>Registracija</title>
    @vite(['resources/css/auth.css'])
</head>
<body>
<div class="auth-container">
    <h1>Registracija</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <input type="hidden" name="invite_id" value="{{ $invite->id }}">

        <div class="form-group">
            <label>Ime</label>
            <input type="text" name="name" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $invite->email }}" readonly>
        </div>

        <div class="form-group">
            <label>Lozinka</label>
            <input type="password" name="password">
        </div>

        <div class="form-group">
            <label>Potvrda lozinke</label>
            <input type="password" name="password_confirmation">
        </div>

        <button type="submit">Registruj se</button>
    </form>

</div>
</body>
</html>
