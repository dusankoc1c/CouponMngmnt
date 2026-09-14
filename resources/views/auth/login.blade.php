<!DOCTYPE html>
<html>
<head>
    <title>Prijava</title>
    @vite(['resources/css/auth.css'])
</head>
<body>

<div class="auth-container">
    <h1>Prijava</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label>Lozinka</label>
        <input type="password" name="password">

        <button type="submit">Prijavi se</button>

        <a href="/" class="back-link"> Nazad na pocetnu -></a>
    </form>
</div>
</body>
</html>
