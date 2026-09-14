<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $bundle->name }}</title>
    @vite(['resources/css/store.css'])
</head>
<body>

<div class="content">
    <a href="{{ route('store.show', $bundle->store) }}" class="back-link">&larr; PRODAVNICA </a>

    <div class="header">
        <div class="header-info">
            <h1>{{ $bundle->name }}</h1>
            <div class="total-value">Ukupna vrednost bundle-a: <strong>${{ number_format($bundle->getTotalValue(), 2) }}</strong></div>
        </div>

        <button type="button" class="btn-add" onclick="openModal('coupon-modal')">Dodaj Kupon</button>
    </div>

    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Receiver</th>
            <th>Email</th>
            <th>Amount</th>
            <th>Send Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($coupons as $coupon)
            <tr>
                <td>{{ $coupon->code }}</td>
                <td>{{ $coupon->receiver_name }}</td>
                <td>{{ $coupon->receiver_email }}</td>
                <td>${{ number_format($coupon->discount_amount, 2) }}</td>
                <td>{{ $coupon->send_date ? $coupon->send_date->format('m/d/Y') : 'Not set' }}</td>
                <td>
                    @if ($coupon->is_used)
                        <span class="badge-used">Iskoriscen</span>
                    @else
                        <span class="badge-unused">Nije iskorisen</span>
                    @endif
                </td>
                <td>
                    <div class="dropdown">
                        <button type="button" class="action-link" onclick="toggleDropdown(this)">Actions </button>
                        <div class="dropdown-menu">
                            <a href="{{ route('coupon.edit', $coupon) }}" class="dropdown-item">Edit</a>

                            <form method="POST" action="{{ route('coupon.toggle-used', $coupon) }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    {{ $coupon->is_used ? 'Oznaci kao neiskoriscen' : 'Oznaci kao iskoriscen' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('coupon.destroy', $coupon) }}" onsubmit="return confirm('Obrisi kupon?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item dropdown-item-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="empty-row">
                <td colspan="7">Ovaj bundle nema kupone</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="coupon-modal">
    <div class="modal-box">
        <h2>Novi kupon</h2>

        <form method="POST" action="{{ route('coupon.store', $bundle) }}">
            @csrf

            <div class="form-group">
                <label>Ime primaoca</label>
                <input type="text" name="receiver_name" required>
            </div>

            <div class="form-group">
                <label>Email primaoca</label>
                <input type="email" name="receiver_email" required>
            </div>

            <div class="form-group">
                <label>Iznos ($)</label>
                <input type="number" step="0.01" min="0" name="discount_amount" required>
            </div>

            <div class="form-group">
                <label>Datum slanja (opciono)</label>
                <input type="date" name="send_date">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel-modal" onclick="closeModal('coupon-modal')">Otkaži</button>
                <button type="submit" class="btn-submit-modal">Sačuvaj</button>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/addBundle.js'])

</body>
</html>
