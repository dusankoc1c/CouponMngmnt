<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon Manager</title>
    @vite(['resources/css/welcome.css'])
</head>
<body>
<div class="container">

    <h1>HOME PAGE <br> strana za registraciju i login</h1>

    <p class="subtitle">
        Kreiranje bundla kupona za prodavnicu, vise kupona za jedan budnle
    </p>

    <div class="actions">
        <a href="{{ route('login') }}" class="btn btn-secondary">Prijava</a>
        <a href="{{ route('register') }}" class="btn btn-primary">Registruj se</a>
    </div>

</div>
</body>
</html>
