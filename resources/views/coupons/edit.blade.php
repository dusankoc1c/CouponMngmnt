<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izmena kupoa</title>
    @vite(['resources/css/auth.css'])
</head>
<body>

<div class="auth-container">
    <h1>Izmena kupona ({{ $coupon->code }})</h1>

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('coupon.update', $coupon) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Ime primaoca</label>
            <input type="text" name="receiver_name" value="{{ old('receiver_name', $coupon->receiver_name) }}">
        </div>

        <div class="form-group">
            <label>Email primaoca</label>
            <input type="email" name="receiver_email" value="{{ old('receiver_email', $coupon->receiver_email) }}">
        </div>

        <div class="form-group">
            <label>Iznos ($)</label>
            <input type="number" step="0.01" min="0" name="discount_amount" value="{{ old('discount_amount', $coupon->discount_amount) }}">
        </div>

        <div class="form-group">
            <label>Datum slanja</label>
            <input type="date" name="send_date" value="{{ old('send_date', $coupon->send_date?->format('Y-m-d')) }}">
        </div>

        <button type="submit">Sacuvaj</button>
    </form>
</div>

</body>
</html>
