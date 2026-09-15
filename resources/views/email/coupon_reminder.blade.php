<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>

<h1>{{ $coupon->receiver_name }}</h1>

<p>Your coupon is still unused</p>

<p>Code: <strong>{{ $coupon->code }}</strong></p>
<p>Value: <strong>${{ number_format($coupon->discount_amount, 2) }}</strong></p>

@if ($daniDoIsteka >= 0)
    <p>You have {{ $daniDoIsteka }} {{ $daniDoIsteka === 1 ? 'day' : 'days' }} till deactivation.</p>
@endif

<p><a href="{{ $unsubscribeLink }}">Link to deactivate your email subscription</a></p>

</body>
</html>
