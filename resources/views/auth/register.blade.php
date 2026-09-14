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

        <label>Ime</label>
        <input type="text" name="name" value="{{ old('name') }}">

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label>Lozinka</label>
        <input type="password" name="password">

        <label>Potvrda lozinke</label>
        <input type="password" name="password_confirmation">

        <button type="submit">Registruj se</button>

        <a href="/" class="back-link"> Nazad na pocetnu -></a>
    </form>

</div>
</body>
</html>
