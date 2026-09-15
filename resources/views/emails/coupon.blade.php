<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<h1>Mail for coupon, {{ $coupon->receiver_name }}!</h1>

<p>Your coupon has arrived.</p>

<p>Code: <strong>{{ $coupon->code }}</strong></p>
<p>Value: <strong>${{ number_format($coupon->discount_amount, 2) }}</strong></p>

</body>
</html>
